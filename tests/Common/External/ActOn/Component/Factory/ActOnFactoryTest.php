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
            'config_provider' => 'config_provider',
            'http_client'     => 'http_client',
            'token_provider'  => 'token_provider',
            'url'             => 'baz',
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

        $this->tp = $this->getMockBuilder('TokenProvider')
            ->setMethods([ 'setNamespace' ])
            ->getMock();

        $this->factory = new ActOnFactory($this->container, $this->config);
    }

    /**
     * Tests getAuthentication.
     */
    public function testGetAuthentication()
    {
        $this->cp = $this->getMockBuilder('ConfigurationProvider')
            ->setMethods([ 'getConfiguration' ])
            ->getMock();

        $this->container->expects($this->at(0))->method('get')
            ->with('config_provider')->willReturn($this->cp);
        $this->container->expects($this->at(1))->method('get')
            ->with('token_provider')->willReturn($this->tp);
        $this->container->expects($this->at(2))->method('get')
            ->with('http_client');
        $this->tp->expects($this->once())->method('setNamespace');

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
        $this->assertInstanceOf('GuzzleHttp\Client', $this->factory->getClient());
    }

    /**
     * Tests getEndpoint.
     */
    public function testGetEndpoint()
    {
        $this->cp = $this->getMockBuilder('ConfigurationProvider')
            ->setMethods([ 'getConfiguration' ])
            ->getMock();

        $this->container->expects($this->at(0))->method('get')
            ->with('config_provider')->willReturn($this->cp);
        $this->container->expects($this->at(1))->method('get')
            ->with('token_provider')->willReturn($this->tp);
        $this->container->expects($this->at(2))->method('get')
            ->with('http_client')->willReturn('gorp');
        $this->container->expects($this->at(3))->method('get')
            ->willReturn('grault');
        $this->container->expects($this->at(4))->method('getParameter')
            ->willReturn('glork');

        $this->assertInstanceOf(
            'Common\External\ActOn\Component\Endpoint\EmailCampaignEndpoint',
            $this->factory->getEndpoint('email_campaign')
        );
    }
}
