<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use BackedEnum;
use Closure;
use DateTimeInterface;
use Tcds\Io\Orm\Column\BoolColumn;
use Tcds\Io\Orm\Column\Column;
use Tcds\Io\Orm\Column\DateColumn;
use Tcds\Io\Orm\Column\DateTimeColumn;
use Tcds\Io\Orm\Column\DateTimeImmutableColumn;
use Tcds\Io\Orm\Column\EnumColumn;
use Tcds\Io\Orm\Column\FloatColumn;
use Tcds\Io\Orm\Column\IntegerColumn;
use Tcds\Io\Orm\Column\StringColumn;

/**
 * @template EntryType
 */
abstract class TableColumn
{
    /** @var list<Column<EntryType, mixed>> */
    public private(set) array $columns = [];

    /**
     * @param Closure(EntryType $entry): string $value
     * @return StringColumn<EntryType>
     */
    protected function string(string $name, Closure $value): StringColumn
    {
        $column = new StringColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(EntryType $entry): bool $value
     * @return BoolColumn<EntryType>
     */
    protected function boolean(string $name, Closure $value): BoolColumn
    {
        $column = new BoolColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(EntryType $entry): float $value
     * @return FloatColumn<EntryType>
     */
    protected function numeric(string $name, Closure $value): FloatColumn
    {
        $column = new FloatColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(EntryType $entry): int $value
     * @return IntegerColumn<EntryType>
     */
    protected function integer(string $name, Closure $value): IntegerColumn
    {
        $column = new IntegerColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(EntryType $entry): DateTimeInterface $value
     * @return DateColumn<EntryType>
     */
    protected function date(string $name, Closure $value): DateColumn
    {
        $column = new DateColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(EntryType $entry): DateTimeInterface $value
     * @return DateTimeColumn<EntryType>
     */
    protected function datetime(string $name, Closure $value): DateTimeColumn
    {
        $column = new DateTimeColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @param Closure(EntryType $entry): DateTimeInterface $value
     * @return DateTimeImmutableColumn<EntryType>
     */
    protected function datetimeImmutable(string $name, Closure $value): DateTimeImmutableColumn
    {
        $column = new DateTimeImmutableColumn($name, $value);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * @template EnumType of BackedEnum
     * @param class-string<EnumType> $class
     * @param Closure(EntryType $entry): EnumType $value
     * @return EnumColumn<EntryType, EnumType>
     */
    protected function enum(string $class, string $name, Closure $value): EnumColumn
    {
        $column = new EnumColumn($class, $name, $value);
        $this->columns[] = $column;

        return $column;
    }
}
