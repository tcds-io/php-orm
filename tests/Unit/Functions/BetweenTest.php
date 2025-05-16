<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Functions;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Connection\Driver;
use Test\Tcds\Io\Orm\TestCase;

class BetweenTest extends TestCase
{
    #[Test] public function condition(): void
    {
        $condition = between(18, 55);

        [$query, $params] = $condition->build(Driver::MYSQL);

        $this->assertEquals('BETWEEN ? AND ?', $query);
        $this->assertEquals([18, 55], $params);
    }
}
