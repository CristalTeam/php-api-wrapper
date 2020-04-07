<?php

namespace Cristal\ApiWrapper\Bridges\Symfony;

use Cristal\ApiWrapper\Api;

interface ConnectionInterface
{
    public function getName(): string;

    public function getApi(): Api;
}
