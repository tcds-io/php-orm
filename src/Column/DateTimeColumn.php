<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column;

use DateTime;
use DateTimeInterface;
use Override;

/**
 * @template Entry of object
 * @extends Column<Entry, DateTimeInterface>
 */
readonly class DateTimeColumn extends Column
{
    #[Override] public function plain($entry)
    {
        return parent::plain($entry)?->format(DateTimeInterface::ATOM);
    }

    #[Override] public function value(array $row): DateTime
    {
        return new DateTime(parent::value($row));
    }
}
