<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use Closure;
use Tcds\Io\Orm\Column\Nullable\NullableBoolColumn;
use Tcds\Io\Orm\Column\Nullable\NullableIntegerColumn;
use Tcds\Io\Orm\Column\Nullable\NullableNumericColumn;
use Tcds\Io\Orm\Column\Nullable\NullableStringColumn;

/**
 * @template T of object
 */
trait TableColumnNullable
{
    /**
     * @param Closure(T $entry): string $value
     * @return NullableStringColumn<T>
     */
    protected function nullableString(string $name, Closure $value): NullableStringColumn
    {
        $column = new NullableStringColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(T $entry): bool $value
     * @return NullableBoolColumn<T>
     */
    protected function nullableBoolean(string $name, Closure $value): NullableBoolColumn
    {
        $column = new NullableBoolColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(T $entry): numeric $value
     * @return NullableNumericColumn<T>
     */
    protected function nullableNumeric(string $name, Closure $value): NullableNumericColumn
    {
        $column = new NullableNumericColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(T $entry): int $value
     * @return NullableIntegerColumn<T>
     */
    protected function nullableInteger(string $name, Closure $value): NullableIntegerColumn
    {
        $column = new NullableIntegerColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }
}
