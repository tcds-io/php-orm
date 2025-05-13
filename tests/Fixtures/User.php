<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

use DateTime;

readonly class User
{
    public function __construct(
        public int $id,
        public string $name,
        public DateTime $dateOfBirth,
        public Address $address,
    ) {
    }

    public static function first(): self
    {
        return new self(
            id: 1,
            name: 'First User',
            dateOfBirth: new DateTime('2020-01-01'),
            address: Address::first(),
        );
    }

    public static function second(): self
    {
        return new self(
            id: 2,
            name: 'Second User',
            dateOfBirth: new DateTime('2022-10-15'),
            address: Address::second(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function firstData(): array
    {
        return [
            'id' => 1,
            'name' => 'First User',
            'date_of_birth' => '2020-01-01',
            'address_id' => 1,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function secondData(): array
    {
        return [
            'id' => 2,
            'name' => 'Second User',
            'date_of_birth' => '2022-10-15',
            'address_id' => 2,
        ];
    }
}
