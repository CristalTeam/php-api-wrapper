<?php

namespace Cristal\ApiWrapper\Tests;

use Curl\Curl;
use Exception;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Cristal\ApiWrapper\Transports\Bearer;

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
     * @var MockInterface|Curl
     */
    protected $client;

    protected function setUp(): void
    {
        $this->jwt = 'jwt';
        $this->entrypoint = 'http://entrypoint/';
        $this->client = Mockery::mock(Curl::class);

        $this->client->shouldReceive('setHeader')->andReturn(null);
    }

    public function testCreateCurlTranportClassWorks(): void
    {
        $transport = new Bearer($this->jwt, $this->entrypoint, $this->client);
        self::assertInstanceOf(Bearer::class, $transport);
    }

    /**
     * @param $httpCode
     * @throws Exception
     */
    protected function httpStatusCode($httpCode): void
    {
        $curl = new Bearer($this->jwt, $this->entrypoint, $this->client);
        $this->client->shouldReceive('get')->andReturn(null);

        $this->expectException(Exception::class);
        $this->client->httpStatusCode = $httpCode;
        $curl->request('/some-endpoint');
    }

    /**
     * @throws Exception
     */
    public function testRequestWithHttpStatusCode404ThrowsAnException(): void
    {
        $this->httpStatusCode(404);
    }

    /**
     * @throws Exception
     */
    public function testRequestWithHttpStatusCode301ThrowsAnException(): void
    {
        $this->httpStatusCode(301);
    }

    /**
     * @throws Exception
     */
    public function testRequestWithHttpStatusCode500ThrowsAnException(): void
    {
        $this->httpStatusCode(500);
    }

    /**
     * @throws Exception
     */
    public function testRequestWithHttpStatusCode300ThrowsAnException(): void
    {
        $this->httpStatusCode(300);
    }

    /**
     * @throws Exception
     */
    public function testRequestWithHttpStatusCode418ThrowsAnException(): void
    {
        $this->httpStatusCode(418);
    }

    /**
     * @throws Exception
     */
    public function testRequestWithHttpStatusCode422ThrowsAnException(): void
    {
        $this->httpStatusCode(422);
    }

    /**
     * @param $httpCode
     * @throws Exception
     */
    protected function httpStatusCodeValidParseAndReturnJsonReponse($httpCode): void
    {
        $curl = new Bearer($this->jwt, $this->entrypoint, $this->client);
        $this->client->shouldReceive('get')->andReturn(null);

        $this->client->httpStatusCode = $httpCode;
        $this->client->rawResponse = '{"success":true}';
        self::assertArrayHasKey('success', $curl->request('/some-endpoint'));
    }

    /**
     * @throws Exception
     */
    public function testRequestWithHttpStatusCode200ParseAndReturnJsonResponse(): void
    {
        $this->httpStatusCodeValidParseAndReturnJsonReponse(200);
    }

    /**
     * @throws Exception
     */
    public function testRequestWithHttpStatusCode201ParseAndReturnJsonResponse(): void
    {
        $this->httpStatusCodeValidParseAndReturnJsonReponse(201);
    }

    /**
     * @throws Exception
     */
    public function testRequestWithHttpStatusCode204ParseAndReturnJsonResponse(): void
    {
        $this->httpStatusCodeValidParseAndReturnJsonReponse(204);
    }

    /**
     * @throws Exception
     */
    public function testGetMethodRequest(): void
    {
        $curl = new Bearer($this->jwt, $this->entrypoint, $this->client);
        $client = $this->client;
        $this->client->shouldReceive('get')->andReturnUsing(static function () use ($client) {
            $client->httpStatusCode = 200;
            $client->rawResponse = '{"success":true}';
        });
        self::assertArrayHasKey('success', $curl->request('/some-endpoint', [], 'get'));
    }

    /**
     * @throws Exception
     */
    public function testPostMethodRequest(): void
    {
        $curl = new Bearer($this->jwt, $this->entrypoint, $this->client);
        $client = $this->client;
        $this->client->shouldReceive('post')->andReturnUsing(static function () use ($client) {
            $client->httpStatusCode = 200;
            $client->rawResponse = '{"success":true}';
        });
        self::assertArrayHasKey('success', $curl->request('/some-endpoint', [], 'post'));
    }

    /**
     * @throws Exception
     */
    public function testPutMethodRequest(): void
    {
        $curl = new Bearer($this->jwt, $this->entrypoint, $this->client);
        $client = $this->client;
        $this->client->shouldReceive('put')->andReturnUsing(static function () use ($client) {
            $client->httpStatusCode = 200;
            $client->rawResponse = '{"success":true}';
        });
        self::assertArrayHasKey('success', $curl->request('/some-endpoint', [], 'put'));
    }

    /**
     * @throws Exception
     */
    public function testDeleteMethodRequest(): void
    {
        $curl = new Bearer($this->jwt, $this->entrypoint, $this->client);
        $client = $this->client;
        $this->client->shouldReceive('delete')->andReturnUsing(static function () use ($client) {
            $client->httpStatusCode = 200;
            $client->rawResponse = '{"success":true}';
        });
        self::assertArrayHasKey('success', $curl->request('/some-endpoint', [], 'delete'));
    }

    /**
     * @throws Exception
     */
    public function testUnknownMethodRequestThrowsAnException(): void
    {
        $this->expectException(Exception::class);
        $curl = new Bearer($this->jwt, $this->entrypoint, $this->client);
        $client = $this->client;
        $this->client->shouldReceive('foo')->andReturnUsing(static function () use ($client) {
            $client->httpStatusCode = 200;
        });
        $curl->request('/some-endpoint', [], 'foo');
    }
}
