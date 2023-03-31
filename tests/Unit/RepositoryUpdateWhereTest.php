<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\AddressRepository;

class RepositoryUpdateWhereTest extends TestCase
{
    private Connection&MockObject $connection;
    private AddressRepository $repository;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->repository = new AddressRepository($this->connection);
    }

    public function testGivenWhereWhenNotEmptyWhenRunQueryWithWhereAndLimitAndReturnOnlyOneEntry(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('execute')
            ->with(
                "UPDATE addresses SET street = :street WHERE id = :id",
                [':street' => 'Galaxy Avenue', ':id' => 'address-xxx'],
            );

        $this->repository->updateWhere(['street' => 'Galaxy Avenue'], ['id' => 'address-xxx']);
    }
}
