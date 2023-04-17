<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\Table;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressTable;

class TableTest extends TestCase
{
    private Table $table;

    protected function setUp(): void
    {
        $connection = $this->createMock(Connection::class);

        $this->table = new AddressTable($connection);
    }

    public function testGetEntryValues(): void
    {
        $address = new Address(id: 'address-xxx', street: 'Galaxy Avenue');

        $values = $this->table->valuesOf($address);

        $this->assertEquals(['id' => 'address-xxx', 'street' => 'Galaxy Avenue'], $values);
    }
}
