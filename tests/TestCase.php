<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm;

use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

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
}
