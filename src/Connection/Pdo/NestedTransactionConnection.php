<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Connection\Pdo;

use PDOException;

abstract class NestedTransactionConnection extends GenericConnection
{
    private const int INITIAL_DEPTH = 0;

    private int $transactionDepth = self::INITIAL_DEPTH;

    public function begin(): void
    {
        match ($this->transactionDepth) {
            self::INITIAL_DEPTH => $this->exec("BEGIN"),
            default => $this->exec("SAVEPOINT LEVEL{$this->transactionDepth}"),
        };

        $this->transactionDepth++;
    }

    public function commit(): void
    {
        $this->transactionDepth--;

        match ($this->transactionDepth) {
            self::INITIAL_DEPTH => $this->exec("COMMIT"),
            default => $this->exec("RELEASE SAVEPOINT LEVEL{$this->transactionDepth}"),
        };
    }

    public function rollback(): void
    {
        if ($this->transactionDepth === self::INITIAL_DEPTH) {
            throw new PDOException('Rollback error: There is no transaction started');
        }

        $this->transactionDepth--;

        match ($this->transactionDepth) {
            self::INITIAL_DEPTH => $this->exec("ROLLBACK"),
            default => $this->exec("RELEASE SAVEPOINT LEVEL{$this->transactionDepth}"),
        };
    }
}
