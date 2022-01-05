<?php

namespace Cristal\ApiWrapper\Transports;

class NullTransport implements TransportInterface
{
    /**
     * Create a request and return the raw response.
     *
     * @param $endpoint
     * @param array $data
     * @param string $method
     *
     * @return mixed
     */
    public function rawRequest($endpoint, array $data = [], $method = 'get')
    {
        return null;
    }

    /**
     * Call rawRequest and handle the result.
     *
     * @param $endpoint
     * @param array $data
     * @param string $method
     *
     * @return mixed
     */
    public function request($endpoint, array $data = [], $method = 'get')
    {
        return [];
    }

    public function getResponseHeaders(): array
    {
        return [];
    }
}
