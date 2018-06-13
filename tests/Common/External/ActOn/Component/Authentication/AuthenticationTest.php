<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\External\ActOn\Component\Authentication;

use Common\External\ActOn\Component\Authentication\Authentication;

/**
 * Defines test cases for Authentication class.
 */
class AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->client = $this->getMockBuilder('Client')
            ->setMethods([ 'post' ])
            ->getMock();

        $this->cp = $this->getMockBuilder('ConfigurationProvider')
            ->setMethods([ 'getConfiguration' ])
            ->getMock();

        $this->tp = $this->getMockBuilder('TokenProvider')
            ->setMethods([
                'getAccessToken', 'getRefreshToken',
                'hasAccessToken', 'hasRefreshToken',
                'setAccessToken', 'setRefreshToken'
            ])->getMock();

        $this->response = $this->getMockBuilder('Response')
            ->setMethods([ 'getBody' ])
            ->getMock();

        $this->auth = new Authentication($this->cp, $this->tp, $this->client, 'norf');
    }

    /**
     * Tests authenticate when request to Act-On API fails.
     *
     * @expectedException Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testAuthenticateWhenRequestFails()
    {
        $this->client->expects($this->once())->method('post')
            ->will($this->throwException(new \Exception));

        $this->auth->authenticate();
    }

    /**
     * Tests authenticate when response from Act-On is invalid.
     *
     * @expectedException Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testAuthenticateWhenResponseInvalid()
    {
        $this->client->expects($this->once())->method('post')
            ->willReturn($this->response);

        $this->response->expects($this->once())->method('getBody')
            ->willReturn(json_encode([]));

        $this->auth->authenticate();
    }

    /**
     * Tests authenticate when response from Act-On is valid.
     */
    public function testAuthenticateWhenResponseValid()
    {
        $this->client->expects($this->once())->method('post')
            ->willReturn($this->response);

        $this->response->expects($this->once())->method('getBody')
            ->willReturn(json_encode([
                'access_token'  => 'glorp',
                'expires_in'    => 1234,
                'refresh_token' => 'flob',
            ]));

        $this->tp->expects($this->once())->method('setAccessToken')
            ->with('glorp', 1234)->willReturn($this->tp);
        $this->tp->expects($this->once())->method('setRefreshToken')
            ->with('flob')->willReturn($this->tp);

        $this->auth->authenticate();
    }

    /**
     * Tests getToken when no token found.
     */
    public function testGetTokenWhenNoTokenFound()
    {
        $auth = $this->getMockBuilder('Common\External\ActOn\Component\Authentication\Authentication')
            ->setMethods([ 'authenticate' ])
            ->setConstructorArgs([ $this->cp, $this->tp, $this->client, 'norf' ])
            ->getMock();

        $auth->expects($this->once())->method('authenticate');

        $this->tp->expects($this->once())->method('hasAccessToken')
            ->willReturn(false);
        $this->tp->expects($this->once())->method('hasRefreshToken')
            ->willReturn(false);
        $this->tp->expects($this->once())->method('getAccessToken')
            ->willReturn('wubble');

        $this->assertEquals('wubble', $auth->getToken());
    }

    /**
     * Tests getToken when refresh token found.
     */
    public function testGetTokenWhenAccessTokenFound()
    {
        $this->tp->expects($this->any())->method('hasAccessToken')
            ->willReturn(true);
        $this->tp->expects($this->once())->method('getAccessToken')
            ->willReturn('wubble');

        $this->assertEquals('wubble', $this->auth->getToken());
    }

    /**
     * Tests getToken when refresh token found.
     */
    public function testGetTokenWhenRefreshTokenFound()
    {
        $auth = $this->getMockBuilder('Common\External\ActOn\Component\Authentication\Authentication')
            ->setMethods([ 'refreshToken' ])
            ->setConstructorArgs([ $this->cp, $this->tp, $this->client, 'norf' ])
            ->getMock();

        $auth->expects($this->once())->method('refreshToken');

        $this->tp->expects($this->once())->method('hasAccessToken')
            ->willReturn(false);
        $this->tp->expects($this->any())->method('hasRefreshToken')
            ->willReturn(true);
        $this->tp->expects($this->once())->method('getAccessToken')
            ->willReturn('wubble');

        $this->assertEquals('wubble', $auth->getToken());
    }

    /**
     * Tests authenticate when request to Act-On API fails.
     *
     * @expectedException Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testRefreshTokenWhenRequestFails()
    {
        $this->client->expects($this->once())->method('post')
            ->will($this->throwException(new \Exception));

        $this->auth->refreshToken();
    }

    /**
     * Tests authenticate when response from Act-On is invalid.
     *
     * @expectedException Common\External\ActOn\Component\Exception\ActOnException
     */
    public function testRefreshTokenWhenResponseInvalid()
    {
        $this->client->expects($this->once())->method('post')
            ->willReturn($this->response);

        $this->response->expects($this->once())->method('getBody')
            ->willReturn(json_encode([]));

        $this->auth->refreshToken();
    }

    /**
     * Tests authenticate when response from Act-On is valid.
     */
    public function testRefreshTokenWhenResponseValid()
    {
        $this->client->expects($this->once())->method('post')
            ->willReturn($this->response);

        $this->response->expects($this->once())->method('getBody')
            ->willReturn(json_encode([
                'access_token'  => 'glorp',
                'expires_in'    => 1234,
                'refresh_token' => 'flob',
            ]));

        $this->tp->expects($this->once())->method('setAccessToken')
            ->with('glorp', 1234)->willReturn($this->tp);

        $this->auth->refreshToken();
    }
}
