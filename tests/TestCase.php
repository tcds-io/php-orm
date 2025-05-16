<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm;

use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use Tcds\Io\Orm\Connection\Driver;
use Tcds\Io\Orm\Query\Conditions\FilteringCondition;
use Tcds\Io\Orm\Query\Query;

class TestCase extends PhpUnitTestCase
{
    /**
     * @param array<mixed> ...$expectedCalls
     */
    public function consecutive(InvokedCountMatcher $matcher, array ...$expectedCalls): Callback
    {
        $expectedCalls = array_values($expectedCalls);

        return $this->callback(function (...$args) use ($matcher, $expectedCalls) {
            $index = $matcher->numberOfInvocations() - 1;

            $this->assertEquals($expectedCalls[$index], $args);

            return true;
        });
    }

    /**
     * @param list<mixed> $params
     */
    protected function assertConditionQuery(FilteringCondition $condition, string $query, array $params): void
    {
        $this->assertEquals([$query, $params], Query::where($condition)->build(Driver::MYSQL));
    }
}
