<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\EntityRecordRepository;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressEntityRecordMapper;
use Test\Tcds\Io\Orm\TestCase;

class EntityManagerTest extends TestCase
{
    private Connection&MockObject $connection;

    private EntityRecordRepository $manager;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);

        $this->manager = new class (new AddressEntityRecordMapper(), $this->connection) extends EntityRecordRepository
        {
            protected string $table {
                get {
                    return 'entity_table';
                }
            }
        };
    }

    #[Test] public function load_by_id(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with('SELECT * FROM entity_table WHERE id = :id LIMIT 1', ['id' => 'galaxy-1']);

        $this->manager->loadById('galaxy-1');
    }

    #[Test] public function update(): void
    {
        $matcher = $this->exactly(2);

        $this->connection
            ->expects($matcher)
            ->method('write')
            ->with($this->consecutive(
                $matcher,
                [
                    'UPDATE entity_table SET id = :id, street = :street, number = :number, floor = :floor, active = :active WHERE id = :id',
                    [
                        'id' => 'address-1',
                        'street' => 'First Avenue',
                        'number' => 145.45,
                        'floor' => 1,
                        'active' => true,
                    ],
                ],
                [
                    'UPDATE entity_table SET id = :id, street = :street, number = :number, floor = :floor, active = :active WHERE id = :id',
                    [
                        'id' => 'address-2',
                        'street' => 'Second Avenue',
                        'number' => 34.9,
                        'floor' => 5,
                        'active' => false,
                    ],
                ],
            ));

        $this->manager->update(
            Address::first(),
            Address::second(),
        );
    }

    #[Test] public function delete(): void
    {
        $matcher = $this->exactly(2);

        $this->connection
            ->expects($matcher)
            ->method('write')
            ->with($this->consecutive(
                $matcher,
                [
                    'DELETE FROM entity_table WHERE id = :id',
                    ['id' => 'address-1'],
                ],
                [
                    'DELETE FROM entity_table WHERE id = :id',
                    ['id' => 'address-2'],
                ],
            ));

        $this->manager->delete(
            Address::first(),
            Address::second(),
        );
    }
}
