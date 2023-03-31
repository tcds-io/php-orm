<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressEntityRepository;

class EntityRepositoryUpdateTest extends TestCase
{
    private Connection&MockObject $connection;
    private AddressEntityRepository $repository;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->repository = new AddressEntityRepository($this->connection);
    }

    public function testGivenWhereWhenNotEmptyWhenRunQueryWithWhereAndLimitAndReturnOnlyOneEntry(): void
    {
        $address = new Address(id: 'address-xxx', street: "Galaxy Avenue");

        $this->connection
            ->expects($this->once())
            ->method('execute')
            ->with(
                "UPDATE addresses SET street = :street, id = :id WHERE id = :id",
                [':street' => 'Galaxy Avenue', ':id' => 'address-xxx'],
            );

        $this->repository->update($address);
    }
}
