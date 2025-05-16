<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Query\Conditions;

use Tcds\Io\Generic\MutableArrayList;
use Tcds\Io\Orm\Query\Operator;

/**
 * @extends MutableArrayList<array{0: Operator, 1: Condition}>
 */
class ConditionList extends MutableArrayList
{
    public function __construct()
    {
        parent::__construct([]);
    }

    public function add(Operator $operator, Condition $condition): void
    {
        $this->push([$operator, $condition]);
    }
}
