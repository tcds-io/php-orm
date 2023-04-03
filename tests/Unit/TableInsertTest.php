<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressTable;

class TableInsertTest extends TestCase
{
    private Connection&MockObject $connection;
    private AddressTable $table;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->table = new AddressTable($this->connection);
    }

    public function testGivenAnEntryThenRunInsertWithItsData(): void
    {
        $address = new Address("address-xxx", "Galaxy Avenue");

        $this->connection
            ->expects($this->once())
            ->method('write')
            ->with(
                <<<SQL
                    INSERT INTO addresses (id, street)
                        VALUES (:id, :street)
                SQL,
                [':id' => 'address-xxx', ':street' => "Galaxy Avenue"],
            );

        $this->table->insert($address);
    }
}
