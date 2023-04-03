<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Connection\Pdo;

use PDO;

class MysqlConnection extends NestedTransactionConnection
{
    public function __construct(PDO $read, PDO $write)
    {
        $read->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
        $write->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');

        parent::__construct($read, $write);
    }
}
