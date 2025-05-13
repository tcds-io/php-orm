<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column;

use Closure;

/**
 * @template Entry
 * @template Value
 */
abstract readonly class Column
{
    /**
     * @param Closure(Entry $record): Value $value
     */
    public function __construct(
        public string $name,
        public Closure $value,
    ) {
    }

    /**
     * @param Entry $entry
     * @return Value|null
     */
    public function plain($entry)
    {
        return ($this->value)($entry);
    }

    /**
     * @param array<string, mixed> $row
     * @return Value
     */
    public function value(array $row)
    {
        return $row[$this->name];
    }

    /**
     * @param array<string, mixed> $row
     * @return Value|null
     */
    public function nullable(array $row)
    {
        return ($row[$this->name] ?? null)
            ? static::value($row)
            : null;
    }
}
