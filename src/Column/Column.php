<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column;

use Closure;

abstract class Column
{
    public readonly string $name;
    public readonly Closure $value;

    public function __construct(string $name, Closure $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
}
