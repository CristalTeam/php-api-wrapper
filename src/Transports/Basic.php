<?php

namespace Cpro\ApiWrapper\Transports;

use Curl\Curl as CurlClient;

class Basic extends Transport
{
    /**
     * Curl constructor.
     *
     * @param string     $username
     * @param string     $password
     * @param string     $entrypoint
     * @param CurlClient $client
     */
    public function __construct(string $username, string $password, string $entrypoint, CurlClient $client)
    {
        parent::__construct($entrypoint, $client);
        $this->getClient()->setBasicAuthentication($username, $password);
    }
}
