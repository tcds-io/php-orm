<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column;

use Closure;

/**
 * @template Entry of object
 * @template Type
 */
abstract readonly class Column
{
    /**
     * @param Closure(Entry $record): Type $value
     */
    public function __construct(
        public string $name,
        public Closure $value,
    ) {
    }

    /**
     * @param Entry $entry
     * @return Type
     */
    public function valueOn($entry)
    {
        return ($this->value)($entry);
    }
}
