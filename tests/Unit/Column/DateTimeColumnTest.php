<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Column;

use DateTime;
use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Column\DateTimeColumn;
use Test\Tcds\Io\Orm\TestCase;

class DateTimeColumnTest extends TestCase
{
    private DateTimeColumn $column;

    protected function setUp(): void
    {
        $this->column = new DateTimeColumn('created_at', fn(object $entry) => $entry->created_at);
    }

    #[Test] public function given_an_entry_then_get_its_plain_value(): void
    {
        $entry = (object) ['created_at' => new DateTime('2025-05-08T18:46:20')];

        $plain = $this->column->plain($entry);

        $this->assertEquals('2025-05-08T18:46:20+00:00', $plain);
    }

    #[Test] public function given_an_entry_when_value_is_null_then_get_null(): void
    {
        $entry = (object) ['created_at' => null];

        $plain = $this->column->plain($entry);

        $this->assertNull($plain);
    }

    #[Test] public function given_an_array_then_get_its_value(): void
    {
        $row = ['created_at' => '2025-05-08T18:46:20'];

        $value = $this->column->value($row);

        $this->assertEquals(DateTime::class, $value::class);
        $this->assertEquals(new DateTime('2025-05-08T18:46:20'), $value);
    }
}
