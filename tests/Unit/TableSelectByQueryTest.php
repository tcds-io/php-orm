<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressTable;

class TableSelectByQueryTest extends TestCase
{
    private Connection&MockObject $connection;
    private PDOStatement&MockObject $statement;
    private AddressTable $table;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->statement = $this->createMock(PDOStatement::class);
        $this->table = new AddressTable($this->connection);
    }

    public function testGivenASqlQueryAndItsBindingsThenBypassTheQueryToTheConnectionAndReturnTheItemList(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                ['id' => 'address-xxx', 'street' => "Galaxy Avenue"],
                ['id' => 'address-yyy', 'street' => "Galaxy Highway"],
                null,
            );

        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with("select * from addresses where street LIKE :street", [':street' => 'Galaxy%'])
            ->willReturn($this->statement);

        $result = $this->table->findByQuery(
            "select * from addresses where street LIKE :street",
            [':street' => 'Galaxy%'],
        );

        $this->assertEquals(
            [
                new Address(id: 'address-xxx', street: "Galaxy Avenue"),
                new Address(id: 'address-yyy', street: "Galaxy Highway"),
            ],
            iterator_to_array($result),
        );
    }
}
