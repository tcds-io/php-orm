<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Query\Conditions;

use Override;
use Tcds\Io\Orm\Connection\Driver;

readonly class FilteringCondition implements Condition
{
    final public function __construct(
        protected string $comparator,
        /** @var array<mixed> */
        protected array $params = [],
    ) {
    }

    #[Override] public function build(Driver $driver): array
    {
        return [
            $this->comparator,
            $this->params,
        ];
    }
}
