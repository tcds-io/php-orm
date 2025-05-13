<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

enum AddressType: string
{
    case RESIDENCE = 'RESIDENCE';
    case WORK = 'WORK';
}
