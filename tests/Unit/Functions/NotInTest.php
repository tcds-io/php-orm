<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Functions;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Connection\Driver;
use Test\Tcds\Io\Orm\TestCase;

class NotInTest extends TestCase
{
    #[Test] public function condition(): void
    {
        $condition = notIn([10, 20, 30]);

        [$query, $params] = $condition->build(Driver::MYSQL);

        $this->assertEquals('NOT IN (?,?,?)', $query);
        $this->assertEquals([10, 20, 30], $params);
    }
}
