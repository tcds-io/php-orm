<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Functions;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Connection\Driver;
use Test\Tcds\Io\Orm\TestCase;

class NotLikeTest extends TestCase
{
    #[Test] public function condition(): void
    {
        $condition = notLike('Berlin');

        [$query, $params] = $condition->build(Driver::MYSQL);

        $this->assertEquals('NOT LIKE ?', $query);
        $this->assertEquals(['Berlin'], $params);
    }
}
