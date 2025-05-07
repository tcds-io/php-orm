<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

use Tcds\Io\Orm\Column\Column;
use Tcds\Io\Orm\RecordMapper;

final class AddressRecordMapper extends RecordMapper
{
    protected Column $id;

    public function __construct()
    {
        $this->string("id", fn(Address $entity) => $entity->id);
        $this->string("street", fn(Address $entity) => $entity->street);
    }

    public function table(): string
    {
        return "addresses";
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
