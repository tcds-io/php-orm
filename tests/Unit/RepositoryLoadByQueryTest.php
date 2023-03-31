<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tcds\Io\Orm\Connection\Connection;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressRepository;

class RepositoryLoadByQueryTest extends TestCase
{
    private Connection&MockObject $connection;
    private PDOStatement&MockObject $statement;
    private AddressRepository $repository;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->statement = $this->createMock(PDOStatement::class);
        $this->repository = new AddressRepository($this->connection);
    }

    public function testGivenASqlQueryAndItsBindingsThenBypassTheQueryToTheConnectionAndReturnTheFirstItem(): void
    {
        $this->statement
            ->method('fetch')
            ->willReturn(['id' => 'address-xxx', 'street' => "Galaxy Avenue"]);
        $this->connection
            ->expects($this->once())
            ->method('execute')
            ->with("select * from addresses where id LIKE :id", [':id' => 'address-xxx'])
            ->willReturn($this->statement);

        $result = $this->repository->loadByQuery("select * from addresses where id LIKE :id", [':id' => 'address-xxx']);

        $this->assertEquals(new Address(id: 'address-xxx', street: "Galaxy Avenue"), $result);
    }
}
