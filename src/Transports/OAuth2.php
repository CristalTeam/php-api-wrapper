<?php

namespace Cristal\ApiWrapper\Transports;

use Cristal\ApiWrapper\Transports\Transport as TransportCore;
use Curl\Curl;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Cache\CacheItemPoolInterface;

class OAuth2 extends TransportCore
{
    /**
     * @var AbstractProvider
     */
    private $provider;

    /**
     * @var CacheItemPoolInterface
     */
    private $cacheResolver;

    /**
     * @var string
     */
    private $cacheKey;

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

    public function setCacheResolver(CacheItemPoolInterface $cacheResolver, string $key): OAuth2
    {
        $this->cacheResolver = $cacheResolver;
        $this->cacheKey = $key;
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

    /**
     * @param string $endpoint
     * @return mixed|null
     * @throws IdentityProviderException
     */
    public function rawRequest($endpoint, array $data = [], $method = 'get')
    {
        $this->getClient()->setHeader('Authorization', 'Bearer '.$this->getToken());

        foreach($this->provider->getHeaders() as $name => $value) {
            $this->getClient()->setHeader($name, $value);
        }

        return parent::rawRequest($endpoint, $data, $method);
    }

    /**
     * @throws IdentityProviderException
     */
    protected function getToken(): string
    {
        $cacheItem = $this->cacheResolver->getItem($this->cacheKey);

        if ($cacheItem->isHit()) {
            $token = $cacheItem->get();
        } else {
            $token = $this->provider->getAccessToken($this->grant, $this->options);

            $cacheItem->set($token);
            $this->cacheResolver->save($cacheItem);
        }

        if ($token->hasExpired()) {
            $this->cacheResolver->deleteItem($this->cacheKey);
            return $this->getToken();
        }

        return $token->getToken();
    }
}
