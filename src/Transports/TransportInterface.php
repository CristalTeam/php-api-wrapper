<?php

namespace Cpro\ApiWrapper\Transports;

interface TransportInterface
{
    public function rawRequest($endpoint, array $data = [], $method = 'get');

    public function request($endpoint, array $data = [], $method = 'get');
}
