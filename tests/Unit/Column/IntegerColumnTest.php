<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Column;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Column\IntegerColumn;
use Test\Tcds\Io\Orm\TestCase;

class IntegerColumnTest extends TestCase
{
    private IntegerColumn $column;

    protected function setUp(): void
    {
        $this->column = new IntegerColumn('age', fn(object $entry) => $entry->age);
    }

    #[Test] public function given_an_entry_then_get_its_plain_value(): void
    {
        $entry = (object) ['age' => 36];

        $plain = $this->column->plain($entry);

        $this->assertEquals(36, $plain);
    }

    #[Test] public function given_an_array_then_get_its_value(): void
    {
        $row = ['age' => 36];

        $value = $this->column->value($row);

        $this->assertEquals(36, $value);
    }
}
