<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column;

use BackedEnum;
use Closure;
use Override;

/**
 * @template Entry of object
 * @template Enum of class-string
 * @extends Column<Entry, BackedEnum>
 */
readonly class EnumColumn extends Column
{
    /**
     * @param class-string<Enum> $class
     */
    public function __construct(
        private string $class,
        string $name,
        Closure $value,
    ) {
        parent::__construct($name, $value);
    }

    #[Override] public function plain($entry): ?string
    {
        return parent::plain($entry)?->value;
    }

    /**
     * @param array $row
     * @return Enum
     */
    #[Override] public function value(array $row)
    {
        return $this->class::from(parent::value($row));
    }
}
