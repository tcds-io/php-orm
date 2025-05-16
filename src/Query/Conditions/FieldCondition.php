<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Query\Conditions;

use Tcds\Io\Orm\Connection\Driver;

readonly class FieldCondition implements Condition
{
    public function __construct(
        private string $column,
        private FilteringCondition $condition,
    ) {
    }

    public function build(Driver $driver): array
    {
        $column = $driver->wrap($this->column);
        [$query, $params] = $this->condition->build($driver);

        return ["$column $query", $params];
    }
}
