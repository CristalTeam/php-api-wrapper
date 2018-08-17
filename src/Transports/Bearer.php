<?php

namespace Cpro\ApiWrapper\Transports;

use Cpro\ApiWrapper\ApiEntityNotFoundException;
use Cpro\ApiWrapper\ApiException;
use Cpro\ApiWrapper\TransportInterface;
use Curl\Curl as CurlClient;

class Bearer implements TransportInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var null|string
     */
    protected $entrypoint;

    /**
     * @var CurlClient
     */
    protected $client;

    /**
     * Curl constructor.
     *
     * @param string     $token
     * @param string     $entrypoint
     * @param CurlClient $client
     */
    public function __construct(string $token, string $entrypoint, CurlClient $client)
    {
        $this->client = $client;
        $this->token = $token;
        $this->entrypoint = rtrim($entrypoint, '/').'/';
        $this->client->setHeader('Authorization', 'Bearer '.$this->token);
        $this->client->setHeader('Content-Type', 'application/json');
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

        return $this->getClient()->rawResponse;
    }

    /**
     * {@inheritdoc}
     */
    public function request($endpoint, array $data = [], $method = 'get'): array
    {
        $rawResponse = $this->rawRequest($endpoint, $data, $method);

        if (!($this->getClient()->httpStatusCode >= 200 && $this->getClient()->httpStatusCode <= 299)) {
            $response = json_decode($rawResponse, true);
            if ($response === null && json_last_error() !== JSON_ERROR_NONE && !isset($response['message'])) {
                throw new \Exception($rawResponse ?? $this->getClient()->errorMessage);
            }
            if ($this->getClient()->httpStatusCode >= 400 && $this->getClient()->httpStatusCode <= 499) {
                throw new ApiEntityNotFoundException($response, $this->getClient()->httpStatusCode);
            }

            throw new ApiException($response, $this->getClient()->httpStatusCode);
        }

        if (!$rawResponse) {
            return [];
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

        return (count($data)) ? $url.'?'.http_build_query($data) : $url;
    }
}
