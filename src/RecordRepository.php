<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use PDO;
use Tcds\Io\Orm\Column\Column;
use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\Query\Query;
use Traversable;

/**
 * @template EntryType
 */
abstract class RecordRepository
{
    public function __construct(
        /** @var RecordMapper<EntryType> */
        protected readonly RecordMapper $mapper,
        protected readonly Connection $connection,
        protected readonly string $table,
    ) {
    }

    /**
     * @param EntryType $entry
     */
    public function insertOne($entry): void
    {
        $sql = "INSERT INTO $this->table ({$this->mapper->names()}) VALUES ({$this->bindings()})";

        $values = $this->mapper->plain($entry);

        $this->connection->write($sql, $values);
    }

    /**
     * @return EntryType|null
     */
    public function selectOneWhere(Query $where)
    {
        [$whereColumnsString, $whereBindings] = $where->build($this->connection->driver());
        $sql = trim("SELECT * FROM {$this->wrap($this->table)} $whereColumnsString LIMIT 1");
        $items = $this->connection->read($sql, $whereBindings);

        /** @var array<string, mixed> $item */
        $item = $items->fetch(PDO::FETCH_ASSOC);

        return $item ? $this->mapper->map($item) : null;
    }

    /**
     * @param array<string, mixed> $bindings
     * @return EntryType|null
     */
    public function selectOneByQuery(string $selectQuery, array $bindings)
    {
        $items = $this->connection->read($selectQuery, $bindings);

        /** @var array<string, mixed> $item */
        $item = $items->fetch(PDO::FETCH_ASSOC);

        return $item ? $this->mapper->map($item) : null;
    }

    /**
     * @return Traversable<EntryType>
     */
    public function selectManyWhere(?Query $where = null, ?int $limit = null, ?int $offset = null): Traversable
    {
        [$whereColumnsString, $whereBindings] = $where?->build($this->connection->driver()) ?? ['', []];
        $limitOffset = $this->prepareLimitOffset($limit, $offset);
        $sql = trim("SELECT * FROM {$this->wrap($this->table)} $whereColumnsString$limitOffset");
        $items = $this->connection->read($sql, $whereBindings);

        while ($item = $items->fetch(PDO::FETCH_ASSOC)) {
            /** @var array<string, mixed> $item */
            yield $this->mapper->map($item);
        }
    }

    /**
     * @param array<string, mixed> $bindings
     * @return Traversable<EntryType>
     */
    public function selectManyByQuery(string $selectQuery, array $bindings): Traversable
    {
        $items = $this->connection->read($selectQuery, $bindings);

        while ($item = $items->fetch(PDO::FETCH_ASSOC)) {
            /** @var array<string, mixed> $item */
            yield $this->mapper->map($item);
        }
    }

    public function existsWhere(Query $query): bool
    {
        return $this->selectOneWhere($query) !== null;
    }

    public function deleteWhere(Query $where): void
    {
        [$whereColumnsString, $whereBindings] = $where->build($this->connection->driver());
        $sql = trim("DELETE FROM {$this->wrap($this->table)} $whereColumnsString");

        $this->connection->write($sql, $whereBindings);
    }

    /**
     * @param array<string, mixed> $values
     */
    public function updateWhere(array $values, Query $where): void
    {
        $columnBindings = [];
        $valuesBinding = [];

        foreach ($values as $column => $value) {
            $columnBindings[] = "{$this->wrap($column)} = ?";
            $valuesBinding[] = $value;
        }

        $columnBindingsString = join(", ", $columnBindings);

        [$whereColumnsString, $whereBindings] = $where->build($this->connection->driver());
        $sql = trim("UPDATE {$this->wrap($this->table)} SET $columnBindingsString $whereColumnsString");

        $this->connection->write($sql, array_merge($valuesBinding, $whereBindings));
    }

    private function bindings(): string
    {
        return join(', ', array_map(fn(Column $column) => ":$column->name", $this->mapper->columns));
    }

    private function prepareLimitOffset(?int $limit, ?int $offset): string
    {
        return ($limit ? " LIMIT $limit" : '') . ($offset ? " OFFSET $offset" : '');
    }

    private function wrap(string $column): string
    {
        return $this->connection->driver()->wrap($column);
    }
}
