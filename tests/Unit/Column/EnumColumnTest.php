<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Column;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Column\EnumColumn;
use Test\Tcds\Io\Orm\Fixtures\AddressType;
use Test\Tcds\Io\Orm\TestCase;

class EnumColumnTest extends TestCase
{
    private EnumColumn $column;

    protected function setUp(): void
    {
        $this->column = new EnumColumn(AddressType::class, 'type', fn(object $entry) => $entry->type);
    }

    #[Test] public function given_an_entry_then_get_its_plain_value(): void
    {
        $entry = (object) ['type' => AddressType::RESIDENCE];

        $plain = $this->column->plain($entry);

        $this->assertEquals('RESIDENCE', $plain);
    }

    #[Test] public function given_an_entry_when_value_is_null_then_get_null(): void
    {
        $entry = (object) ['type' => null];

        $plain = $this->column->plain($entry);

        $this->assertNull($plain);
    }

    #[Test] public function given_an_array_then_get_its_value(): void
    {
        $row = ['type' => 'RESIDENCE'];

        $value = $this->column->value($row);

        $this->assertEquals(AddressType::RESIDENCE, $value);
    }
}
