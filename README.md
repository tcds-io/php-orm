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
- PHP 8.4+ only, leveraging modern language features

## 📦 Usage

There are two main types of mappers:

- `RecordMapper<Type>` — for mapping simple, immutable data records
- `EntityRecordMapper<Type, PrimaryKeyType>` — for mapping richer domain entities, with support for repositories or
  lazy-loading

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
    public function __construct(
        private readonly AddressRepository $addressRepository,
    ) {
        parent::__construct($this->string('id', fn(User $entity) => $entity->id));

        $this->string('name', fn(User $entity) => $entity->name);
        $this->date('date_of_birth', fn(User $entity) => $entity->dateOfBirth);
        $this->integer('address_id', fn(User $entity) => $entity->address->id);
    }

    public function map(array $row): User
    {
        return new User(
            id: $row['id'],
            name: $row['name'],
            dateOfBirth: new DateTime($row['date_of_birth']),
            address: lazyOf(Address::class, fn() => $this->addressRepository->loadById($row['address_id'])),
        );
    }
}
```

### Nullable Support

For nullable fields, use the `->nullable(...)` method on column definitions. This allows you to gracefully handle `NULL`
values in your database rows.

## 🤝 Contributing

Contributions are welcome! If you have ideas, find a bug, or want to improve the library, feel free to:

- Fork the repo
- Create a new branch
- Submit a pull request

Please follow PSR-12 coding standards and ensure tests pass before submitting changes.

## 📄 License

This project is open-sourced under the [MIT license](./LICENSE).

---

Happy Mapping! 🎉
