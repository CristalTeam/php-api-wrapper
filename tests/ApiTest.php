<?php

namespace Cpro\ApiWrapper\Tests;

use Mockery;
use Cpro\ApiWrapper\TransportInterface;
use PHPUnit\Framework\TestCase;
use Cpro\ApiWrapper\Api;

class ApiTest extends TestCase
{
    const ENDPOINTS = ['client', 'catalogue', 'materiel', 'fabricant', 'type', 'tarif', 'caracteristique'];
    protected $token;
    protected $entrypoint;

    const WITH_FILTER = ['with_filters'];
    const WITHOUT_FILTER = ['without_filters'];
    const ID_ENTITY = 123;

    protected function createFakeTransport()
    {
        $transport = Mockery::mock(TransportInterface::class);
        $transport->shouldReceive('request')->withArgs([Mockery::any(), self::WITH_FILTER])->andReturn(self::WITH_FILTER);
        $transport->shouldReceive('request')->withArgs([Mockery::any(), []])->andReturn(self::WITHOUT_FILTER);
        $transport->shouldReceive('request')->withArgs([Mockery::pattern('#/'.self::ID_ENTITY.'$#')])->andReturn(['entity']);
        return $transport;
    }

    public function setUp()
    {
        $this->token = 'token_jwt';
        $this->entrypoint = 'https://exemple/api/';
    }

    public function testApiWithoutTransport()
    {
        $this->expectException(\TypeError::class);
        new Api(null);
    }

    public function testApiWithTransport()
    {
        $transport = Mockery::mock(TransportInterface::class);
        $api = new Api($transport);
        $this->assertInstanceOf(TransportInterface::class, $api->getTransport());
    }

    public function testGetWithoutFilters()
    {
        $transport = $this->createFakeTransport();
        $api = new Api($transport);
        foreach(self::ENDPOINTS as $endpoint){
            $this->assertEquals(self::WITHOUT_FILTER, $api->{'get'.ucfirst($endpoint).'s'}());
        }
    }

    public function testGetWithFilters()
    {
        $transport = $this->createFakeTransport();
        $api = new Api($transport);
        foreach(self::ENDPOINTS as $endpoint){
            $this->assertEquals(self::WITH_FILTER, $api->{'get'.ucfirst($endpoint).'s'}(self::WITH_FILTER));
        }
    }

    public function testTryToGetSpecificEntityWithoutIdAsArgument()
    {
        $this->expectException(\TypeError::class);
        $transport = $this->createFakeTransport();
        $api = new Api($transport);
        foreach(self::ENDPOINTS as $endpoint){
            $api->{'get'.ucfirst($endpoint)}();
        }
    }

    public function testTryToGetSpecificEntity()
    {
        $transport = $this->createFakeTransport();
        $api = new Api($transport);
        foreach(self::ENDPOINTS as $endpoint){
            $entity = $api->{'get'.ucfirst($endpoint)}(self::ID_ENTITY);
            $this->assertInternalType('array', $entity);
        }
    }
}