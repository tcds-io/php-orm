<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use Tcds\Io\Orm\Connection\Connection;

/**
 * @template T of object
 * @template FK of int|string
 * @extends RecordMapper<T>
 * @internal
 */
abstract class EntityRecordRepository extends RecordRepository
{
    /**
     * @param EntityRecordMapper<T> $entityMapper
     */
    public function __construct(
        Connection $connection,
        protected EntityRecordMapper $entityMapper,
    ) {
        parent::__construct($connection, $entityMapper);
    }

    /**
     * @param FK $id
     * @return T|null
     */
    public function loadById($id)
    {
        return $this->loadBy(['id' => $id]);
    }

    /**
     * @param T ...$entities
     */
    public function update(...$entities): void
    {
        foreach ($entities as $entity) {
            $this->updateWhere(
                $this->mapper->values($entity),
                ['id' => $this->entityMapper->primaryKey->valueOn($entity)],
            );
        }
    }

    /**
     * @param T ...$entities
     */
    public function delete(...$entities): void
    {
        foreach ($entities as $entity) {
            $this->deleteWhere(['id' => $this->entityMapper->primaryKey->valueOn($entity)]);
        }
    }
}
