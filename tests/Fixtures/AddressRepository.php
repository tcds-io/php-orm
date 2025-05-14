<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

use Exception;
use Tcds\Io\Orm\Connection\Connection;
use Tcds\Io\Orm\RecordMapper;
use Tcds\Io\Orm\RecordRepository;
use Traversable;

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

    /**
     * @param list<string> $ids
     * @return Traversable<Address>
     */
    public function loadAllByIds(array $ids): Traversable
    {
        return $this->selectManyByQuery('SELECT * FROM addresses WHERE id IN (:ids)', ['ids' => $ids]);
    }
}
