<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\EntityTable;

final class AddressEntityTable extends EntityTable
{
    public function __construct(Connection $connection)
    {
        $this->column('street', fn(Address $entity) => $entity->street);

        parent::__construct($this->column('id', fn(Address $entity) => $entity->id), $connection);
    }

    public function name(): string
    {
        return "addresses";
    }

    /**
     * @param array<string, string|int|float|bool|null> $row
     */
    public function entry(array $row): Address
    {
        return new Address(
            id: $row['id'],
            street: $row['street'],
        );
    }
}
