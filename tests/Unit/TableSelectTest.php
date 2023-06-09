<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressTable;

class TableSelectTest extends TestCase
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

    public function testGivenWhereConditionWhenNotEmptyThenSelectWithWhereStatement(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(['id' => 'address-xxx', 'street' => "Galaxy Avenue"], null);

        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with(
                "SELECT * FROM addresses WHERE id = :id AND street = :street",
                [':id' => 'address-xxx', ':street' => "Galaxy Avenue"],
            )
            ->willReturn($this->statement);

        $result = $this->table->findBy(['id' => 'address-xxx', 'street' => 'Galaxy Avenue']);

        $this->assertEquals(
            [
                new Address(id: 'address-xxx', street: "Galaxy Avenue"),
            ],
            iterator_to_array($result),
        );
    }

    public function testGivenWhereConditionWhenEmptyThenSelectWithoutWhereStatement(): void
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
            ->with("SELECT * FROM addresses", [])
            ->willReturn($this->statement);

        $result = $this->table->findBy([]);

        $this->assertEquals(
            [
                new Address(id: 'address-xxx', street: "Galaxy Avenue"),
                new Address(id: 'address-yyy', street: "Galaxy Highway"),
            ],
            iterator_to_array($result),
        );
    }

    public function testGivenTheLimitAndOffsetThenSelectWithLimitOffsetStatement(): void
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
            ->with("SELECT * FROM addresses LIMIT 5 OFFSET 15", [])
            ->willReturn($this->statement);

        $result = $this->table->findBy(where: [], limit: 5, offset: 15);

        $this->assertEquals(
            [
                new Address(id: 'address-xxx', street: "Galaxy Avenue"),
                new Address(id: 'address-yyy', street: "Galaxy Highway"),
            ],
            iterator_to_array($result),
        );
    }
}
