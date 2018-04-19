<?php

namespace Starif\ApiWrapper;

interface TransportInterface
{
    public function __construct(string $token, string $entrypoint);

    public function request($endpoint, array $data = [], $method = 'get'): array;
}