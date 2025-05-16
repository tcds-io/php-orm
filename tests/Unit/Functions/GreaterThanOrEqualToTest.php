<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Functions;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Connection\Driver;
use Tcds\Io\Orm\Query\Conditions\GreaterThanOrEqualTo;
use Test\Tcds\Io\Orm\TestCase;

class GreaterThanOrEqualToTest extends TestCase
{
    #[Test] public function condition(): void
    {
        $condition = greaterThanOrEqualTo(55);

        [$query, $params] = $condition->build(Driver::MYSQL);

        $this->assertEquals('>= ?', $query);
        $this->assertEquals([55], $params);
    }
}
