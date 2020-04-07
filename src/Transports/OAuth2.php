<?php

namespace Cristal\ApiWrapper\Transports;

use Cristal\ApiWrapper\Transports\Transport as TransportCore;
use Curl\Curl;
use Illuminate\Contracts\Cache\Repository;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class OAuth2 extends TransportCore
{
    /**
     * @var AbstractProvider
     */
    private $provider;

    /**
     * @var Repository
     */
    private $cache;

    /**
     * @var string
     */
    private $grant = 'password';

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var string
     */
    private $token;

    public function __construct(string $entrypoint, AbstractProvider $provider, Curl $client)
    {
        $this->provider = $provider;
        parent::__construct($entrypoint, $client);
    }

    public function setCacheRepository(Repository  $cache): OAuth2
    {
        $this->cache = $cache;
        return $this;
    }

    public function setGrant($grant): OAuth2
    {
        $this->grant = $grant;
        return $this;
    }

    public function setOptions(array $options): OAuth2
    {
        $this->options = $options;
        return $this;
    }

    public function getCacheKey(): string
    {
        return static::class;
    }

    /**
     * @param string $endpoint
     * @return mixed|null
     * @throws IdentityProviderException
     */
    public function rawRequest($endpoint, array $data = [], $method = 'get')
    {
        $this->getClient()->setHeader('Authorization', 'Bearer '.$this->getToken());
        return parent::rawRequest($endpoint, $data, $method);
    }

    /**
     * @throws IdentityProviderException
     */
    protected function getToken(): string
    {
        if(!$this->cache){
            return $this->token = $this->provider->getAccessToken($this->grant, $this->options);
        }

        $oauth = $this->cache->rememberForever($this->getCacheKey(), function () {
            return $this->token = $this->provider->getAccessToken($this->grant, $this->options);
        });

        if (!$oauth->hasExpired()) {
            return $oauth->getToken();
        }

        $this->cache->forget($this->getCacheKey());

        return $this->getToken();
    }
}
