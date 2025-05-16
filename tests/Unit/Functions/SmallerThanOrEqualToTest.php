<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Functions;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Connection\Driver;
use Test\Tcds\Io\Orm\TestCase;

class SmallerThanOrEqualToTest extends TestCase
{
    #[Test] public function condition(): void
    {
        $condition = smallerThanOrEqualTo(15);

        [$query, $params] = $condition->build(Driver::MYSQL);

        $this->assertEquals('<= ?', $query);
        $this->assertEquals([15], $params);
    }
}
