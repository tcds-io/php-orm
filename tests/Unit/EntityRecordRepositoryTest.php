<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\EntityRecordRepository;
use Test\Tcds\Io\Orm\Fixtures\AddressRepository;
use Test\Tcds\Io\Orm\Fixtures\User;
use Test\Tcds\Io\Orm\Fixtures\UserMapper;
use Test\Tcds\Io\Orm\Fixtures\UserRepository;
use Test\Tcds\Io\Orm\TestCase;

class EntityRecordRepositoryTest extends TestCase
{
    private Connection&MockObject $connection;
    private AddressRepository&MockObject $addressRepository;

    private EntityRecordRepository $repository;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->addressRepository = $this->createMock(AddressRepository::class);

        $this->repository = new UserRepository(
            $this->connection,
            new UserMapper($this->addressRepository),
        );
    }

    #[Test] public function load_by_id(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with('SELECT * FROM users WHERE id = :id LIMIT 1', ['id' => 'galaxy-1']);

        $this->repository->selectEntityById('galaxy-1');
    }

    #[Test] public function update_one(): void
    {
        $this->connection
            ->expects($this->exactly(1))
            ->method('write')
            ->with(
                'UPDATE users SET id = :id, name = :name, date_of_birth = :date_of_birth, address_id = :address_id WHERE id = :id',
                [
                    'id' => 1,
                    'name' => 'First User',
                    'date_of_birth' => '2020-01-01',
                    'address_id' => 1,
                ],
            );

        $this->repository->updateOne(User::first());
    }

    #[Test] public function update_many(): void
    {
        $matcher = $this->exactly(2);

        $this->connection
            ->expects($matcher)
            ->method('write')
            ->with($this->consecutive(
                $matcher,
                [
                    'UPDATE users SET id = :id, name = :name, date_of_birth = :date_of_birth, address_id = :address_id WHERE id = :id',
                    [
                        'id' => 1,
                        'name' => 'First User',
                        'date_of_birth' => '2020-01-01',
                        'address_id' => 1,
                    ],
                ],
                [
                    'UPDATE users SET id = :id, name = :name, date_of_birth = :date_of_birth, address_id = :address_id WHERE id = :id',
                    [
                        'id' => 2,
                        'name' => 'Second User',
                        'date_of_birth' => '2022-10-15',
                        'address_id' => 2,
                    ],
                ],
            ));

        $this->repository->updateMany(
            User::first(),
            User::second(),
        );
    }

    #[Test] public function delete_one(): void
    {
        $this->connection
            ->expects($this->exactly(1))
            ->method('write')
            ->with(
                'DELETE FROM users WHERE id = :id',
                ['id' => 1],
            );

        $this->repository->deleteOne(User::first());
    }

    #[Test] public function delete_many(): void
    {
        $matcher = $this->exactly(2);

        $this->connection
            ->expects($matcher)
            ->method('write')
            ->with($this->consecutive(
                $matcher,
                [
                    'DELETE FROM users WHERE id = :id',
                    ['id' => 1],
                ],
                [
                    'DELETE FROM users WHERE id = :id',
                    ['id' => 2],
                ],
            ));

        $this->repository->deleteMany(
            User::first(),
            User::second(),
        );
    }
}
