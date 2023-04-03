<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Connection\Pdo;

use PDO;
use PDOStatement;
use Tcds\Io\Orm\Connection\Connection;
use Throwable;

class GenericConnection implements Connection
{
    private PDO $read;
    private PDO $write;

    public function __construct(PDO $read, PDO $write)
    {
        $this->read = $read;
        $this->read->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->read->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->write = $write;
        $this->write->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->write->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function begin(): void
    {
        $this->write->beginTransaction();
    }

    public function commit(): void
    {
        $this->write->commit();
    }

    public function rollback(): void
    {
        $this->write->rollBack();
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

    /**
     * @inheritdoc
     */
    public function read(string $statement, array $params = []): PDOStatement
    {
        $stmt = $this->read->prepare($statement);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * @inheritdoc
     */
    public function write(string $statement, array $params = []): PDOStatement
    {
        $stmt = $this->write->prepare($statement);
        $stmt->execute($params);

        return $stmt;
    }

    protected function exec(string $statement): void
    {
        $this->write->exec($statement);
    }
}
