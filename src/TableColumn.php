<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use Closure;
use Tcds\Io\Orm\Column\BoolColumn;
use Tcds\Io\Orm\Column\IntegerColumn;
use Tcds\Io\Orm\Column\NumericColumn;
use Tcds\Io\Orm\Column\StringColumn;

/**
 * @template T of object
 */
trait TableColumn
{
    /**
     * @param Closure(T $entry): string $value
     * @return StringColumn<T>
     */
    protected function string(string $name, Closure $value): StringColumn
    {
        $column = new StringColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(T $entry): bool $value
     * @return BoolColumn<T>
     */
    protected function boolean(string $name, Closure $value): BoolColumn
    {
        $column = new BoolColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(T $entry): numeric $value
     * @return NumericColumn<T>
     */
    protected function numeric(string $name, Closure $value): NumericColumn
    {
        $column = new NumericColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(T $entry): int $value
     * @return IntegerColumn<T>
     */
    protected function integer(string $name, Closure $value): IntegerColumn
    {
        $column = new IntegerColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }
}
