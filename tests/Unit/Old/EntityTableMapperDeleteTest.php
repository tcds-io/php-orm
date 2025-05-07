<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Old;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressEntityRecordMapper;

class EntityTableMapperDeleteTest extends TestCase
{
    private Connection&MockObject $connection;
    private AddressEntityRecordMapper $table;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->table = new AddressEntityRecordMapper($this->connection);
    }

    public function testGivenWhereWhenNotEmptyWhenRunQueryWithWhereAndLimitAndReturnOnlyOneEntry(): void
    {
        $address = new Address(id: 'address-xxx', street: "Galaxy Avenue");

        $this->connection
            ->expects($this->once())
            ->method('write')
            ->with("DELETE FROM addresses WHERE id = :id", [':id' => 'address-xxx']);

        $this->table->delete($address);
    }
}
