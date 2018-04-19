<?php

namespace Starif\ApiWrapper;

interface TransportInterface
{
    public function request($endpoint, array $data = [], $method = 'get'): array;
}