<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Query;

use Tcds\Io\Orm\Connection\Driver;
use Tcds\Io\Orm\Query\Conditions\ConditionList;
use Tcds\Io\Orm\Query\Conditions\FieldCondition;
use Tcds\Io\Orm\Query\Conditions\FilteringCondition;
use Tcds\Io\Orm\Query\Conditions\GroupedCondition;

readonly class Query
{
    private function __construct(
        private ConditionList $conditions,
    ) {
    }

    public static function where(string $field, FilteringCondition $condition): self
    {
        $conditions = new ConditionList();
        $conditions->add(Operator::WHERE, new FieldCondition($field, $condition));

        return new self($conditions);
    }

    public static function empty(): self
    {
        $conditions = new ConditionList();

        return new self($conditions);
    }

    public function field(string $field, FilteringCondition $condition): self
    {
        $this->conditions->add(Operator::NONE, new FieldCondition($field, $condition));

        return $this;
    }

    public function and(string $field, FilteringCondition $condition): self
    {
        $this->conditions->add(Operator::AND, new FieldCondition($field, $condition));

        return $this;
    }

    public function or(string $field, FilteringCondition $condition): self
    {
        $this->conditions->add(Operator::OR, new FieldCondition($field, $condition));

        return $this;
    }

    /**
     * @param callable(Query $query): Query $inner
     * @return self
     */
    public function andGrouped(callable $inner): self
    {
        $query = $inner(Query::empty());
        $this->conditions->add(Operator::AND, new GroupedCondition($query));

        return $this;
    }

    /**
     * @param callable(Query $query): Query $inner
     * @return self
     */
    public function orGrouped(callable $inner): self
    {
        $query = $inner(Query::empty());
        $this->conditions->add(Operator::OR, new GroupedCondition($query));

        return $this;
    }

    /**
     * @return array{
     *     0: string,
     *     1: list<mixed>
     * }
     */
    public function build(Driver $driver): array
    {
        $statements = [];
        $bindings = [];

        foreach ($this->conditions->items() as $item) {
            [$operator, $condition] = $item;
            [$statement, $params] = $condition->build($driver);

            $statements[] = trim("$operator->value $statement");
            array_push($bindings, ...$params);
        }

        return [
            join(' ', $statements),
            $bindings,
        ];
    }
}
