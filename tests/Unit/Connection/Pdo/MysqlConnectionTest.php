<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Connection\Pdo;

use PDO;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Pdo\MysqlConnection;

class MysqlConnectionTest extends TestCase
{
    public function testGivenPdoThenConfigurePdoWithMysqlInitCommand(): void
    {
        $read = $this->createMock(PDO::class);
        $write = $this->createMock(PDO::class);

        $read->expects($this->exactly(3))
            ->method('setAttribute')
            ->withConsecutive(
                [PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8'],
                [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION],
                [PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC],
            );

        $write->expects($this->exactly(3))
            ->method('setAttribute')
            ->withConsecutive(
                [PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8'],
                [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION],
                [PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC],
            );

        new MysqlConnection($read, $write);
    }
}
