<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\External\ActOn\Endpoint;

use Common\External\ActOn\Endpoint\EmailCampaignEndpoint;

/**
 * Defines test cases for EmailCampaignEndpoint class.
 */
class EmailCampaignEndpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->client = $this->getMockBuilder('HTTPClient')
            ->getMock();

        $this->endpoint = $this->getMockBuilder('Common\External\ActOn\Endpoint\EmailCampaignEndpoint')
            ->setMethods([ 'post' ])
            ->setConstructorArgs([ $this->client ])
            ->getMock();

        $this->endpoint->setConfiguration([
            'actions' => [
                'create_message' => [
                    'parameters' => [
                        'required' => [ 'title', 'subject' ],
                        'optional' => [ 'body' ]
                    ]
                ],
                'send_message' => [
                    'parameters' => [
                        'required' => [ 'id' ],
                    ]
                ]
            ]
        ]);
    }

    /**
     *  Tests createMessage when invalid parameters provided.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCreateMessageWhenInvalidParameters()
    {
        $this->endpoint->createMessage(null);
    }

    /**
     * Tests createMessage when valid parameters provided.
     */
    public function testCreateMessageWhenValidParameters()
    {
        $params = [ 'title' => 'wubble', 'subject' => 'fred' ];

        $this->endpoint->expects($this->once())->method('post')
            ->with(array_merge([ 'type' => 'draft' ], $params))
            ->willReturn([ 'status' => 'success', 'id' => 1 ]);

        $this->assertEquals(1, $this->endpoint->createMessage($params));
    }

    /**
     *  Tests sendMessage when invalid parameters provided.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testSendMessageWhenInvalidParameters()
    {
        $this->endpoint->sendMessage(null);
    }

    /**
     * Tests sendMessage when valid parameters provided.
     */
    public function testSendMessageWhenValidParameters()
    {
        $params = [ 'id' => 1 ];

        $this->endpoint->expects($this->once())->method('post')
            ->with($params)
            ->willReturn([ 'status' => 'success' ]);

        $this->endpoint->sendMessage($params);
    }
}
