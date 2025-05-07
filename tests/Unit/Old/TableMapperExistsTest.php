<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Old;

use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\AddressRecordMapper;

class TableMapperExistsTest extends TestCase
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

    public function testGivenTheConditionsWhenSelectReturnsAnEntryThenExistIsTrue(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturn(['id' => 'address-xxx', 'street' => "Galaxy Avenue"]);
        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with("SELECT * FROM addresses WHERE id = :id LIMIT 1", [':id' => 'address-xxx'])
            ->willReturn($this->statement);

        $exists = $this->table->exists(['id' => 'address-xxx']);

        $this->assertTrue($exists);
    }

    public function testGivenTheConditionsWhenSelectReturnsNullThenExistIsFalse(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturn(null);
        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with("SELECT * FROM addresses WHERE id = :id LIMIT 1", [':id' => 'address-xxx'])
            ->willReturn($this->statement);

        $exists = $this->table->exists(['id' => 'address-xxx']);

        $this->assertFalse($exists);
    }
}
