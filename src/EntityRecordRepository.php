<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use Tcds\Io\Orm\Connection\Connection;

/**
 * @template T
 * @template FK
 * @extends RecordMapper<T>
 * @internal
 */
abstract class EntityRecordRepository extends RecordRepository
{
    /**
     * @param EntityRecordMapper<T> $entityMapper
     */
    public function __construct(
        protected EntityRecordMapper $entityMapper,
        Connection $connection,
        string $table,
    ) {
        parent::__construct($entityMapper, $connection, $table);
    }

    /**
     * @param FK $id
     * @return T|null
     */
    public function selectEntityById($id)
    {
        return $this->selectOneWhere(['id' => $id]);
    }

    /**
     * @param T $entity
     */
    public function updateOne($entity): void
    {
        $this->updateWhere(
            $this->mapper->plain($entity),
            ['id' => $this->entityMapper->primaryKey->plain($entity)],
        );
    }

    /**
     * @param T ...$entities
     */
    public function updateMany(...$entities): void
    {
        foreach ($entities as $entity) {
            $this->updateOne($entity);
        }
    }

    /**
     * @param T $entity
     */
    public function deleteOne($entity): void
    {
        $this->deleteWhere(['id' => $this->entityMapper->primaryKey->plain($entity)]);
    }

    /**
     * @param T ...$entities
     */
    public function deleteMany(...$entities): void
    {
        foreach ($entities as $entity) {
            $this->deleteOne($entity);
        }
    }
}
