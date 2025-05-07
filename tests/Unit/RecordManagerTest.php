<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PDOStatement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\RecordRepository;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressEntityRecordMapper;
use Test\Tcds\Io\Orm\TestCase;

class RecordManagerTest extends TestCase
{
    private Connection&MockObject $connection;
    private PDOStatement&MockObject $statement;
    private RecordRepository $manager;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->statement = $this->createMock(PDOStatement::class);

        $this->manager = new class (new AddressEntityRecordMapper(), $this->connection) extends RecordRepository
        {
            protected string $table {
                get {
                    return 'record_table';
                }
            }
        };
    }

    #[Test] public function insert(): void
    {
        $address = Address::first();

        $this->connection
            ->expects($this->once())
            ->method('write')
            ->with(
                "INSERT INTO addresses (id, street, number, floor, active) VALUES (:id, :street, :number, :floor, :active)",
                [
                    'id' => 'address-1',
                    'street' => 'First Avenue',
                    'number' => 145.45,
                    'floor' => 1,
                    'active' => true,
                ],
            );

        $this->manager->insert($address);
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
                "SELECT * FROM addresses WHERE id = :id LIMIT 1",
                ['id' => 'address-xxx'],
            )
            ->willReturn($this->statement);

        $result = $this->manager->loadBy(['id' => 'address-xxx']);

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

        $result = $this->manager->loadByQuery("select * from addresses where id LIKE :id", [':id' => 'address-xxx']);

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
                'SELECT * FROM addresses WHERE id = :id AND street = :street LIMIT 5 OFFSET 15',
                ['id' => 'address-xxx', 'street' => "Galaxy Avenue"],
            )
            ->willReturn($this->statement);

        $result = $this->manager->listBy(
            ['id' => 'address-xxx', 'street' => 'Galaxy Avenue'],
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
                ['id' => 'address-xxx', 'street' => "Galaxy Avenue"],
                ['id' => 'address-yyy', 'street' => "Galaxy Highway"],
                null,
            );

        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with("SELECT * FROM addresses", [])
            ->willReturn($this->statement);

        $result = $this->manager->listBy();

        $this->assertEquals(
            [
                new Address(id: 'address-xxx', street: "Galaxy Avenue"),
                new Address(id: 'address-yyy', street: "Galaxy Highway"),
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

        $result = $this->manager->listByQuery('SELECT * FROM addresses WHERE foo = :foo', ['foo' => 'bar']);

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
            ->with("SELECT * FROM addresses WHERE id = :id LIMIT 1", ['id' => 'address-xxx'])
            ->willReturn($this->statement);

        $exists = $this->manager->exists(['id' => 'address-xxx']);

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
            ->with("SELECT * FROM addresses WHERE id = :id LIMIT 1", ['id' => 'address-xxx'])
            ->willReturn($this->statement);

        $exists = $this->manager->exists(['id' => 'address-xxx']);

        $this->assertFalse($exists);
    }

    #[Test] public function delete_where(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('write')
            ->with(
                "DELETE FROM addresses WHERE id = :id",
                ['id' => 'address-xxx'],
            );

        $this->manager->deleteWhere(['id' => 'address-xxx']);
    }

    #[Test] public function update(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('write')
            ->with(
                "UPDATE addresses SET street = :street WHERE id = :id",
                ['street' => 'Galaxy Avenue', 'id' => 'address-xxx'],
            );

        $this->manager->updateWhere(['street' => 'Galaxy Avenue'], ['id' => 'address-xxx']);
    }
}
