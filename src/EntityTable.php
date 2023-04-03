<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use Tcds\Io\Orm\Column\Column;
use Tcds\Io\Orm\Connection\Connection;
use Throwable;

/**
 * @template T of object
 * @extends Table<T>
 */
abstract class EntityTable extends Table
{
    private Column $primaryKey;

    public function __construct(Column $primaryKey, Connection $connection)
    {
        $this->primaryKey = $primaryKey;

        parent::__construct($connection);
    }

    /**
     * @return T|null
     * @throws Throwable
     */
    public function loadById(int|string $id): ?object
    {
        return $this->loadBy(['id' => $id]);
    }

    /**
     * @param T ...$entities
     */
    public function update(object ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->updateWhere(
                array_reduce(
                    $this->columns,
                    fn($current, Column $column) => array_merge($current, [$column->name => ($column->value)($entity)]),
                    [],
                ),
                ['id' => ($this->primaryKey->value)($entity)],
            );
        }
    }

    /**
     * @param T ...$entities
     */
    public function delete(object ...$entities): void
    {
        foreach ($entities as $entity) {
            $this->deleteWhere(['id' => ($this->primaryKey->value)($entity)]);
        }
    }
}
