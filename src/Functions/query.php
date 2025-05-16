<?php

declare(strict_types=1);

use Tcds\Io\Orm\Query\Conditions\FilteringCondition;
use Tcds\Io\Orm\Query\Query;

/**
 * @param array<string, FilteringCondition> $where
 * @return Query
 */
function where(array $where): Query
{
    $query = null;

    foreach ($where as $column => $condition) {
        $query === null
            ? $query = Query::where($column, $condition)
            : $query->and($column, $condition);
    }

    return $query;
}

function between(mixed $first, mixed $last): FilteringCondition
{
    return new FilteringCondition('BETWEEN ? AND ?', [$first, $last]);
}

function differentOf(mixed $value): FilteringCondition
{
    return new FilteringCondition('!= ?', [$value]);
}

function equalsTo(mixed $value): FilteringCondition
{
    return new FilteringCondition('= ?', [$value]);
}

function greaterThan(mixed $value): FilteringCondition
{
    return new FilteringCondition('> ?', [$value]);
}

function greaterThanOrEqualTo(mixed $value): FilteringCondition
{
    return new FilteringCondition('>= ?', [$value]);
}

function in(array $values): FilteringCondition
{
    $marks = join(',', array_fill(0, count($values), '?'));

    return new FilteringCondition("IN ($marks)", $values);
}

function isNotNull(): FilteringCondition
{
    return new FilteringCondition('IS NOT NULL', []);
}

function isNull(): FilteringCondition
{
    return new FilteringCondition('IS NULL', []);
}

function like(mixed $value): FilteringCondition
{
    return new FilteringCondition('LIKE ?', [$value]);
}

function notIn(array $values): FilteringCondition
{
    $marks = join(',', array_fill(0, count($values), '?'));

    return new FilteringCondition("NOT IN ($marks)", $values);
}

function notLike(mixed $value): FilteringCondition
{
    return new FilteringCondition('NOT LIKE ?', [$value]);
}

function smallerThan(mixed $value): FilteringCondition
{
    return new FilteringCondition('< ?', [$value]);
}

function smallerThanOrEqualTo(mixed $value): FilteringCondition
{
    return new FilteringCondition('<= ?', [$value]);
}
