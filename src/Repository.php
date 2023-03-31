<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use PDO;
use Tcds\Io\Orm\Column\Column;
use Tcds\Io\Orm\Column\ColumnFactory;
use Tcds\Io\Orm\Connection\Connection;
use Throwable;
use Traversable;

/**
 * @template T of object
 */
abstract class Repository
{
    use ColumnFactory;

    /** @var array<Column> */
    protected array $columns = [];
    protected Connection $connection;

    abstract public function name(): string;

    /**
     * @param array<string, string|int|bool|float|null> $row
     * @return T
     */
    abstract public function entry(array $row);

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param T $entry
     */
    public function insert(object $entry): void
    {
        $sql = <<<SQL
            INSERT INTO {$this->name()} ({$this->columnNames()})
                VALUES ({$this->columnBindings()})
        SQL;

        $values = array_reduce(
            $this->columns,
            fn($current, Column $column) => array_merge($current, [":$column->name" => ($column->value)($entry)]),
            [],
        );

        $this->connection->execute($sql, $values);
    }

    /**
     * @param array<string, string|int|bool|float|null> $where
     * @return T|null
     * @throws Throwable
     */
    public function loadBy(array $where): ?object
    {
        [$whereColumnsString, $whereBindings] = $this->prepareWhere($where);
        $sql = trim("SELECT * FROM {$this->name()}$whereColumnsString LIMIT 1");
        $items = $this->connection->execute($sql, $whereBindings);

        /** @var array<string, string|int|bool|float|null> $item */
        $item = $items->fetch(PDO::FETCH_ASSOC);

        return $item ? $this->entry($item) : null;
    }

    /**
     * @param array<string, string|int|bool|float|null> $bindings
     * @return T|null
     * @throws Throwable
     */
    public function loadByQuery(string $selectQuery, array $bindings): ?object
    {
        $items = $this->connection->execute($selectQuery, $bindings);

        /** @var array<string, string|int|bool|float|null> $item */
        $item = $items->fetch(PDO::FETCH_ASSOC);

        return $item ? $this->entry($item) : null;
    }

    /**
     * @param array<string, string|int|bool|float|null> $where
     * @return Traversable<T>
     */
    public function findBy(array $where, ?int $limit = null, ?int $offset = null): Traversable
    {
        [$whereColumnsString, $whereBindings] = $this->prepareWhere($where);
        $limitOffset = $this->prepareLimitOffset($limit, $offset);
        $sql = trim("SELECT * FROM {$this->name()}{$whereColumnsString}{$limitOffset}");
        $items = $this->connection->execute($sql, $whereBindings);

        while ($item = $items->fetch(PDO::FETCH_ASSOC)) {
            /** @var array<string, string|int|bool|float|null> $item */
            yield $this->entry($item);
        }
    }

    /**
     * @param array<string, string|int|bool|float|null> $bindings
     * @return Traversable<T>
     */
    public function findByQuery(string $selectQuery, array $bindings): Traversable
    {
        $items = $this->connection->execute($selectQuery, $bindings);

        while ($item = $items->fetch(PDO::FETCH_ASSOC)) {
            /** @var array<string, string|int|bool|float|null> $item */
            yield $this->entry($item);
        }
    }

    /**
     * @param array<string, string|int|bool|float|null> $where
     */
    public function deleteWhere(array $where): void
    {
        [$whereColumnsString, $whereBindings] = $this->prepareWhere($where);
        $sql = trim("DELETE FROM {$this->name()}$whereColumnsString");

        $this->connection->execute($sql, $whereBindings);
    }

    /**
     * @param array<string, string|int|bool|float|null> $where
     * @throws Throwable
     */
    public function exists(array $where): bool
    {
        return $this->loadBy($where) !== null;
    }

    /**
     * @param array<string, string|int|bool|float|null> $values
     * @param array<string, string|int|bool|float|null> $where
     */
    public function updateWhere(array $values, array $where): void
    {
        $columnBindings = [];
        $valuesBinding = [];

        foreach ($values as $column => $value) {
            $key = ":$column";
            $columnBindings[] = "$column = $key";
            $valuesBinding[$key] = $value;
        }

        $columnBindingsString = join(", ", $columnBindings);

        [$whereColumnsString, $whereBindings] = $this->prepareWhere($where);
        $sql = trim("UPDATE {$this->name()} SET {$columnBindingsString}{$whereColumnsString}");

        $this->connection->execute($sql, array_merge($valuesBinding, $whereBindings));
    }

    private function columnNames(): string
    {
        return join(', ', array_map(fn(Column $column) => $column->name, $this->columns));
    }

    private function columnBindings(): string
    {
        return join(', ', array_map(fn(Column $column) => ":$column->name", $this->columns));
    }

    /**
     * @param array<string, string|int|bool|float|null> $where
     * @return array{0: string, 1: array<string, string|int|bool|float|null>}
     */
    private function prepareWhere(array $where): array
    {
        $whereColumns = [];
        $whereBindings = [];

        foreach ($where as $column => $value) {
            $binding = ":$column";
            $whereColumns[] = "$column = $binding";
            $whereBindings[$binding] = $value;
        }

        return [
            empty($whereColumns) ? '' : sprintf(" WHERE %s", join(' AND ', $whereColumns)),
            $whereBindings,
        ];
    }

    private function prepareLimitOffset(?int $limit, ?int $offset): string
    {
        return ($limit ? " LIMIT $limit" : '') . ($offset ? " OFFSET $offset" : '');
    }
}
