<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Query;

enum Operator: string
{
    case NONE = "";
    case WHERE = "WHERE";
    case AND = "AND";
    case OR = "OR";
}
