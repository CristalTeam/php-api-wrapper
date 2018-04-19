<?php

namespace Starif\ApiWrapper\Transports;

use Curl\Curl as CurlClient;
use Starif\ApiWrapper\TransportInterface;

class Curl implements TransportInterface
{
    /**
     * @var string
     */
    protected $jwt;

    /**
     * @var null|string
     */
    protected $entrypoint;

    /**
     * @var CurlClient
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $jwt, string $entrypoint, CurlClient $client)
    {
        $this->client = $client;
        $this->jwt = $jwt;
        $this->entrypoint = rtrim($entrypoint, '/').'/';
        $this->client->setHeader('Authorization','Bearer '.$this->jwt);
    }

    /**
     * {@inheritdoc}
     */
    public function request($endpoint, array $data = [], $method = 'get'): array
    {
        $method = strtolower($method);

        switch ($method) {
            case 'get':
                $url = $this->getUrl($endpoint, $data);
                $this->client->get($url);
                break;
            case 'post':
                $url = $this->getUrl($endpoint);
                $this->client->post($url, $data);
                break;
            case 'put':
                $url = $this->getUrl($endpoint);
                $this->client->put($url, $data);
                break;
            case 'delete':
                $url = $this->getUrl($endpoint);
                $this->client->delete($url, $data);
                break;
        }

        if (!($this->client->httpStatusCode >= 200 && $this->client->httpStatusCode <= 299)) {
            throw new \Exception('La requête à l\'API a échoué, code HTTP ['.$this->client->httpStatusCode.'] : '.$this->client->rawResponse);
        }

        if(!$this->client->rawResponse){
            return [];
        }
        else{
            return json_decode($this->client->rawResponse, true);
        }
    }

    protected function getUrl(string $endpoint, array $data = [])
    {
        $url = $this->entrypoint.ltrim($endpoint, '/');
        return (count($data)) ? $url.http_build_query($data) : $url;
    }
}