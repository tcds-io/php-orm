<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column;

use DateTimeImmutable;
use DateTimeInterface;
use Override;

/**
 * @template Entry of object
 * @extends Column<Entry, DateTimeInterface>
 */
readonly class DateTimeImmutableColumn extends Column
{
    #[Override] public function plain($entry): ?string
    {
        return parent::plain($entry)?->format(DateTimeInterface::ATOM);
    }

    #[Override] public function value(array $row): DateTimeImmutable
    {
        return new DateTimeImmutable(parent::value($row));
    }
}
