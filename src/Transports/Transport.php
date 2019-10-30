<?php

namespace Cpro\ApiWrapper\Transports;

use Closure;
use Cpro\ApiWrapper\Exceptions\ApiEntityNotFoundException;
use Cpro\ApiWrapper\Exceptions\ApiException;
use Cpro\ApiWrapper\Exceptions\Handlers\AbstractErrorHandler;
use Cpro\ApiWrapper\Exceptions\Handlers\NetworkErrorHandler;
use Cpro\ApiWrapper\Exceptions\Handlers\NotFoundErrorHandler;
use Curl\Curl as CurlClient;
use CURLFile;

/**
 * Class Transport
 * @package Cpro\ApiWrapper\Transports
 */
class Transport implements TransportInterface
{
    const HTTP_NETWORK_ERROR_CODE = 0;
    const HTTP_NOT_FOUND_ERROR_CODE = 404;

    /**
     * @var null|string
     */
    protected $entrypoint;

    /**
     * @var CurlClient
     */
    protected $client;

    /**
     * @var AbstractErrorHandler[]
     */
    protected $errorHandlers = [];

    /**
     * Transport constructor.
     *
     * @param string $entrypoint
     * @param CurlClient $client
     */
    public function __construct(string $entrypoint, CurlClient $client)
    {
        $this->client = $client;
        $this->entrypoint = rtrim($entrypoint, '/').'/';

        $this->setErrorHandler(self::HTTP_NETWORK_ERROR_CODE, new NetworkErrorHandler($this));
        $this->setErrorHandler(self::HTTP_NOT_FOUND_ERROR_CODE, new NotFoundErrorHandler($this));

        $this->getClient()->setHeader('Content-Type', 'application/json');
    }

    /**
     * Define or remove an error handler for the request.
     * Pass null to remove an existing handler.
     *
     * @param int $code
     * @param AbstractErrorHandler|null $handler
     *
     * @return $this
     */
    public function setErrorHandler(int $code, ?AbstractErrorHandler $handler)
    {
        $this->errorHandlers[$code] = $handler;

        if(is_null($handler)) {
            unset($this->errorHandlers[$code]);
        }

        return $this;
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
    public function request($endpoint, array $data = [], $method = 'get')
    {
        $rawResponse = $this->rawRequest($endpoint, $data, $method);
        $httpStatusCode = $this->getClient()->httpStatusCode;
        $response = json_decode($rawResponse, true);

        if ($httpStatusCode >= 200 && $httpStatusCode <= 299) {
            return $response;
        }

        $exception = new ApiException(
            $response,
            $response['message'] ?? $rawResponse ?? 'Unknown error message',
            $httpStatusCode
        );

        if ($handler = $this->errorHandlers[$httpStatusCode] ?? false) {
            return $handler->handle($exception, compact('endpoint', 'data', 'method'));
        }

        throw $exception;
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
                $this->getClient()->post(
                    $this->getUrl($endpoint),
                    $this->encodeBody($data),
                    (version_compare(PHP_VERSION, '5.5.11') < 0) || defined('HHVM_VERSION')
                );
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

        return $this->getClient()->rawResponse;
    }

    /**
     * Build URL with stored entrypoint, the endpoint and data queries.
     *
     * @param string $endpoint
     * @param array $data
     *
     * @return string
     */
    protected function getUrl(string $endpoint, array $data = [])
    {
        $url = $this->getEntrypoint() . ltrim($endpoint, '/');

        return $url . $this->appendData($data);
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
     * Add request parameters to the URI.
     *
     * @param array $data
     *
     * @return string|null
     */
    protected function appendData(array $data = [])
    {
        if (!count($data)) {
            return null;
        }

        $data = array_map(function ($item) {
            return is_null($item) ? '' : $item;
        }, $data);

        return '?' . http_build_query($data);
    }

    /**
     * If a file is sent, use multipart header and raw data.
     *
     * @param $data
     *
     * @return false|string
     */
    public function encodeBody($data)
    {
        foreach ($data as $value) {
            if ($value instanceof CURLFile) {
                $this->getClient()->setHeader('Content-Type', 'multipart/form-data');

                return $data;
            }
        }

        return json_encode($data);
    }
}
