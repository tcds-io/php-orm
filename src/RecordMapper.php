<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use Tcds\Io\Orm\Column\Column;

/**
 * @template T
 * @extends RecordRepository<T>
 */
abstract class RecordMapper
{
    /** @use TableColumn<T> */
    use TableColumn;

    /** @var list<Column<T, mixed>> */
    public private(set) array $columns = [];

    /**
     * @param array<string, mixed> $row
     * @return T
     */
    abstract public function map(array $row);

    /**
     * @param T $entry
     * @return array<string, mixed>
     */
    public function plain($entry): array
    {
        $entries = array_map(
            fn(Column $column) => [$column->name => $column->plain($entry)],
            $this->columns,
        );

        return array_merge(...$entries);
    }

    public function names(): string
    {
        return join(', ', array_map(fn(Column $column) => $column->name, $this->columns));
    }
}
