<?php

namespace Cpro\ApiWrapper\Transports;

use Cpro\ApiWrapper\Exceptions\ApiEntityNotFoundException;
use Cpro\ApiWrapper\Exceptions\ApiException;
use Curl\Curl as CurlClient;

class Transport implements TransportInterface
{
    const MAX_RETRIES = 2;

    const HTTP_NETWORK_ERROR_CODE = 0;

    /**
     * @var null|string
     */
    protected $entrypoint;

    /**
     * @var CurlClient
     */
    protected $client;

    public function __construct(string $entrypoint, CurlClient $client)
    {
        $this->client = $client;
        $this->entrypoint = rtrim($entrypoint, '/').'/';

        $this->getClient()->setHeader('Content-Type', 'application/json');
    }

    /**
     * Get entrypoint URL.
     *
     * @return string
     */
    public function getEntrypoint()
    {
        return $this->entrypoint;
    }

    /**
     * Get Curl client.
     *
     * @return CurlClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function rawRequest($endpoint, array $data = [], $method = 'get')
    {
        $method = strtolower($method);

        switch ($method) {
            case 'get':
                $url = $this->getUrl($endpoint, $data);
                $this->getClient()->get($url);
                break;
            case 'post':
                $url = $this->getUrl($endpoint);
                $this->getClient()->post($url, $this->encodeBody($data));
                break;
            case 'put':
                $url = $this->getUrl($endpoint);
                $this->getClient()->put($url, $this->encodeBody($data));
                break;
            case 'delete':
                $url = $this->getUrl($endpoint);
                $this->getClient()->delete($url, $this->encodeBody($data));
                break;
        }

        if ($this->getClient()->httpStatusCode == 404) {
            throw new ApiEntityNotFoundException(
                (array) $this->getClient()->response,
                $this->getClient()->httpStatusCode
            );
        }

        return $this->getClient()->rawResponse;
    }

    public function encodeBody($data)
    {
        // If a file is sent, use multipart header and raw data
        foreach ($data as $value) {
            if ($value instanceof \CURLFile) {
                $this->getClient()->setHeader('Content-Type', 'multipart/form-data');

                return $data;
            }
        }

        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function request($endpoint, array $data = [], $method = 'get')
    {
        $retries = 0;

        do {
            $rawResponse = $this->rawRequest($endpoint, $data, $method);
            $httpStatusCode = $this->getClient()->httpStatusCode;

            if ($httpStatusCode >= 200 && $httpStatusCode <= 299) {
                return json_decode($rawResponse, true);
            }
        } while ($httpStatusCode == self::HTTP_NETWORK_ERROR_CODE && $retries++ < self::MAX_RETRIES);

        $response = json_decode($rawResponse, true);

        if (!$response) {
            $response = ['message' => $this->getClient()->response];
        }

        throw new ApiException($response, $httpStatusCode);
    }

    /**
     * Build URL with stored entrypoint, the endpoint and data queries.
     *
     * @param string $endpoint
     * @param array  $data
     *
     * @return string
     */
    protected function getUrl(string $endpoint, array $data = [])
    {
        $url = $this->getEntrypoint().ltrim($endpoint, '/');

        return $url.$this->appendData($data);
    }

    protected function appendData(array $data = [])
    {
        if (!count($data)) {
            return null;
        }

        $data = array_map(function ($item) {
            return is_null($item) ? '' : $item;
        }, $data);

        return '?'.http_build_query($data);
    }
}
