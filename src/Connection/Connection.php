<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Connection;

use PDOStatement;

interface Connection
{
    public function begin(): void;

    public function commit(): void;

    public function rollback(): void;

    public function transaction(callable $fn): mixed;

    /**
     * @param array<string, string|int|float|bool|null> $params
     */
    public function read(string $statement, array $params = []): PDOStatement;

    /**
     * @param array<string, string|int|float|bool|null> $params
     */
    public function write(string $statement, array $params = []): PDOStatement;
}
