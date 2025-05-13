<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column;

use DateTime;
use DateTimeInterface;
use Override;

/**
 * @template Entry of object
 * @extends Column<Entry, DateTimeInterface, string>
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
        return new DateTime(parent::value($row));
    }
}
