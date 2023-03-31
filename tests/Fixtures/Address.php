<?php

declare(strict_types=1);

namespace Test\Tcds\Io\Orm\Fixtures;

class Address
{
    public readonly string $id;
    public readonly string $street;

    public function __construct(string $id, string $street)
    {
        $this->id = $id;
        $this->street = $street;
    }
}
