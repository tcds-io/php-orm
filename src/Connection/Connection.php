<?php

declare(strict_types=1);

namespace Tcds\Io\Orm\Connection;

use PDOStatement;

interface Connection
{
    /**
     * @param array<string, string|int|float|bool|null> $params
     */
    public function execute(string $statement, array $params = []): PDOStatement;

    public function begin(): void;

    public function commit(): void;

    public function rollback(): void;

    public function transaction(callable $fn): mixed;
}
