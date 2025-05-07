<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

readonly class Address
{
    public function __construct(
        public string $id,
        public string $street,
        public float $number,
        public int $floor,
        public bool $active,
    ) {
    }

    public static function first(): self
    {
        return new self(
            id: 'address-1',
            street: 'First Avenue',
            number: 145.45,
            floor: 1,
            active: true,
        );
    }

    public static function second(): self
    {
        return new self(
            id: 'address-2',
            street: 'Second Avenue',
            number: 34.90,
            floor: 5,
            active: false,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function firstRowData(): array
    {
        return [
            'id' => 'address-1',
            'street' => 'First Avenue',
            'number' => 145.45,
            'floor' => 1,
            'active' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function secondRowData(): array
    {
        return [
            'id' => 'address-2',
            'street' => 'Second Avenue',
            'number' => 34.90,
            'floor' => 5,
            'active' => false,
        ];
    }
}
