<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Query\Conditions;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Connection\Driver;
use Tcds\Io\Orm\Query\Conditions\FieldCondition;
use Test\Tcds\Io\Orm\TestCase;

class FieldConditionTest extends TestCase
{
    #[Test] public function build_condition(): void
    {
        $condition = new FieldCondition('name', equalsTo('Arthur'));

        [$query, $params] = $condition->build(Driver::MYSQL);

        $this->assertEquals('`name` = ?', $query);
        $this->assertEquals(['Arthur'], $params);
    }
}
