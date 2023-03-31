<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressRepository;

class RepositoryInsertTest extends TestCase
{
    private Connection&MockObject $connection;
    private AddressRepository $repository;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->repository = new AddressRepository($this->connection);
    }

    public function testGivenAnEntryThenRunInsertWithItsData(): void
    {
        $address = new Address("address-xxx", "Galaxy Avenue");

        $this->connection
            ->expects($this->once())
            ->method('execute')
            ->with(
                <<<SQL
                    INSERT INTO addresses (id, street)
                        VALUES (:id, :street)
                SQL,
                [':id' => 'address-xxx', ':street' => "Galaxy Avenue"],
            );

        $this->repository->insert($address);
    }
}
