<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PHPUnit\Framework\Attributes\Test;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressMapper;
use Test\Tcds\Io\Orm\TestCase;

class RecordMapperTest extends TestCase
{
    private AddressMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new AddressMapper();
    }

    #[Test] public function get_plain_array_from_first_address(): void
    {
        $object = Address::first();

        $plain = $this->mapper->plain($object);

        $this->assertSame(Address::firstRowData(), $plain);
    }

    #[Test] public function get_plain_array_from_second_address(): void
    {
        $object = Address::second();

        $plain = $this->mapper->plain($object);

        $this->assertSame(Address::secondRowData(), $plain);
    }

    #[Test] public function map_first_address(): void
    {
        $data = Address::firstRowData();

        $object = $this->mapper->map($data);

        $this->assertEquals(Address::first(), $object);
    }

    #[Test] public function map_second_address(): void
    {
        $data = Address::secondRowData();

        $object = $this->mapper->map($data);

        $this->assertEquals(Address::second(), $object);
    }
}
