<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Connection;

enum Driver
{
    case GENERIC;
    case MYSQL;

    public function wrap(string $column): string
    {
        return match ($this) {
            Driver::MYSQL => "`$column`",
            Driver::GENERIC => "$column",
        };
    }
}
