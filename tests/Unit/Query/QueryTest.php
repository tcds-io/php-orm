<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Query;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Connection\Driver;
use Tcds\Io\Orm\Query\Query;
use Test\Tcds\Io\Orm\TestCase;

class QueryTest extends TestCase
{
    #[Test] public function build_query(): void
    {
        $query = Query::where('name', like('Arthur%'))
            ->or('last_name', like('Dent%'))
            ->and('age', greaterThan(18));

        $this->assertEquals(
            [
                'WHERE `name` LIKE ? OR `last_name` LIKE ? AND `age` > ?',
                ['Arthur%', 'Dent%', 18],
            ],
            $query->build(Driver::MYSQL),
        );
    }

    #[Test] public function build_query_with_and_group(): void
    {
        $query = Query::where('active', equalsTo(true))
            ->andGrouped(fn(Query $query) => $query
                ->field('city', equalsTo('Berlin'))
                ->and('country', equalsTo('Germany')))
            ->orGrouped(fn(Query $query) => $query
                ->field('city', equalsTo('Amsterdam'))
                ->and('country', equalsTo('Netherlands')));

        $this->assertEquals(
            [
                'WHERE `active` = ? AND (`city` = ? AND `country` = ?) OR (`city` = ? AND `country` = ?)',
                [true, 'Berlin', 'Germany', 'Amsterdam', 'Netherlands'],
            ],
            $query->build(Driver::MYSQL),
        );
    }
}
