<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

use DateTime;
use DateTimeImmutable;

readonly class Address
{
    public function __construct(
        public int $id,
        public string $street,
        public float $number,
        public int $floor,
        public bool $active,
        public AddressType $type,
        public DateTime $createdAt,
        public ?DateTimeImmutable $deletedAt,
    ) {
    }

    public static function first(): self
    {
        return new self(
            id: 1,
            street: 'First Avenue',
            number: 145.45,
            floor: 1,
            active: true,
            type: AddressType::RESIDENCE,
            createdAt: new DateTime('2025-05-01 10:15:20'),
            deletedAt: new DateTimeImmutable('2025-05-10 11:16:30'),
        );
    }

    public static function second(): self
    {
        return new self(
            id: 2,
            street: 'Second Avenue',
            number: 34.90,
            floor: 5,
            active: false,
            type: AddressType::WORK,
            createdAt: new DateTime('2025-05-02 20:25:30'),
            deletedAt: null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function firstRowData(): array
    {
        return [
            'id' => 1,
            'street' => 'First Avenue',
            'number' => 145.45,
            'floor' => 1,
            'active' => true,
            'type' => 'RESIDENCE',
            'created_at' => '2025-05-01T10:15:20+00:00',
            'deleted_at' => '2025-05-10T11:16:30+00:00',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function secondRowData(): array
    {
        return [
            'id' => 2,
            'street' => 'Second Avenue',
            'number' => 34.90,
            'floor' => 5,
            'active' => false,
            'type' => 'WORK',
            'created_at' => '2025-05-02T20:25:30+00:00',
            'deleted_at' => null,
        ];
    }
}
