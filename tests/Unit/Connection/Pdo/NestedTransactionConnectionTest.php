<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Connection\Pdo;

use PDO;
use PDOException;
use PHPUnit\Framework\MockObject\MockObject;
use Tcds\Io\Orm\Connection\ConnectionDriver;
use Tcds\Io\Orm\Connection\Pdo\NestedTransactionConnection;
use Test\Tcds\Io\Orm\TestCase;

class NestedTransactionConnectionTest extends TestCase
{
    private PDO&MockObject $write;

    private NestedTransactionConnection $connection;

    protected function setUp(): void
    {
        $read = $this->createMock(PDO::class);
        $this->write = $this->createMock(PDO::class);

        $this->connection = new class ($read, $this->write) extends NestedTransactionConnection
        {
            public function driver(): ConnectionDriver
            {
                return ConnectionDriver::GENERIC;
            }
        };
    }

    public function testWhenBeginThenCommitGetsCalledThenRunBeginAndCommitStatements(): void
    {
        $this->expectToSetupPdo(
            $this->write,
            ['BEGIN'],
            ['COMMIT'],
        );

        $this->connection->begin();
        $this->connection->commit();
    }

    public function testWhenBeginAndCommitGetsCalledMultipleTimesThenRunStoreAndReleaseSavepoint(): void
    {
        $this->expectToSetupPdo(
            $this->write,
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
        $this->expectToSetupPdo(
            $this->write,
            ['BEGIN'],
            ['ROLLBACK'],
        );

        $this->connection->begin();
        $this->connection->rollback();
    }

    public function testWhenBeginAndRollbackGetsCalledMultipleTimesThenRunStoreAndReleaseSavepoint(): void
    {
        $this->expectToSetupPdo(
            $this->write,
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

    /**
     * @param array<mixed> ...$params
     */
    private function expectToSetupPdo(PDO&MockObject $pdo, array ...$params): void
    {
        $matcher = $this->exactly(count($params));

        $pdo
            ->expects($matcher)
            ->method('exec')
            ->with($this->consecutive($matcher, ...$params));
    }
}
