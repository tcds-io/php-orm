# PHP ORM for PHP 8.4

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

A lightweight, type-safe Object-Relational Mapper (ORM) for modern PHP (8.4+), designed to be expressive and easy to
extend. It emphasizes strict typing and modern PHP features while providing a flexible mapping interface between
database rows and PHP objects.

## 🚀 Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require tcds-io/orm
```

## 🧠 Features

- Typed mapping of database rows to PHP objects
- Support for Records and Entities
- Fine-grained control over columns (e.g. enums, dates, nullable values)
- Lazy-loading support
- Extendable repository base classes
- Injectable mappers
- PHP 8.4+ only, leveraging modern language features

## 📦 Usage

There are two main types of mappers:

- `RecordMapper<Type>` — for mapping simple, immutable data records
- `EntityRecordMapper<Type, PrimaryKeyType>` — for mapping richer domain entities, with support for repositories or
  lazy-loading

These mappers are designed to be injected wherever data transformation is needed, such as in services, repositories, or
controllers.

### Record Mapper Example

```php
/**
 * @extends RecordMapper<Address>
 */
final class AddressMapper extends RecordMapper
{
    private IntegerColumn $id;
    private StringColumn $street;
    private FloatColumn $number;
    private IntegerColumn $floor;
    private BoolColumn $active;
    private EnumColumn $type;
    private DateTimeColumn $createdAt;
    private DateTimeImmutableColumn $deletedAt;

    public function __construct()
    {
        $this->id = $this->integer('id', fn(Address $entry) => $entry->id);
        $this->street = $this->string('street', fn(Address $entity) => $entity->street);
        $this->number = $this->numeric('number', fn(Address $entity) => $entity->number);
        $this->floor = $this->integer('floor', fn(Address $entity) => $entity->floor);
        $this->active = $this->boolean('active', fn(Address $entity) => $entity->active);
        $this->type = $this->enum(AddressType::class, 'type', fn(Address $entity) => $entity->type);
        $this->createdAt = $this->datetime('created_at', fn(Address $entity) => $entity->createdAt);
        $this->deletedAt = $this->datetimeImmutable('deleted_at', fn(Address $entity) => $entity->deletedAt);
    }

    public function map(array $row): Address
    {
        return new Address(
            id: $this->id->value($row),
            street: $this->street->value($row),
            number: $this->number->value($row),
            floor: $this->floor->value($row),
            active: $this->active->value($row),
            type: $this->type->value($row),
            createdAt: $this->createdAt->value($row),
            deletedAt: $this->deletedAt->nullable($row),
        );
    }
}
```

### Entity Mapper Example

```php
/**
 * @extends EntityRecordMapper<User, int>
 */
final class UserMapper extends EntityRecordMapper
{
    /** @var LazyBuffer<string, Address> */
    private LazyBuffer $addressLoader;

    public function __construct(
        private readonly AddressRepository $addressRepository,
    ) {
        parent::__construct($this->string('id', fn(User $entity) => $entity->id));

        $this->string('name', fn(User $entity) => $entity->name);
        $this->date('date_of_birth', fn(User $entity) => $entity->dateOfBirth);
        $this->integer('address_id', fn(User $entity) => $entity->address->id);

        $this->addressLoader = lazyBufferOf(Address::class, function (array $ids) {
            return listOf($this->addressRepository->loadAllByIds($ids))
                ->indexedBy(fn(Address $address) => $address->id)
                ->entries();
        });
    }

    #[Override] public function map(array $row): User
    {
        return new User(
            id: $row['id'],
            name: $row['name'],
            dateOfBirth: new DateTime($row['date_of_birth']),
            address: $this->addressLoader->lazyOf($row['address_id']),
        );
    }
}
```

### Nullable Support

For nullable fields, use the `->nullable(...)` method on column definitions. This allows you to gracefully handle `NULL`
values in your database rows.

### Foreign keys and objects

The ORM does not resolve foreign keys and objects automatically.
Instead, you must inject the object repository and load the object as needed:

```php
return new User(
    ...,
    /** lazy load foreign object */
    address: lazyOf(Address::class, fn() => $addressRepository->loadById($row['address_id'])),
    /** eager load foreign object */
    address: $addressRepository->loadById($row['address_id']),
);
```

### Lazy loading

Records can be lazy-loaded with the `lazyOf` function, which receives an initializer and loads the entry only when any of its properties are accessed:

```php
/** lazy object */
$address = lazyOf(
    /** The class to be loaded */
    Address::class,
    /** The object initializer */
    fn() => $addressRepository->loadById($addressId),
);

/** loaded object */
$street = $address->street;
```

### Solving N+1 problems

N+1 can be solved with the `lazyBufferOf` function, which manages buffered and loaded records.
All buffered records are loaded at once when any of the entries are accessed,
and all previously loaded records are returned immediately without additional loader calls.

```php
$addressLoader = lazyBufferOf(
    /** The class to be loaded */
    Address::class,
    /** The object list loader */
    function (array $bufferedIds) {
        listOf($this->addressRepository->loadAllByIds($bufferedIds))
          ->indexedBy(fn(Address $address) => $address->id)
          ->entries();
    },
);

/** lazy object */
$address = $addressLoader->lazyOf($addressId);

/** loaded object */
$street = $address->street;
```

## 🗃️ Repositories

This library also provides base repository classes that you can extend to perform actual database operations.

### Record Repository Example

```php
/**
 * @extends RecordRepository<Address>
 */
class AddressRepository extends RecordRepository
{
    public function __construct(Connection $connection, RecordMapper $mapper)
    {
        parent::__construct($mapper, $connection, 'addresses');
    }

    public function loadById(int $id): Address
    {
        return $this->selectOneWhere(['id' => $id]) ?? throw new Exception('Address not found');
    }
}
```

### Entity Repository Example

```php
/**
 * @extends EntityRecordRepository<User, string>
 */
class UserRepository extends EntityRecordRepository
{
    public function __construct(Connection $connection, UserMapper $mapper)
    {
        parent::__construct($mapper, $connection, 'users');
    }

    public function loadById(string $id): User
    {
        return $this->selectEntityById($id) ?? throw new Exception('User not found');
    }
}
```

### 🔧 Repository Capabilities

📘 `RecordRepository` Provides core operations for working with raw records:

```php
insertOne($entry)
selectOneWhere(where: ['id' => 10])
selectOneByQuery(selectQuery: 'SELECT * FROM table where id = :id', bindings: ['id' => 10])
selectManyWhere([where: 'deleted_at' => null], limit: 10, offset: 100)
selectManyByQuery(selectQuery: 'SELECT * FROM table where deleted_at is null', bindings: [])
existsWhere(where: ['id' => 10])
deleteWhere(where: ['id' => 10])
updateWhere(values: ['name' => 'Arthur Dent', 'date_of_birth' => '1990-01-01'], where: ['id' => 10])
```

📙 `EntityRecordRepository` extends RecordRepository with additional features for managing entity lifecycles:

```php
selectEntityById(id: 10)
updateOne(entity: $user)
updateMany($user1, $user2, $user3, ...)
deleteOne(entity: $user)
deleteMany($user1, $user2, $user3, ...)
```

## 🤝 Contributing

Contributions are welcome! If you have ideas, find a bug, or want to improve the library, feel free to:

- Fork the repo
- Create a new branch
- Submit a pull request

Please follow PSR-12 coding standards and ensure tests pass before submitting changes.

## 🚀 Next steps

- Query builder
- Extend where comparisons

## 📄 License

This project is open-sourced under the [MIT license](LICENSE).

---

Happy Mapping! 🎉
