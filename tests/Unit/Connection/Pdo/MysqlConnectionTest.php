<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Connection\Pdo;

use PDO;
use PHPUnit\Framework\MockObject\MockObject;
use Tcds\Io\Orm\Connection\Pdo\MysqlConnection;
use Test\Tcds\Io\Orm\TestCase;

class MysqlConnectionTest extends TestCase
{
    public function testGivenPdoThenConfigurePdoWithMysqlInitCommand(): void
    {
        $read = $this->createMock(PDO::class);
        $write = $this->createMock(PDO::class);

        $this->expectToSetupPdo($read);
        $this->expectToSetupPdo($write);

        new MysqlConnection($read, $write);
    }

    private function expectToSetupPdo(PDO&MockObject $pdo): void
    {
        $matcher = $this->exactly(3);

        $pdo
            ->expects($matcher)
            ->method('setAttribute')
            ->with($this->consecutive(
                matcher: $matcher,
                first: [PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8'],
                second: [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION],
                third: [PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC],
            ));
    }
}
