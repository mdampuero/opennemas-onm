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

use \Common\External\ActOn\Component\Endpoint\ContactEndpoint;
use \Common\External\ActOn\Component\Exception\ActOnException;

/**
 * Defines test cases for ContactEndpoint class.
 */
class ContactEndpointTest extends \PHPUnit\Framework\TestCase
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
            ->setMethods([ 'post', 'get' ])
            ->getMock();

        $this->endpoint = new ContactEndpoint($this->auth, $this->client, 'foo');

        $this->endpoint->setConfiguration([
            'actions' => [
                'add_contact' => [
                    'path'       => '/list/{listId}/record',
                    'parameters' => [
                        'required' => [ 'contact' ]
                    ]
                ],
                'get_contact' => [
                    'path'       => '/list/lookup/{listId}',
                    'parameters' => [
                        'required' => [ 'email' ],
                    ]
                ]
            ]
        ]);
    }

    /**
     *  Tests addContact when invalid parameters provided.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAddContactWhenInvalidParameters()
    {
        $this->endpoint->addContact(1, null);
    }

    /**
     * Tests addContact when the request fails.
     *
     * @expectedException \Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testAddContactWhenRequestFails()
    {
        $params = [ 'contact' => '{ email: wubble, name: foobar }' ];

        $this->auth->expects($this->once())->method('getToken')
            ->willReturn('awlodwbobelgrop');
        $this->client->expects($this->once())->method('post')
            ->will($this->throwException(new \Exception()));

        $this->assertEquals('thud', $this->endpoint->addContact(1, $params));
    }

    /**
     * Tests addContact when the request fails.
     *
     * @expectedException \Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testAddContactWhenResponseFails()
    {
        $params = [ 'contact' => '{ email: wubble, name: foobar }' ];

        $response = $this->getMockBuilder('Response')
            ->setMethods([ 'getBody' ])
            ->getMock();

        $response->expects($this->once())->method('getBody')
            ->willReturn(json_encode([ 'status' => 'failure' ]));

        $this->auth->expects($this->once())->method('getToken')
            ->willReturn('awlodwbobelgrop');
        $this->client->expects($this->once())->method('post')
            ->with('foo/list/1/record', [
                'headers' => [
                    'authorization' => 'Bearer awlodwbobelgrop',
                    'content-type'  => 'application/json'
                ],
                'body' => $params['contact']
            ])->willReturn($response);

        $this->assertEquals('thud', $this->endpoint->addContact(1, $params));
    }

    /**
     * Tests addContact when valid parameters provided.
     */
    public function testAddContactWhenValidParameters()
    {
        $params = [ 'contact' => '{ email: wubble, name: foobar }' ];

        $response = $this->getMockBuilder('Response')
            ->setMethods([ 'getBody' ])
            ->getMock();

        $response->expects($this->once())->method('getBody')
            ->willReturn(json_encode([ 'status' => 'success', 'message' => 'thud' ]));

        $this->auth->expects($this->once())->method('getToken')
            ->willReturn('awlodwbobelgrop');
        $this->client->expects($this->once())->method('post')
            ->with('foo/list/1/record', [
                'headers' => [
                    'authorization' => 'Bearer awlodwbobelgrop',
                    'content-type'  => 'application/json'
                ],
                'body' => $params['contact']
            ])->willReturn($response);

        $this->assertEquals('thud', $this->endpoint->addContact(1, $params));
    }

    /**
     *  Tests getContact when invalid parameters provided.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testgetContactWhenInvalidParameters()
    {
        $this->endpoint->getContact(1, null);
    }

    /**
     * Tests getContact when request fails.
     *
     * @expectedException \Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testGetContactWhenRequestFails()
    {
        $params = [ 'email' => 'foo@bar.baz' ];

        $this->auth->expects($this->once())->method('getToken')
            ->willReturn('awlodwbobelgrop');
        $this->client->expects($this->once())->method('get')
            ->will($this->throwException(new \Exception()));

        $this->endpoint->getContact(1, $params);
    }

    /**
     * Tests getContact when the response fails.
     *
     * @expectedException \Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testGetContactWhenResponseFails()
    {
        $params = [ 'email' => 'foo@bar.baz' ];

        $response = $this->getMockBuilder('Response')
            ->setMethods([ 'getBody' ])
            ->getMock();

        $response->expects($this->once())->method('getBody')
            ->willReturn(json_encode([ 'status' => 'failure' ]));

        $this->auth->expects($this->once())->method('getToken')
            ->willReturn('awlodwbobelgrop');
        $this->client->expects($this->once())->method('get')
            ->with('foo/list/lookup/1', [
                'headers' => [
                    'authorization' => 'Bearer awlodwbobelgrop'
                ],
                'query' => $params
            ])->willReturn($response);

        $this->endpoint->getContact(1, $params);
    }

    /**
     * Tests getContact when valid parameters provided.
     */
    public function testgetContactWhenValidParameters()
    {
        $params = [ 'email' => 'foo@bar.baz' ];

        $response = $this->getMockBuilder('Response')
            ->setMethods([ 'getBody' ])
            ->getMock();

        $response->expects($this->once())->method('getBody')
            ->willReturn(json_encode([
                'contactID' => 'l-0001:1'
            ]));

        $this->auth->expects($this->once())->method('getToken')
            ->willReturn('awlodwbobelgrop');
        $this->client->expects($this->once())->method('get')
            ->with('foo/list/lookup/1', [
                'headers' => [
                    'authorization' => 'Bearer awlodwbobelgrop'
                ],
                'query' => $params
            ])->willReturn($response);

        $this->assertEquals(
            ['contactID' => 'l-0001:1'],
            $this->endpoint->getContact(1, $params)
        );
    }

    /**
     * Tests existContact when contact exists.
     */
    public function testExistContactWhenExists()
    {
        $email = 'foo@bar.baz';

        $contact = $this->getMockBuilder('Common\External\ActOn\Component\Endpoint\ContactEndpoint')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContact' ])
            ->getMock();

        $contact->expects($this->once())->method('getContact')
            ->with(1, [ 'email' => $email ])
            ->willReturn([ 'contactID' => 'l-0001:1' ]);

        $this->assertTrue($contact->existContact(1, $email));
    }

    /**
     * Tests existContact when error.
     *
     * @expectedException \Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testExistContactWhenError()
    {
        $email = 'foo@bar.baz';

        $contact = $this->getMockBuilder('Common\External\ActOn\Component\Endpoint\ContactEndpoint')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContact' ])
            ->getMock();

        $contact->expects($this->once())->method('getContact')
            ->with(1, [ 'email' => $email ])
            ->willThrowException(new ActOnException());

        $contact->existContact(1, $email);
    }

    /**
     * Tests existContact when error.
     *
     * @expectedException \Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testExistContactWhenResponseError()
    {
        $email = 'foo@bar.baz';

        $contact = $this->getMockBuilder('Common\External\ActOn\Component\Endpoint\ContactEndpoint')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContact' ])
            ->getMock();

        $contact->expects($this->once())->method('getContact')
            ->with(1, [ 'email' => $email ])
            ->willReturn([ 'status' => 'failure' ]);

        $contact->existContact(1, $email);
    }

    /**
     * Tests existContact when contact not exists.
     */
    public function testExistContactWhenNotExists()
    {
        $email = 'foo@bar.baz';

        $contact = $this->getMockBuilder('Common\External\ActOn\Component\Endpoint\ContactEndpoint')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContact' ])
            ->getMock();

        $contact->expects($this->once())->method('getContact')
            ->with(1, [ 'email' => $email ])
            ->willThrowException(new ActOnException('errorCode:10162'));

        $this->assertFalse($contact->existContact(1, $email));
    }
}
