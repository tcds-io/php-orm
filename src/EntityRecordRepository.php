<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\Query\Query;

/**
 * @template EntryType
 * @template PrimaryKeyType
 * @extends RecordRepository<EntryType>
 */
abstract class EntityRecordRepository extends RecordRepository
{
    public function __construct(
        /** @var EntityRecordMapper<EntryType, PrimaryKeyType> */
        protected EntityRecordMapper $entityMapper,
        Connection $connection,
        string $table,
    ) {
        parent::__construct($entityMapper, $connection, $table);
    }

    /**
     * @param PrimaryKeyType $id
     * @return EntryType|null
     */
    public function selectEntityById($id)
    {
        return $this->selectOneWhere(where(['id' => equalsTo($id)]));
    }

    /**
     * @param EntryType $entity
     */
    public function updateOne($entity): void
    {
        $this->updateWhere(
            $this->mapper->plain($entity),
            $this->equalsToPrimaryKey($entity),
        );
    }

    /**
     * @param EntryType ...$entities
     */
    public function updateMany(...$entities): void
    {
        foreach ($entities as $entity) {
            $this->updateOne($entity);
        }
    }

    /**
     * @param EntryType $entity
     */
    public function deleteOne($entity): void
    {
        $this->deleteWhere(
            $this->equalsToPrimaryKey($entity),
        );
    }

    /**
     * @param EntryType ...$entities
     */
    public function deleteMany(...$entities): void
    {
        foreach ($entities as $entity) {
            $this->deleteOne($entity);
        }
    }

    /**
     * @param EntryType $entity
     */
    private function equalsToPrimaryKey($entity): Query
    {
        return where([
            'id' => equalsTo($this->entityMapper->primaryKey->plain($entity)),
        ]);
    }
}
