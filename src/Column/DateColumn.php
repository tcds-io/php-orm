<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column;

use DateTime;
use DateTimeInterface;
use Override;

/**
 * @template EntryType
 * @extends Column<EntryType, DateTimeInterface>
 */
readonly class DateColumn extends Column
{
    public const string FORMAT = 'Y-m-d';

    #[Override] public function plain($entry): ?string
    {
        return parent::plain($entry)?->format(self::FORMAT);
    }

    #[Override] public function value(array $row): DateTime
    {
        /** @var string $value */
        $value = parent::value($row);

        return new DateTime($value);
    }
}
