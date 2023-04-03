<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\AddressTable;

class TableDeleteWhereTest extends TestCase
{
    private Connection&MockObject $connection;
    private AddressTable $table;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->table = new AddressTable($this->connection);
    }

    public function testGivenWhereWhenNotEmptyWhenRunQueryWithWhereAndLimitAndReturnOnlyOneEntry(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('write')
            ->with(
                "DELETE FROM addresses WHERE id = :id",
                [':id' => 'address-xxx'],
            );

        $this->table->deleteWhere(['id' => 'address-xxx']);
    }
}
