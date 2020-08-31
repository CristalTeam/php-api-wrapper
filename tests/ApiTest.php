<?php

namespace Cristal\ApiWrapper\Tests;

use Cristal\ApiWrapper\Api;
use Cristal\ApiWrapper\Transports\TransportInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use TypeError;

class ApiTest extends TestCase
{
    protected const ENDPOINTS = ['client', 'catalogue', 'materiel', 'fabricant', 'type', 'tarif', 'caracteristique'];
    protected $token;
    protected $entrypoint;

    protected const WITH_FILTER = ['with_filters'];
    protected const WITHOUT_FILTER = ['without_filters'];
    protected const ID_ENTITY = 123;

    protected function createFakeTransport()
    {
        $transport = Mockery::mock(TransportInterface::class);
        $transport->shouldReceive('request')->withArgs([Mockery::any(), self::WITH_FILTER])->andReturn(self::WITH_FILTER);
        $transport->shouldReceive('request')->withArgs([Mockery::any(), []])->andReturn(self::WITHOUT_FILTER);
        $transport->shouldReceive('request')->withArgs([Mockery::pattern('#/'.self::ID_ENTITY.'$#')])->andReturn(['entity']);

        return $transport;
    }

    public function setUp(): void
    {
        $this->token = 'token_jwt';
        $this->entrypoint = 'https://exemple/api/';
    }

    public function testApiWithoutTransport(): void
    {
        $this->expectException(TypeError::class);
        new Api(null);
    }

    public function testApiWithTransport(): void
    {
        $transport = Mockery::mock(TransportInterface::class);
        $api = new Api($transport);
        self::assertInstanceOf(TransportInterface::class, $api->getTransport());
    }

    public function testGetWithoutFilters(): void
    {
        $transport = $this->createFakeTransport();
        $api = new Api($transport);
        foreach (self::ENDPOINTS as $endpoint) {
            self::assertEquals(self::WITHOUT_FILTER, $api->{'get'.ucfirst($endpoint).'s'}());
        }
    }

    public function testGetWithFilters(): void
    {
        $transport = $this->createFakeTransport();
        $api = new Api($transport);
        foreach (self::ENDPOINTS as $endpoint) {
            self::assertEquals(self::WITH_FILTER, $api->{'get'.ucfirst($endpoint).'s'}(self::WITH_FILTER));
        }
    }

    public function testTryToGetSpecificEntity(): void
    {
        $transport = $this->createFakeTransport();
        $api = new Api($transport);
        foreach (self::ENDPOINTS as $endpoint) {
            $entity = $api->{'get'.ucfirst($endpoint)}(self::ID_ENTITY);
            self::assertIsArray($entity);
        }
    }
}
