<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Connection\Pdo;

use Exception;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Pdo\GenericConnection;

class GenericConnectionTest extends TestCase
{
    private PDO&MockObject $pdo;
    private PDOStatement&MockObject $statement;

    private GenericConnection $connection;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->statement = $this->createMock(PDOStatement::class);

        $this->connection = new GenericConnection($this->pdo);
    }

    public function testGivenPdoThenConfigurePdo(): void
    {
        $pdo = $this->createMock(PDO::class);

        $pdo->expects($this->exactly(2))
            ->method('setAttribute')
            ->withConsecutive(
                [PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION],
                [PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC],
            );

        new GenericConnection($pdo);
    }

    public function testGivenTheQueryAndItsParamsWhenExecuteIsCalledThenRunPrepareAndExecuteInPdo(): void
    {
        $query = 'SELECT * FROM addresses WHERE id = :id';
        $params = [':id' => 'address-xxx'];

        $this->pdo
            ->expects($this->once())
            ->method('prepare')
            ->with($query)
            ->willReturn($this->statement);
        $this->statement
            ->expects($this->once())
            ->method('execute')
            ->with($params);

        $this->connection->execute($query, $params);
    }

    public function testGivenStatementWhenExecIsCalledThenRunExecInPdo(): void
    {
        $statement = 'DELETE FROM addresses WHERE id IS NULL';

        $this->pdo
            ->expects($this->once())
            ->method('exec')
            ->with($statement);

        $this->connection->exec($statement);
    }

    public function testBeginPdoTransaction(): void
    {
        $this->pdo
            ->expects($this->once())
            ->method('beginTransaction');

        $this->connection->begin();
    }

    public function testCommitPdoTransaction(): void
    {
        $this->pdo
            ->expects($this->once())
            ->method('commit');

        $this->connection->commit();
    }

    public function testRollbackPdoTransaction(): void
    {
        $this->pdo
            ->expects($this->once())
            ->method('rollBack');

        $this->connection->rollback();
    }

    public function testWhenTransactionFailsThenRollback(): void
    {
        $this->pdo->expects($this->once())->method('beginTransaction');
        $this->pdo->expects($this->once())->method('rollBack');

        $this->pdo->expects($this->never())->method('commit');

        $this->expectException(Exception::class);
        $this->connection->transaction(fn() => throw new Exception("Error"));
    }

    public function testWhenTransactionSucceedThenCommitAndReturnCallbackResponse(): void
    {
        $this->pdo->expects($this->once())->method('beginTransaction');
        $this->pdo->expects($this->once())->method('commit');

        $this->pdo->expects($this->never())->method('rollBack');

        $response = $this->connection->transaction(fn() => "success");

        $this->assertEquals("success", $response);
    }
}
