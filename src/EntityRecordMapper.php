<?php

declare(strict_types=1);

namespace Tcds\Io\Orm;

use Tcds\Io\Orm\Column\Column;

/**
 * @template T
 * @template FK of int|string
 * @extends RecordMapper<T>
 */
abstract class EntityRecordMapper extends RecordMapper
{
    /** @var Column<T, FK> */
    public readonly Column $primaryKey;

    /**
     * @param Column<T, FK> $primaryKey
     */
    public function __construct(Column $primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }
}
