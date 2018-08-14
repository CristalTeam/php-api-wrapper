<?php

namespace Cpro\ApiWrapper\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use Cpro\ApiWrapper\Transports\Curl;

class TransportCurlTest extends TestCase
{
    /**
     * @var string
     */
    protected $jwt;

    /**
     * @var string
     */
    protected $entrypoint;

    /**
     * @var \Mockery\MockInterface|\Curl\Curl
     */
    protected $client;

    protected function setUp()
    {
        $this->jwt = 'jwt';
        $this->entrypoint = 'http://entrypoint/';
        $this->client = Mockery::mock(\Curl\Curl::class);

        $this->client->shouldReceive('setHeader')->andReturn(null);
    }

    public function testCreateCurlTranportClass()
    {
        new Curl($this->jwt, $this->entrypoint, $this->client);
    }

    /**
     * @param $httpCode
     * @throws \Exception
     */
    protected function httpStatusCode($httpCode)
    {
        $curl = new Curl($this->jwt, $this->entrypoint, $this->client);
        $this->client->shouldReceive('get')->andReturn(null);

        $this->expectException(\Exception::class);
        $this->client->httpStatusCode = $httpCode;
        $curl->request('/some-endpoint');
    }

    /**
     * @throws \Exception
     */
    public function testRequestWithHttpStatusCode404ThrowsAnException()
    {
        $this->httpStatusCode(404);
    }

    /**
     * @throws \Exception
     */
    public function testRequestWithHttpStatusCode301ThrowsAnException()
    {
        $this->httpStatusCode(301);
    }

    /**
     * @throws \Exception
     */
    public function testRequestWithHttpStatusCode500ThrowsAnException()
    {
        $this->httpStatusCode(500);
    }

    /**
     * @throws \Exception
     */
    public function testRequestWithHttpStatusCode300ThrowsAnException()
    {
        $this->httpStatusCode(300);
    }

    /**
     * @throws \Exception
     */
    public function testRequestWithHttpStatusCode418ThrowsAnException()
    {
        $this->httpStatusCode(418);
    }

    /**
     * @throws \Exception
     */
    public function testRequestWithHttpStatusCode422ThrowsAnException()
    {
        $this->httpStatusCode(422);
    }

    /**
     * @param $httpCode
     * @throws \Exception
     */
    protected function httpStatusCodeValidParseAndReturnJsonReponse($httpCode)
    {
        $curl = new Curl($this->jwt, $this->entrypoint, $this->client);
        $this->client->shouldReceive('get')->andReturn(null);

        $this->client->httpStatusCode = $httpCode;
        $this->client->rawResponse = '{"success":true}';
        $this->assertArrayHasKey('success', $curl->request('/some-endpoint'));
    }

    /**
     * @throws \Exception
     */
    public function testRequestWithHttpStatusCode200ParseAndReturnJsonResponse()
    {
        $this->httpStatusCodeValidParseAndReturnJsonReponse(200);
    }

    /**
     * @throws \Exception
     */
    public function testRequestWithHttpStatusCode201ParseAndReturnJsonResponse()
    {
        $this->httpStatusCodeValidParseAndReturnJsonReponse(201);
    }

    /**
     * @throws \Exception
     */
    public function testRequestWithHttpStatusCode204ParseAndReturnJsonResponse()
    {
        $this->httpStatusCodeValidParseAndReturnJsonReponse(204);
    }

    /**
     * @throws \Exception
     */
    public function testGetMethodRequest()
    {
        $curl = new Curl($this->jwt, $this->entrypoint, $this->client);
        $client = $this->client;
        $this->client->shouldReceive('get')->andReturnUsing(function () use ($client) {
            $client->httpStatusCode = 200;
            $client->rawResponse = '{"success":true}';
        });
        $this->assertArrayHasKey('success', $curl->request('/some-endpoint', [], 'get'));
    }

    /**
     * @throws \Exception
     */
    public function testPostMethodRequest()
    {
        $curl = new Curl($this->jwt, $this->entrypoint, $this->client);
        $client = $this->client;
        $this->client->shouldReceive('post')->andReturnUsing(function () use ($client) {
            $client->httpStatusCode = 200;
            $client->rawResponse = '{"success":true}';
        });
        $this->assertArrayHasKey('success', $curl->request('/some-endpoint', [], 'post'));
    }

    /**
     * @throws \Exception
     */
    public function testPutMethodRequest()
    {
        $curl = new Curl($this->jwt, $this->entrypoint, $this->client);
        $client = $this->client;
        $this->client->shouldReceive('put')->andReturnUsing(function () use ($client) {
            $client->httpStatusCode = 200;
            $client->rawResponse = '{"success":true}';
        });
        $this->assertArrayHasKey('success', $curl->request('/some-endpoint', [], 'put'));
    }

    /**
     * @throws \Exception
     */
    public function testDeleteMethodRequest()
    {
        $curl = new Curl($this->jwt, $this->entrypoint, $this->client);
        $client = $this->client;
        $this->client->shouldReceive('delete')->andReturnUsing(function () use ($client) {
            $client->httpStatusCode = 200;
            $client->rawResponse = '{"success":true}';
        });
        $this->assertArrayHasKey('success', $curl->request('/some-endpoint', [], 'delete'));
    }

    /**
     * @throws \Exception
     */
    public function testUnknownMethodRequestThrowsAnException()
    {
        $this->expectException(\Exception::class);
        $curl = new Curl($this->jwt, $this->entrypoint, $this->client);
        $client = $this->client;
        $this->client->shouldReceive('azerty')->andReturnUsing(function () use ($client) {
            $client->httpStatusCode = 200;
        });
        $curl->request('/some-endpoint', [], 'azerty');
    }
}