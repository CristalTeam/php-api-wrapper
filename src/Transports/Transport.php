<?php

namespace Cpro\ApiWrapper\Transports;

use Cpro\ApiWrapper\Exceptions\ApiEntityNotFoundException;
use Cpro\ApiWrapper\Exceptions\ApiException;
use Curl\Curl as CurlClient;

class Transport implements TransportInterface
{
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
                $this->getClient()->post($url, json_encode($data));
                break;
            case 'put':
                $url = $this->getUrl($endpoint);
                $this->getClient()->put($url, json_encode($data));
                break;
            case 'delete':
                $url = $this->getUrl($endpoint);
                $this->getClient()->delete($url, json_encode($data));
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

    /**
     * {@inheritdoc}
     */
    public function request($endpoint, array $data = [], $method = 'get')
    {
        $rawResponse = $this->rawRequest($endpoint, $data, $method);

        if (!($this->getClient()->httpStatusCode >= 200 && $this->getClient()->httpStatusCode <= 299)) {
            $response = json_decode($rawResponse, true);

            if ($response === null && json_last_error() !== JSON_ERROR_NONE && !isset($response['message'])) {
                $response = ['message' => $this->getClient()->response];

                throw new ApiException($response, $this->getClient()->httpStatusCode);
            }

            throw new ApiException($response, $this->getClient()->httpStatusCode);
        }

        return json_decode($rawResponse, true);
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

        return '?'.http_build_query($data);
    }
}
