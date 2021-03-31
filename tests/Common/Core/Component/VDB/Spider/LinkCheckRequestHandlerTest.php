<?php

namespace Tests\Common\Core\Component\VDB\Spider;

use PHPUnit\Framework\TestCase;
use VDB\Spider\Resource;

class LinkCheckRequestHandlerTest extends TestCase
{
    public function setUp()
    {
        $this->discoveredUri = $this->getMockBuilder('VDB\Spider\Uri\DiscoveredUri')
            ->disableOriginalConstructor()
            ->setMethods([ 'toString '])
            ->getMock();

        $this->linkCheckRequestHandler = $this
            ->getMockBuilder('Common\Core\Component\VDB\Spider\LinkCheckRequestHandler')
            ->disableOriginalConstructor()
            ->setMethods([ 'getClient' ])
            ->getMock();

        $this->client = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->linkCheckRequestHandler->expects($this->any())->method('getClient')
            ->willReturn($this->client);

        $this->client->expects($this->any())->method('get')->willReturn($this->response);
    }

    /**
     * Tests method request.
     */
    public function testRequest()
    {
        $resource = new Resource($this->discoveredUri, $this->response);

        $this->assertEquals($resource, $this->linkCheckRequestHandler->request($this->discoveredUri));
    }

    /**
     * Tests getters methods.
     */
    public function testGetters()
    {
        $domain = 'foo/bar';

        $this->linkCheckRequestHandler->setDomain($domain);
        $this->assertEquals($domain, $this->linkCheckRequestHandler->getDomain());
    }
}
