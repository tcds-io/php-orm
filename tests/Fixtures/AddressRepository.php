<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

use Exception;
use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\RecordMapper;
use Tcds\Io\Orm\RecordRepository;

/**
 * @extends RecordRepository<Address>
 */
class AddressRepository extends RecordRepository
{
    public function __construct(Connection $connection, RecordMapper $mapper)
    {
        parent::__construct($mapper, $connection, 'addresses');
    }

    public function loadById(int $id): Address
    {
        return $this->selectOneWhere(['id' => $id]) ?? throw new Exception('Address not found');
    }
}
