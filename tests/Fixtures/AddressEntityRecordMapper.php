<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

use Tcds\Io\Orm\EntityRecordMapper;

/**
 * @extends EntityRecordMapper<Address, string>
 */
final class AddressEntityRecordMapper extends EntityRecordMapper
{
    public function __construct()
    {
        parent::__construct($this->string('id', fn(Address $entity) => $entity->id));

        $this->string('street', fn(Address $entity) => $entity->street);
        $this->numeric('number', fn(Address $entity) => $entity->number);
        $this->integer('floor', fn(Address $entity) => $entity->floor);
        $this->boolean('active', fn(Address $entity) => $entity->active);
    }

    /**
     * @param array<string, mixed> $row
     */
    public function entry(array $row): Address
    {
        return new Address(
            id: $row['id'],
            street: $row['street'],
            number: $row['number'],
            floor: $row['floor'],
            active: $row['active'],
        );
    }
}
