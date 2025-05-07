<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Old;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\AddressRecordMapper;

class TableUpdateWhereTest extends TestCase
{
    private Connection&MockObject $connection;
    private AddressRecordMapper $table;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->table = new AddressRecordMapper($this->connection);
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
