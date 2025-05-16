<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Query\Conditions;

use Tcds\Io\Orm\Connection\Driver;

interface Condition
{
    /**
     * @return array{0: string, 1: list<mixed>}
     */
    public function build(Driver $driver): array;
}
