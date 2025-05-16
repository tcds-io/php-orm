<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Query\Conditions;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Query\Conditions\ConditionList;
use Tcds\Io\Orm\Query\Operator;
use Test\Tcds\Io\Orm\TestCase;

class FieldConditionListTest extends TestCase
{
    #[Test] public function when_condition_is_empty_then_add_with_where_operator(): void
    {
        $conditions = new ConditionList();

        $conditions->add(Operator::AND, equalsTo(22));

        $this->assertEquals([
            [Operator::AND, equalsTo(22)],
        ], $conditions->items());
    }

    #[Test] public function when_condition_is_not_empty_then_add_with_given_operator(): void
    {
        $conditions = new ConditionList();

        $conditions->add(Operator::WHERE, equalsTo(22));
        $conditions->add(Operator::AND, equalsTo(33));

        $this->assertEquals([
            [Operator::WHERE, equalsTo(22)],
            [Operator::AND, equalsTo(33)],
        ], $conditions->items());
    }
}
