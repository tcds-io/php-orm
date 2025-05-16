<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PDOStatement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\Connection\Driver;
use Tcds\Io\Orm\RecordRepository;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressMapper;
use Test\Tcds\Io\Orm\TestCase;

class RecordRepositoryTest extends TestCase
{
    private Connection&MockObject $connection;
    private PDOStatement&MockObject $statement;
    private RecordRepository $manager;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->statement = $this->createMock(PDOStatement::class);

        $this->connection
            ->method('driver')
            ->willReturn(Driver::MYSQL);

        $this->manager = new class (new AddressMapper(), $this->connection, 'addresses') extends RecordRepository
        {
        };
    }

    #[Test] public function insert(): void
    {
        $address = Address::first();

        $this->connection
            ->expects($this->once())
            ->method('write')
            ->with(
                "INSERT INTO addresses (id, street, number, floor, active, type, created_at, deleted_at) VALUES (:id, :street, :number, :floor, :active, :type, :created_at, :deleted_at)",
                [
                    'id' => 1,
                    'street' => 'First Avenue',
                    'number' => 145.45,
                    'floor' => 1,
                    'active' => true,
                    'type' => 'RESIDENCE',
                    'created_at' => '2025-05-01T10:15:20+00:00',
                    'deleted_at' => '2025-05-10T11:16:30+00:00',
                ],
            );

        $this->manager->insertOne($address);
    }

    #[Test] public function load_by(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturn(Address::secondRowData());
        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with(
                "SELECT * FROM `addresses` WHERE `id` = ? LIMIT 1",
                ['address-xxx'],
            )
            ->willReturn($this->statement);

        $result = $this->manager->selectOneWhere(where(['id' => equalsTo('address-xxx')]));

        $this->assertEquals(Address::second(), $result);
    }

    #[Test] public function load_by_query(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturn(Address::firstRowData());
        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with("select * from addresses where id LIKE :id", [':id' => 'address-xxx'])
            ->willReturn($this->statement);

        $result = $this->manager->selectOneByQuery("select * from addresses where id LIKE :id", [':id' => 'address-xxx']);

        $this->assertEquals(Address::first(), $result);
    }

    #[Test] public function list_by(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                Address::firstRowData(),
                Address::secondRowData(),
                null,
            );

        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with(
                'SELECT * FROM `addresses` WHERE `id` = ? AND `street` = ? LIMIT 5 OFFSET 15',
                ['address-xxx', "Galaxy Avenue"],
            )
            ->willReturn($this->statement);

        $result = $this->manager->selectManyWhere(
            where([
                'id' => equalsTo('address-xxx'),
                'street' => equalsTo('Galaxy Avenue'),
            ]),
            limit: 5,
            offset: 15,
        );

        $this->assertEquals(
            [
                Address::first(),
                Address::second(),
            ],
            iterator_to_array($result),
        );
    }

    public function list_by_returns_multiple_entries(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                Address::firstRowData(),
                Address::secondRowData(),
                null,
            );

        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with("SELECT * FROM addresses", [])
            ->willReturn($this->statement);

        $result = $this->manager->selectManyWhere();

        $this->assertEquals(
            [
                Address::first(),
                Address::second(),
            ],
            iterator_to_array($result),
        );
    }

    #[Test] public function list_by_query(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(
                Address::firstRowData(),
                Address::secondRowData(),
                null,
            );

        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with("SELECT * FROM addresses WHERE foo = :foo", ['foo' => 'bar'])
            ->willReturn($this->statement);

        $result = $this->manager->selectManyByQuery('SELECT * FROM addresses WHERE foo = :foo', ['foo' => 'bar']);

        $this->assertEquals(
            [
                Address::first(),
                Address::second(),
            ],
            iterator_to_array($result),
        );
    }

    #[Test] public function exists_returns_true(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturn(Address::firstRowData());
        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with("SELECT * FROM `addresses` WHERE `id` = ? LIMIT 1", ['address-xxx'])
            ->willReturn($this->statement);

        $exists = $this->manager->existsWhere(where(['id' => equalsTo('address-xxx')]));

        $this->assertTrue($exists);
    }

    #[Test] public function exists_returns_false(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturn(null);
        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with("SELECT * FROM `addresses` WHERE `id` = ? LIMIT 1", ['address-xxx'])
            ->willReturn($this->statement);

        $exists = $this->manager->existsWhere(where(['id' => equalsTo('address-xxx')]));

        $this->assertFalse($exists);
    }

    #[Test] public function delete_where(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('write')
            ->with(
                "DELETE FROM `addresses` WHERE `id` = ?",
                ['address-xxx'],
            );

        $this->manager->deleteWhere(where(['id' => equalsTo('address-xxx')]));
    }

    #[Test] public function update(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('write')
            ->with(
                "UPDATE `addresses` SET `street` = ? WHERE `id` = ?",
                ['Galaxy Avenue', 'address-xxx'],
            );

        $this->manager->updateWhere(['street' => 'Galaxy Avenue'], where(['id' => equalsTo('address-xxx')]));
    }
}
