<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Old;

use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressRecordMapper;

class TableMapperLoadByTest extends TestCase
{
    private Connection&MockObject $connection;
    private PDOStatement&MockObject $statement;
    private AddressRecordMapper $table;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->statement = $this->createMock(PDOStatement::class);
        $this->table = new AddressRecordMapper($this->connection);
    }

    public function testGivenWhereWhenNotEmptyWhenRunQueryWithWhereAndLimitAndReturnOnlyOneEntry(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturn(['id' => 'address-xxx', 'street' => "Galaxy Avenue"]);
        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with(
                "SELECT * FROM addresses WHERE id = :id LIMIT 1",
                [':id' => 'address-xxx'],
            )
            ->willReturn($this->statement);

        $result = $this->table->loadBy(['id' => 'address-xxx']);

        $this->assertEquals(new Address(id: 'address-xxx', street: "Galaxy Avenue"), $result);
    }
}
