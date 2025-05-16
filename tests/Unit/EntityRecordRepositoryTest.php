<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\Connection\Driver;
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

        $this->connection
            ->method('driver')
            ->willReturn(Driver::MYSQL);

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
            ->with('SELECT * FROM `users` WHERE `id` = ? LIMIT 1', ['galaxy-1']);

        $this->repository->selectEntityById('galaxy-1');
    }

    #[Test] public function update_one(): void
    {
        $this->connection
            ->expects($this->exactly(1))
            ->method('write')
            ->with(
                'UPDATE `users` SET `id` = ?, `name` = ?, `date_of_birth` = ?, `address_id` = ? WHERE `id` = ?',
                [1, 'First User', '2020-01-01', 1, 1],
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
                    'UPDATE `users` SET `id` = ?, `name` = ?, `date_of_birth` = ?, `address_id` = ? WHERE `id` = ?',
                    [
                        1,
                        'First User',
                        '2020-01-01',
                        1,
                        1,
                    ],
                ],
                [
                    'UPDATE `users` SET `id` = ?, `name` = ?, `date_of_birth` = ?, `address_id` = ? WHERE `id` = ?',
                    [
                        2,
                        'Second User',
                        '2022-10-15',
                        2,
                        2,
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
                'DELETE FROM `users` WHERE `id` = ?',
                [1],
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
                    'DELETE FROM `users` WHERE `id` = ?',
                    [1],
                ],
                [
                    'DELETE FROM `users` WHERE `id` = ?',
                    [2],
                ],
            ));

        $this->repository->deleteMany(
            User::first(),
            User::second(),
        );
    }
}
