<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Column;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Column\BoolColumn;
use Test\Tcds\Io\Orm\TestCase;

class BoolColumnTest extends TestCase
{
    private BoolColumn $column;

    protected function setUp(): void
    {
        $this->column = new BoolColumn('active', fn(object $entry) => $entry->active);
    }

    #[Test] public function given_an_entry_then_get_its_plain_value(): void
    {
        $this->assertTrue($this->column->plain((object) ['active' => true]));
        $this->assertFalse($this->column->plain((object) ['active' => false]));
    }

    #[Test] public function given_an_array_then_get_its_value(): void
    {
        $this->assertTrue($this->column->value(['active' => true]));
        $this->assertFalse($this->column->value(['active' => false]));

        $this->assertTrue($this->column->value(['active' => 'true']));
        $this->assertFalse($this->column->value(['active' => 'false']));

        $this->assertTrue($this->column->value(['active' => 1]));
        $this->assertFalse($this->column->value(['active' => 0]));

        $this->assertTrue($this->column->value(['active' => '1']));
        $this->assertFalse($this->column->value(['active' => '0']));
    }
}
