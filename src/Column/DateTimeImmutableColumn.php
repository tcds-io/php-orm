<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column;

use DateTimeImmutable;
use DateTimeInterface;
use Override;

/**
 * @template EntryType
 * @extends Column<EntryType, DateTimeInterface>
 */
readonly class DateTimeImmutableColumn extends Column
{
    #[Override] public function plain($entry): ?string
    {
        return parent::plain($entry)?->format(DateTimeInterface::ATOM);
    }

    #[Override] public function value(array $row): DateTimeImmutable
    {
        /** @var string $value */
        $value = parent::value($row);

        return new DateTimeImmutable($value);
    }
}
