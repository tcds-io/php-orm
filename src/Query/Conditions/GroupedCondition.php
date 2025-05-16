<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Query\Conditions;

use Override;
use Tcds\Io\Orm\Connection\Driver;
use Tcds\Io\Orm\Query\Query;

readonly class GroupedCondition implements Condition
{
    public function __construct(private Query $query)
    {
    }

    #[Override] public function build(Driver $driver): array
    {
        [$query, $params] = $this->query->build($driver);

        return [
            "($query)",
            $params,
        ];
    }
}
