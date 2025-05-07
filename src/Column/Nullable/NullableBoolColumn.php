<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Column\Nullable;

use Tcds\Io\Orm\Column\Column;

/**
 * @template Entry of object
 * @extends Column<Entry, bool>
 */
readonly class NullableBoolColumn extends Column
{
}
