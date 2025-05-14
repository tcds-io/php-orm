<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Test\Tcds\Io\Orm\Fixtures\Address;
use Test\Tcds\Io\Orm\Fixtures\AddressRepository;
use Test\Tcds\Io\Orm\Fixtures\User;
use Test\Tcds\Io\Orm\Fixtures\UserMapper;
use Test\Tcds\Io\Orm\TestCase;

class EntityRecordMapperTest extends TestCase
{
    private UserMapper $mapper;
    private AddressRepository&MockObject $addressRepository;

    protected function setUp(): void
    {
        $this->addressRepository = $this->createMock(AddressRepository::class);

        $this->mapper = new UserMapper($this->addressRepository);
    }

    #[Test] public function get_plain_array_from_first_user(): void
    {
        $object = User::first();

        $plain = $this->mapper->plain($object);

        $this->assertSame(User::firstData(), $plain);
    }

    #[Test] public function get_plain_array_from_second_user(): void
    {
        $object = User::second();

        $values = $this->mapper->plain($object);

        $this->assertSame(User::secondData(), $values);
    }

    #[Test] public function map_first_user(): void
    {
        $this->setupLoadAddress(1, Address::first());
        $data = User::firstData();

        $object = $this->mapper->map($data);
        initializeLazyObject($object->address);

        $this->assertEquals(User::first(), $object);
    }

    #[Test] public function map_second_user(): void
    {
        $this->setupLoadAddress(2, Address::second());
        $data = User::secondData();

        $object = $this->mapper->map($data);
        initializeLazyObject($object->address);

        $this->assertEquals(User::second(), $object);
    }

    private function setupLoadAddress(int $userId, Address $address): void
    {
        $this->addressRepository
            ->expects($this->once())
            ->method('loadAllByIds')
            ->with([$userId])
            ->willReturn(listOf($address));
    }
}
