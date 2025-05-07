<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Connection\Pdo;

use Exception;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use Tcds\Io\Orm\Connection\ConnectionDriver;
use Tcds\Io\Orm\Connection\Pdo\GenericConnection;
use Test\Tcds\Io\Orm\TestCase;

class GenericConnectionTest extends TestCase
{
    private PDO&MockObject $read;
    private PDO&MockObject $write;
    private PDOStatement&MockObject $statement;

    private GenericConnection $connection;

    protected function setUp(): void
    {
        $this->read = $this->createMock(PDO::class);
        $this->write = $this->createMock(PDO::class);
        $this->statement = $this->createMock(PDOStatement::class);

        $this->connection = new class ($this->read, $this->write) extends GenericConnection
        {
            public function driver(): ConnectionDriver
            {
                return ConnectionDriver::GENERIC;
            }
        };
    }

    public function testGivenPdoThenConfigurePdo(): void
    {
        $this->expectToSetupPdo($this->read);
        $this->expectToSetupPdo($this->write);

        new class ($this->read, $this->write) extends GenericConnection
        {
            public function driver(): ConnectionDriver
            {
                return ConnectionDriver::GENERIC;
            }
        };
    }

    public function testGivenTheQueryAndItsParamsWhenExecuteIsCalledThenRunPrepareAndExecuteInPdo(): void
    {
        $query = 'SELECT * FROM addresses WHERE id = :id';
        $params = [':id' => 'address-xxx'];

        $this->read
            ->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->willReturn($this->statement);
        $this->statement
            ->expects($this->once())
            ->method('execute')
            ->with($params);

        $this->connection->read($query, $params);
    }

    public function testGivenStatementWhenExecIsCalledThenRunExecInPdo(): void
    {
        $deleteStatement = 'DELETE FROM addresses WHERE id IS NULL';

        $this->write
            ->expects($this->once())
            ->method('prepare')
            ->with($deleteStatement)
            ->willReturn($this->statement);
        $this->statement
            ->expects($this->once())
            ->method('execute')
            ->with([]);

        $this->connection->write($deleteStatement);
    }

    public function testBeginPdoTransaction(): void
    {
        $this->write
            ->expects($this->once())
            ->method('beginTransaction');

        $this->connection->begin();
    }

    public function testCommitPdoTransaction(): void
    {
        $this->write
            ->expects($this->once())
            ->method('commit');

        $this->connection->commit();
    }

    public function testRollbackPdoTransaction(): void
    {
        $this->write
            ->expects($this->once())
            ->method('rollBack');

        $this->connection->rollback();
    }

    public function testWhenTransactionFailsThenRollback(): void
    {
        $this->write->expects($this->once())->method('beginTransaction');
        $this->write->expects($this->once())->method('rollBack');

        $this->write->expects($this->never())->method('commit');

        $this->expectException(Exception::class);
        $this->connection->transaction(fn() => throw new Exception("Error"));
    }

    public function testWhenTransactionSucceedThenCommitAndReturnCallbackResponse(): void
    {
        $this->write->expects($this->once())->method('beginTransaction');
        $this->write->expects($this->once())->method('commit');

        $this->write->expects($this->never())->method('rollBack');

        $response = $this->connection->transaction(fn() => "success");

        $this->assertEquals("success", $response);
    }

    private function expectToSetupPdo(PDO&MockObject $pdo): void
    {
        $matcher = $this->exactly(2);

        $pdo
            ->expects($matcher)
            ->method('setAttribute')
            ->with($this->consecutive(
                matcher: $matcher,
                first: [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION],
                second: [PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC],
            ));
    }
}
