<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column;

use Override;

/**
 * @template Entry of object
 * @extends Column<Entry, bool>
 */
readonly class BoolColumn extends Column
{
    #[Override] public function plain($entry): bool
    {
        return parent::plain($entry);
    }

    #[Override] public function value(array $row): bool
    {
        return filter_var(parent::value($row), FILTER_VALIDATE_BOOLEAN);
    }
}
