<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Varnish;

use Common\Core\Component\Varnish\Varnish;
use GuzzleHttp\Exception\TransferException;

/**
 * Defines test cases for Varnish class.
 */
class VarnishTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->client = $this->getMockBuilder('GuzzleHttp\Client')
            ->setMethods([ 'request' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Symfony\Bridge\Monolog\Logger')
            ->disableOriginalConstructor()
            ->setMethods([ 'info', 'error' ])
            ->getMock();

        $this->varnish = new Varnish($this->logger, [
            'header_name' => 'X-Plugh',
            'servers'     => [
                [ 'host' => 'garply', 'port' => 80 ],
                [ 'host' => 'qux', 'port' => 30253 ]
            ]
        ]);

        $property = new \ReflectionProperty($this->varnish, 'client');
        $property->setAccessible(true);

        $property->setValue($this->varnish, $this->client);
    }

    /**
     * Tests ban.
     */
    public function testban()
    {
        $response = $this->getMockBuilder('GuzzleHttp\Response')
            ->setMethods([ 'getReasonPhrase', 'getStatusCode' ])
            ->getMock();

        $response->expects($this->any())->method('getStatusCode')
            ->willReturn(200);
        $response->expects($this->any())->method('getReasonPhrase')
            ->willReturn('mumble');

        $this->client->expects($this->at(0))->method('request')
            ->with('BAN', 'http://garply:80')
            ->willReturn($response);
        $this->client->expects($this->at(1))->method('request')
            ->with('BAN', 'http://qux:30253')
            ->willReturn($response);

        $this->logger->expects($this->exactly(2))->method('info');

        $this->varnish->ban('obj.http.x-tags ~ garply');
    }

    /**
     * Tests ban when the request to the server throws an exception.
     */
    public function testBanWhenException()
    {
        $response = $this->getMockBuilder('GuzzleHttp\Response')
            ->setMethods([ 'getReasonPhrase', 'getStatusCode' ])
            ->getMock();

        $this->client->expects($this->at(0))->method('request')
            ->with('BAN', 'http://garply:80')
            ->will($this->throwException(new TransferException()));

        $this->client->expects($this->at(1))->method('request')
            ->with('BAN', 'http://qux:30253')
            ->willReturn($response);

        $response->expects($this->any())->method('getStatusCode')
            ->willReturn(200);

        $response->expects($this->any())->method('getReasonPhrase')
            ->willReturn('mumble');

        $this->logger->expects($this->once())->method('error');

        $this->varnish->ban('obj.http.x-tags ~ garply');
    }

    /**
     * Tests parseResponse with multiple values
     */
    public function testParseResponse()
    {
        $method = new \ReflectionMethod($this->varnish, 'parseResponse');
        $method->setAccessible(true);

        $this->assertEquals('Unknown response', $method->invokeArgs($this->varnish, [
            'norf'
        ]));

        $this->assertEquals('Ban added', $method->invokeArgs($this->varnish, [
            'Ban added'
        ]));
    }
}
