<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

use DateTime;
use Override;
use Tcds\Io\Orm\EntityRecordMapper;

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

    #[Override] public function map(array $row): User
    {
        return new User(
            id: $row['id'],
            name: $row['name'],
            dateOfBirth: new DateTime($row['date_of_birth']),
            address: lazyOf(Address::class, fn() => $this->addressRepository->loadById($row['address_id'])),
        );
    }
}
