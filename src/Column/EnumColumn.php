<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column;

use BackedEnum;
use Closure;
use Override;

/**
 * @template EntryType
 * @template ValueType of BackedEnum
 * @extends Column<EntryType, ValueType>
 */
readonly class EnumColumn extends Column
{
    /**
     * @param class-string<ValueType> $class
     */
    public function __construct(
        private string $class,
        string $name,
        Closure $value,
    ) {
        parent::__construct($name, $value);
    }

    #[Override] public function plain($entry): string|int|null
    {
        return parent::plain($entry)?->value;
    }

    #[Override] public function value(array $row)
    {
        /** @var string $value */
        $value = parent::value($row);

        return $this->class::from($value);
    }
}
