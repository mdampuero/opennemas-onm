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
            'token'     => 'mumblenorfthudwaldo',
            'endpoints' => [
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

        $this->container->expects($this->any())->method('get')
            ->willReturn('gorp');
        $this->container->expects($this->any())->method('getParameter')
            ->willReturn('glork');


        $this->factory = new ActOnFactory($this->container, $this->config);
    }

    /**
     * Tests getEndpoint.
     */
    public function testGetEndpoint()
    {
        $this->assertInstanceOf(
            'Common\External\ActOn\Component\Endpoint\EmailCampaignEndpoint',
            $this->factory->getEndpoint('email_campaign')
        );
    }
}
