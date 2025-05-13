<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

use Exception;
use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\EntityRecordRepository;

/**
 * @extends EntityRecordRepository<User, string>
 */
class UserRepository extends EntityRecordRepository
{
    public function __construct(Connection $connection, UserMapper $mapper)
    {
        parent::__construct($mapper, $connection, 'users');
    }

    public function loadById(string $id): User
    {
        return $this->selectEntityById($id) ?? throw new Exception('Address not found');
    }
}
