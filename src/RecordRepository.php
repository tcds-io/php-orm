<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use PDO;
use Tcds\Io\Orm\Column\Column;
use Tcds\Io\Orm\Connection\Connection;
use Traversable;

/**
 * @template T of object
 * @internal
 */
abstract class RecordRepository
{
    abstract protected string $table { get; }

    public function __construct(
        protected Connection $connection,
        protected RecordMapper $mapper,
    ) {
    }

    /**
     * @param T $entry
     */
    public function insert($entry): void
    {
        $sql = "INSERT INTO $this->table ({$this->mapper->names()}) VALUES ({$this->bindings()})";

        $values = $this->mapper->values($entry);

        $this->connection->write($sql, $values);
    }

    /**
     * @param array<string, mixed> $where
     * @return T|null
     */
    public function loadBy(array $where)
    {
        [$whereColumnsString, $whereBindings] = $this->prepareWhere($where);
        $sql = trim("SELECT * FROM $this->table$whereColumnsString LIMIT 1");
        $items = $this->connection->read($sql, $whereBindings);

        /** @var array<string, mixed> $item */
        $item = $items->fetch(PDO::FETCH_ASSOC);

        return $item ? $this->mapper->entry($item) : null;
    }

    /**
     * @param array<string, mixed> $bindings
     * @return T|null
     */
    public function loadByQuery(string $selectQuery, array $bindings)
    {
        $items = $this->connection->read($selectQuery, $bindings);

        /** @var array<string, mixed> $item */
        $item = $items->fetch(PDO::FETCH_ASSOC);

        return $item ? $this->mapper->entry($item) : null;
    }

    /**
     * @param array<string, mixed> $where
     * @return Traversable<T>
     */
    public function listBy(array $where = [], ?int $limit = null, ?int $offset = null): Traversable
    {
        [$whereColumnsString, $whereBindings] = $this->prepareWhere($where);
        $limitOffset = $this->prepareLimitOffset($limit, $offset);
        $sql = trim("SELECT * FROM $this->table$whereColumnsString$limitOffset");
        $items = $this->connection->read($sql, $whereBindings);

        while ($item = $items->fetch(PDO::FETCH_ASSOC)) {
            /** @var array<string, mixed> $item */
            yield $this->mapper->entry($item);
        }
    }

    /**
     * @param array<string, mixed> $bindings
     * @return Traversable<T>
     */
    public function listByQuery(string $selectQuery, array $bindings): Traversable
    {
        $items = $this->connection->read($selectQuery, $bindings);

        while ($item = $items->fetch(PDO::FETCH_ASSOC)) {
            /** @var array<string, mixed> $item */
            yield $this->mapper->entry($item);
        }
    }

    /**
     * @param array<string, mixed> $where
     */
    public function exists(array $where): bool
    {
        return $this->loadBy($where) !== null;
    }

    /**
     * @param array<string, mixed> $where
     */
    public function deleteWhere(array $where): void
    {
        [$whereColumnsString, $whereBindings] = $this->prepareWhere($where);
        $sql = trim("DELETE FROM $this->table$whereColumnsString");

        $this->connection->write($sql, $whereBindings);
    }

    /**
     * @param array<string, mixed> $values
     * @param array<string, mixed> $where
     */
    public function updateWhere(array $values, array $where): void
    {
        $columnBindings = [];
        $valuesBinding = [];

        foreach ($values as $column => $value) {
            $columnBindings[] = "$column = :$column";
            $valuesBinding[$column] = $value;
        }

        $columnBindingsString = join(", ", $columnBindings);

        [$whereColumnsString, $whereBindings] = $this->prepareWhere($where);
        $sql = trim("UPDATE $this->table SET $columnBindingsString$whereColumnsString");

        $this->connection->write($sql, array_merge($valuesBinding, $whereBindings));
    }

    private function bindings(): string
    {
        return join(', ', array_map(fn(Column $column) => ":$column->name", $this->mapper->columns));
    }

    /**
     * @param array<string, mixed> $where
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function prepareWhere(array $where): array
    {
        $whereColumns = [];
        $whereBindings = [];

        foreach ($where as $column => $value) {
            $whereColumns[] = "$column = :$column";
            $whereBindings[$column] = $value;
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
