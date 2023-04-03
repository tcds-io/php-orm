<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column;

use Closure;

trait TableColumn
{
    protected function column(string $name, Closure $value): Column
    {
        $column = new MultiTypeColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }
}
