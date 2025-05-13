<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Column;

use Override;
use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Column\Column;
use Test\Tcds\Io\Orm\TestCase;

class ColumnTest extends TestCase
{
    /** @var Column */
    private $column;

    protected function setUp(): void
    {
        $this->column = new readonly class ('prop', fn(object $entry) => $entry->prop) extends Column
        {
            #[Override] public function value(array $row): string
            {
                return parent::value($row) . '-' . parent::value($row);
            }
        };
    }

    #[Test] public function given_row_when_prop_is_not_null_then_return_value(): void
    {
        $this->assertEquals('bar-bar', $this->column->nullable(['prop' => 'bar']));
    }

    #[Test] public function given_row_when_prop_is_null_then_return_null(): void
    {
        $this->assertNull($this->column->nullable(['prop' => null]));
    }

    #[Test] public function given_row_when_prop_is_unset_null_then_return_null(): void
    {
        $this->assertNull($this->column->nullable([]));
    }
}
