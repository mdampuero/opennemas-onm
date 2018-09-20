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

use Common\External\ActOn\Component\Endpoint\EmailCampaignEndpoint;

/**
 * Defines test cases for EmailCampaignEndpoint class.
 */
class EmailCampaignEndpointTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->auth = $this->getMockBuilder('Authentication')
            ->setMethods([ 'getToken' ])
            ->getMock();

        $this->client = $this->getMockBuilder('HTTPClient')
            ->setMethods([ 'post' ])
            ->getMock();

        $this->endpoint = new EmailCampaignEndpoint($this->auth, $this->client, 'foo');

        $this->endpoint->setConfiguration([
            'actions' => [
                'create_message' => [
                    'path'       => '/message',
                    'parameters' => [
                        'required' => [ 'title', 'subject' ],
                        'optional' => [ 'body' ]
                    ]
                ],
                'send_message' => [
                    'path'       => '/message/{id}/send',
                    'parameters' => [
                        'required' => [ 'sendertoids' ],
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
     * Tests createMessage when the request fails.
     *
     * @expectedException Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testCreateMessageWhenRequestFails()
    {
        $params = [ 'title' => 'wubble', 'subject' => 'fred' ];

        $this->auth->expects($this->once())->method('getToken')
            ->willReturn('awlodwbobelgrop');
        $this->client->expects($this->once())->method('post')
            ->will($this->throwException(new \Exception()));

        $this->assertEquals(1, $this->endpoint->createMessage($params));
    }

    /**
     * Tests createMessage when the request fails.
     *
     * @expectedException Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testCreateMessageWhenResponseFails()
    {
        $params = [ 'title' => 'wubble', 'subject' => 'fred' ];

        $response = $this->getMockBuilder('Response')
            ->setMethods([ 'getBody' ])
            ->getMock();

        $response->expects($this->once())->method('getBody')
            ->willReturn(json_encode([ 'status' => 'failure' ]));

        $this->auth->expects($this->once())->method('getToken')
            ->willReturn('awlodwbobelgrop');
        $this->client->expects($this->once())->method('post')
            ->with('foo/message', [
                'headers' => [
                    'authorization' => 'Bearer awlodwbobelgrop'
                ],
                'form_params' => array_merge([ 'type' => 'draft' ], $params)
            ])->willReturn($response);

        $this->assertEquals(1, $this->endpoint->createMessage($params));
    }

    /**
     * Tests createMessage when valid parameters provided.
     */
    public function testCreateMessageWhenValidParameters()
    {
        $params = [ 'title' => 'wubble', 'subject' => 'fred' ];

        $response = $this->getMockBuilder('Response')
            ->setMethods([ 'getBody' ])
            ->getMock();

        $response->expects($this->once())->method('getBody')
            ->willReturn(json_encode([ 'status' => 'success', 'id' => 1 ]));

        $this->auth->expects($this->once())->method('getToken')
            ->willReturn('awlodwbobelgrop');
        $this->client->expects($this->once())->method('post')
            ->with('foo/message', [
                'headers' => [
                    'authorization' => 'Bearer awlodwbobelgrop'
                ],
                'form_params' => array_merge([ 'type' => 'draft' ], $params)
            ])->willReturn($response);

        $this->assertEquals(1, $this->endpoint->createMessage($params));
    }

    /**
     *  Tests sendMessage when invalid parameters provided.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testSendMessageWhenInvalidParameters()
    {
        $this->endpoint->sendMessage(1, null);
    }

    /**
     * Tests sendMessage when request fails.
     *
     * @expectedException Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testSendMessageWhenRequestFails()
    {
        $params = [ 'sendertoids' => '1' ];

        $this->auth->expects($this->once())->method('getToken')
            ->willReturn('awlodwbobelgrop');
        $this->client->expects($this->once())->method('post')
            ->will($this->throwException(new \Exception()));

        $this->endpoint->sendMessage(1, $params);
    }

    /**
     * Tests sendMessage when the response fails.
     *
     * @expectedException Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testSendMessageWhenResponseFails()
    {
        $params = [ 'sendertoids' => '1' ];

        $response = $this->getMockBuilder('Response')
            ->setMethods([ 'getBody' ])
            ->getMock();

        $response->expects($this->once())->method('getBody')
            ->willReturn(json_encode([ 'status' => 'failure' ]));

        $this->auth->expects($this->once())->method('getToken')
            ->willReturn('awlodwbobelgrop');
        $this->client->expects($this->once())->method('post')
            ->with('foo/message/1/send', [
                'headers' => [
                    'authorization' => 'Bearer awlodwbobelgrop'
                ],
                'form_params' => $params
            ])->willReturn($response);

        $this->endpoint->sendMessage(1, $params);
    }

    /**
     * Tests sendMessage when valid parameters provided.
     */
    public function testSendMessageWhenValidParameters()
    {
        $params = [ 'sendertoids' => '1' ];

        $response = $this->getMockBuilder('Response')
            ->setMethods([ 'getBody' ])
            ->getMock();

        $response->expects($this->once())->method('getBody')
            ->willReturn(json_encode([
                'status' => 'success', 'message' => 'norf'
            ]));

        $this->auth->expects($this->once())->method('getToken')
            ->willReturn('awlodwbobelgrop');
        $this->client->expects($this->once())->method('post')
            ->with('foo/message/1/send', [
                'headers' => [
                    'authorization' => 'Bearer awlodwbobelgrop'
                ],
                'form_params' => $params
            ])->willReturn($response);

        $this->assertEquals('norf', $this->endpoint->sendMessage(1, $params));
    }
}
