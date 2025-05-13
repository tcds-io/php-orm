<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

use Tcds\Io\Orm\Column\BoolColumn;
use Tcds\Io\Orm\Column\DateTimeColumn;
use Tcds\Io\Orm\Column\DateTimeImmutableColumn;
use Tcds\Io\Orm\Column\EnumColumn;
use Tcds\Io\Orm\Column\FloatColumn;
use Tcds\Io\Orm\Column\IntegerColumn;
use Tcds\Io\Orm\Column\StringColumn;
use Tcds\Io\Orm\RecordMapper;

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

    /**
     * @param array<string, mixed> $row
     */
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
