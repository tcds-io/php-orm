<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Connection;

enum ConnectionDriver
{
    case GENERIC;
    case MYSQL;
}
