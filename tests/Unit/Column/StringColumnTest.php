<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Column;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Column\StringColumn;
use Test\Tcds\Io\Orm\TestCase;

class StringColumnTest extends TestCase
{
    private StringColumn $column;

    protected function setUp(): void
    {
        $this->column = new StringColumn('name', fn(object $entry) => $entry->name);
    }

    #[Test] public function given_an_entry_then_get_its_plain_value(): void
    {
        $entry = (object) ['name' => 'Arthur Dent'];

        $plain = $this->column->plain($entry);

        $this->assertEquals('Arthur Dent', $plain);
    }

    #[Test] public function given_an_array_then_get_its_value(): void
    {
        $row = ['name' => 'Arthur Dent'];

        $value = $this->column->value($row);

        $this->assertEquals('Arthur Dent', $value);
    }
}
