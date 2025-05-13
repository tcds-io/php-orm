<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Column;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Column\FloatColumn;
use Test\Tcds\Io\Orm\TestCase;

class FloatColumnTest extends TestCase
{
    private FloatColumn $column;

    protected function setUp(): void
    {
        $this->column = new FloatColumn('height', fn(object $entry) => $entry->height);
    }

    #[Test] public function given_an_entry_then_get_its_plain_value(): void
    {
        $entry = (object) ['height' => 1.76];

        $plain = $this->column->plain($entry);

        $this->assertEquals(1.76, $plain);
    }

    #[Test] public function given_an_array_then_get_its_value(): void
    {
        $row = ['height' => 1.76];

        $value = $this->column->value($row);

        $this->assertEquals(1.76, $value);
    }
}
