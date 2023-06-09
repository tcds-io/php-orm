<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Connection\Pdo;

use PDO;
use PDOException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Pdo\NestedTransactionConnection;

class NestedTransactionConnectionTest extends TestCase
{
    private PDO&MockObject $write;

    private NestedTransactionConnection $connection;

    protected function setUp(): void
    {
        $read = $this->createMock(PDO::class);
        $this->write = $this->createMock(PDO::class);

        $this->connection = new NestedTransactionConnection($read, $this->write);
    }

    public function testWhenBeginThenCommitGetsCalledThenRunBeginAndCommitStatements(): void
    {
        $this->write->expects($this->exactly(2))->method('exec')
            ->withConsecutive(
                ['BEGIN'],
                ['COMMIT'],
            );

        $this->connection->begin();
        $this->connection->commit();
    }

    public function testWhenBeginAndCommitGetsCalledMultipleTimesThenRunStoreAndReleaseSavepoint(): void
    {
        $this->write->expects($this->exactly(6))->method('exec')
            ->withConsecutive(
                ['BEGIN'],
                ['SAVEPOINT LEVEL1'],
                ['SAVEPOINT LEVEL2'],
                ['RELEASE SAVEPOINT LEVEL2'],
                ['RELEASE SAVEPOINT LEVEL1'],
                ['COMMIT'],
            );

        $this->connection->begin();
        $this->connection->begin();
        $this->connection->begin();
        $this->connection->commit();
        $this->connection->commit();
        $this->connection->commit();
    }

    public function testWhenBeginThenRollbackGetsCalledThenRunBeginAndRollbackStatements(): void
    {
        $this->write->expects($this->exactly(2))->method('exec')
            ->withConsecutive(
                ['BEGIN'],
                ['ROLLBACK'],
            );

        $this->connection->begin();
        $this->connection->rollback();
    }

    public function testWhenBeginAndRollbackGetsCalledMultipleTimesThenRunStoreAndReleaseSavepoint(): void
    {
        $this->write->expects($this->exactly(6))->method('exec')
            ->withConsecutive(
                ['BEGIN'],
                ['SAVEPOINT LEVEL1'],
                ['SAVEPOINT LEVEL2'],
                ['RELEASE SAVEPOINT LEVEL2'],
                ['RELEASE SAVEPOINT LEVEL1'],
                ['ROLLBACK'],
            );

        $this->connection->begin();
        $this->connection->begin();
        $this->connection->begin();
        $this->connection->rollback();
        $this->connection->rollback();
        $this->connection->rollback();
    }

    public function testGivenNoTransactionThenWhenRollbackIsCalledThenThrowAnException(): void
    {
        $this->expectException(PDOException::class);
        $this->expectExceptionMessage('Rollback error: There is no transaction started');

        $this->connection->rollback();
    }
}
