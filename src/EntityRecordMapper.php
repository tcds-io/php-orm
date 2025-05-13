<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use Tcds\Io\Orm\Column\Column;

/**
 * @template EntryType
 * @template ForeignKeyType
 * @extends RecordMapper<EntryType>
 */
abstract class EntityRecordMapper extends RecordMapper
{
    /** @var Column<EntryType, ForeignKeyType> */
    public readonly Column $primaryKey;

    /**
     * @param Column<EntryType, ForeignKeyType> $primaryKey
     */
    public function __construct(Column $primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }
}
