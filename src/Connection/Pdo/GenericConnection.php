<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Connection\Pdo;

use PDO;
use PDOStatement;
use Tcds\Io\Orm\Connection\Connection;
use Throwable;

class GenericConnection implements Connection
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->pdo = $pdo;
    }

    /**
     * @param array<string, string|int|float|bool|null> $params
     */
    public function execute(string $statement, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($statement);
        $stmt->execute($params);

        return $stmt;
    }

    public function exec(string $statement): void
    {
        $this->pdo->exec($statement);
    }

    public function begin(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    /**
     * @throws Throwable
     */
    public function transaction(callable $fn): mixed
    {
        $this->begin();

        try {
            $res = $fn($this);
            $this->commit();

            return $res;
        } catch (Throwable $e) {
            $this->rollback();

            throw $e;
        }
    }
}
