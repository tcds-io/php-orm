<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Old;

use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\RecordMapper;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressRecordMapper;

class TableMapperTest extends TestCase
{
    private RecordMapper $table;

    protected function setUp(): void
    {
        $connection = $this->createMock(Connection::class);

        $this->table = new AddressRecordMapper($connection);
    }

    public function testGetEntryValues(): void
    {
        $address = new Address(id: 'address-xxx', street: 'Galaxy Avenue');

        $values = $this->table->values($address);

        $this->assertEquals([
            'id' => 'address-xxx',
            'street' => 'Galaxy Avenue',
        ], $values);
    }
}
