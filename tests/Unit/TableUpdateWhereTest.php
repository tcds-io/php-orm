<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\AddressTable;

class TableUpdateWhereTest extends TestCase
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
                "UPDATE addresses SET street = :street WHERE id = :id",
                [':street' => 'Galaxy Avenue', ':id' => 'address-xxx'],
            );

        $this->table->updateWhere(['street' => 'Galaxy Avenue'], ['id' => 'address-xxx']);
    }
}
