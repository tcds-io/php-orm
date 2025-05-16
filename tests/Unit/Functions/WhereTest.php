<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Unit\Functions;

use PHPUnit\Framework\Attributes\Test;
use Tcds\Io\Orm\Connection\Driver;
use Tcds\Io\Orm\Query\Query;
use Test\Tcds\Io\Orm\TestCase;

class WhereTest extends TestCase
{
    #[Test] public function convert_where_to_query(): void
    {
        $where = where([
            'name' => equalsTo('Arthur'),
            'last_name' => like('Dent%'),
            'year' => between(1900, 2018),
        ]);

        [$query, $params] = $where->build(Driver::MYSQL);

        $this->assertEquals(
            Query::where('name', equalsTo('Arthur'))
                ->and('last_name', like('Dent%'))
                ->and('year', between(1900, 2018)),
            $where,
        );
        $this->assertEquals('WHERE `name` = ? AND `last_name` LIKE ? AND `year` BETWEEN ? AND ?', $query);
        $this->assertEquals(['Arthur', 'Dent%', 1900, 2018], $params);
    }
}
