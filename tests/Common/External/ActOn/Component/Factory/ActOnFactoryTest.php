<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\External\ActOn\Factory;

use Common\External\ActOn\Component\Endpoint\EmailCampaignEndpoint;
use Common\External\ActOn\Component\Factory\ActOnFactory;

/**
 * Defines test cases for EndpointFactory class.
 */
class ActOnFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->config = [
            'config_provider' => 'mumble',
            'http_client'     => 'corge',
            'token_provider'  => 'garply',
            'endpoints'       => [
                'email_campaign' => [
                    'class'  => 'Common\External\ActOn\Component\Endpoint\EmailCampaignEndpoint',
                    'args'   => [ '@gorp', '%glork%' ],
                    'config' => [
                        'actions' => [
                            'quux_action' => [
                                'parameters' => [
                                    'required' => [ 'subject', 'title' ],
                                    'optional' => [ 'htmlbody' ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->factory = new ActOnFactory($this->container, $this->config);
    }

    /**
     * Tests getAuthentication.
     */
    public function testGetAuthentication()
    {
        $this->container->expects($this->at(0))->method('get')
            ->with('corge');
        $this->container->expects($this->at(1))->method('get')
            ->with('mumble');
        $this->container->expects($this->at(2))->method('get')
            ->with('garply');

        $this->assertInstanceOf(
            'Common\External\ActOn\Component\Authentication\Authentication',
            $this->factory->getAuthentication()
        );
    }

    /**
     * Tests getClient.
     */
    public function testGetClient()
    {
        $method = new \ReflectionMethod($this->factory, 'getClient');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            'GuzzleHttp\Client',
            $method->invokeArgs($this->factory, [])
        );
    }

    /**
     * Tests getEndpoint.
     */
    public function testGetEndpoint()
    {
        $this->container->expects($this->any())->method('get')
            ->willReturn('gorp');
        $this->container->expects($this->any())->method('getParameter')
            ->willReturn('glork');

        $this->assertInstanceOf(
            'Common\External\ActOn\Component\Endpoint\EmailCampaignEndpoint',
            $this->factory->getEndpoint('email_campaign')
        );
    }
}
