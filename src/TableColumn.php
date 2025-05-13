<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use BackedEnum;
use Closure;
use DateTimeInterface;
use Tcds\Io\Orm\Column\BoolColumn;
use Tcds\Io\Orm\Column\DateColumn;
use Tcds\Io\Orm\Column\DateTimeColumn;
use Tcds\Io\Orm\Column\DateTimeImmutableColumn;
use Tcds\Io\Orm\Column\EnumColumn;
use Tcds\Io\Orm\Column\FloatColumn;
use Tcds\Io\Orm\Column\IntegerColumn;
use Tcds\Io\Orm\Column\StringColumn;

/**
 * @template T
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
     * @return FloatColumn<T>
     */
    protected function numeric(string $name, Closure $value): FloatColumn
    {
        $column = new FloatColumn($name, $value);
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

    /**
     * @param Closure(T $entry): DateTimeInterface $value
     * @return DateColumn<T>
     */
    protected function date(string $name, Closure $value): DateColumn
    {
        $column = new DateColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(T $entry): DateTimeInterface $value
     * @return DateTimeColumn<T>
     */
    protected function datetime(string $name, Closure $value): DateTimeColumn
    {
        $column = new DateTimeColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(T $entry): DateTimeInterface $value
     * @return DateTimeImmutableColumn<T>
     */
    protected function datetimeImmutable(string $name, Closure $value): DateTimeImmutableColumn
    {
        $column = new DateTimeImmutableColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @template E of BackedEnum
     * @param class-string<E> $class
     * @param Closure(T $entry): DateTimeInterface $value
     * @return EnumColumn<T>
     */
    protected function enum(string $class, string $name, Closure $value): EnumColumn
    {
        $column = new EnumColumn($class, $name, $value);
        $this->columns[] = $column;

        return $column;
    }
}
