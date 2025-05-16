<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Functions;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Connection\Driver;
use Test\Tcds\Io\Orm\TestCase;

class IsNullTest extends TestCase
{
    #[Test] public function condition(): void
    {
        $condition = isNull();

        [$query, $params] = $condition->build(Driver::MYSQL);

        $this->assertEquals('IS NULL', $query);
        $this->assertEquals([], $params);
    }
}
