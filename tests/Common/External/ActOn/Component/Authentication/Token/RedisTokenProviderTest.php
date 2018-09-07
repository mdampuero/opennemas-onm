<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\External\ActOn\Component\Authentication\Token;

use Common\External\ActOn\Component\Authentication\Token\RedisTokenProvider;

/**
 * Defines test cases for RedisTokenProvider class.
 */
class RedisTokenProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Connection')
            ->setMethods([
                'exists', 'get', 'getNamespace', 'set', 'setNamespace'
            ])->getMock();

        $this->provider = new RedisTokenProvider($this->conn);

        $this->provider->setNamespace('frog');
    }

    /**
     * Tests getAccessToken when access token exists.
     */
    public function testGetAccessTokenWhenTokenExists()
    {
        $this->conn->expects($this->at(0))->method('getNamespace')
            ->willReturn('gorp');
        $this->conn->expects($this->at(1))->method('setNamespace')
            ->with('frog');
        $this->conn->expects($this->at(2))->method('get')
            ->with('acton-access-token')->willReturn(true);
        $this->conn->expects($this->at(3))->method('setNamespace')
            ->with('gorp');

        $this->assertTrue($this->provider->getAccessToken());
    }

    /**
     * Tests getAccessToken when access token does not exists.
     */
    public function testGetAccessTokenWhenTokenNotExists()
    {
        $this->conn->expects($this->at(0))->method('getNamespace')
            ->willReturn('gorp');
        $this->conn->expects($this->at(1))->method('setNamespace')
            ->with('frog');
        $this->conn->expects($this->at(2))->method('get')
            ->with('acton-access-token')->willReturn(false);
        $this->conn->expects($this->at(3))->method('setNamespace')
            ->with('gorp');

        $this->assertFalse($this->provider->getAccessToken());
    }

    /**
     * Tests getNamespace.
     */
    public function testGetNamespace()
    {
        $this->assertEquals('frog', $this->provider->getNamespace());
    }

    /**
     * Tests getRefreshToken when refresh token exists.
     */
    public function testGetRefreshTokenWhenTokenExists()
    {
        $this->conn->expects($this->at(0))->method('getNamespace')
            ->willReturn('gorp');
        $this->conn->expects($this->at(1))->method('setNamespace')
            ->with('frog');
        $this->conn->expects($this->at(2))->method('get')
            ->with('acton-refresh-token')->willReturn(true);
        $this->conn->expects($this->at(3))->method('setNamespace')
            ->with('gorp');

        $this->assertTrue($this->provider->getRefreshToken());
    }

    /**
     * Tests getRefreshToken when refresh token does not exists.
     */
    public function testGetRefreshTokenWhenTokenNotExists()
    {
        $this->conn->expects($this->at(0))->method('getNamespace')
            ->willReturn('gorp');
        $this->conn->expects($this->at(1))->method('setNamespace')
            ->with('frog');
        $this->conn->expects($this->at(2))->method('get')
            ->with('acton-refresh-token')->willReturn(false);
        $this->conn->expects($this->at(3))->method('setNamespace')
            ->with('gorp');

        $this->assertFalse($this->provider->getRefreshToken());
    }

    /**
     * Tests hasAccessToken when access token exists.
     */
    public function testHasAccessTokenWhenTokenExists()
    {
        $this->conn->expects($this->at(0))->method('getNamespace')
            ->willReturn('gorp');
        $this->conn->expects($this->at(1))->method('setNamespace')
            ->with('frog');
        $this->conn->expects($this->at(2))->method('exists')
            ->with('acton-access-token')->willReturn(true);
        $this->conn->expects($this->at(3))->method('setNamespace')
            ->with('gorp');

        $this->assertTrue($this->provider->hasAccessToken());
    }

    /**
     * Tests hasAccessToken when access token does not exists.
     */
    public function testHasAccessTokenWhenTokenNotExists()
    {
        $this->conn->expects($this->at(0))->method('getNamespace')
            ->willReturn('gorp');
        $this->conn->expects($this->at(1))->method('setNamespace')
            ->with('frog');
        $this->conn->expects($this->at(2))->method('exists')
            ->with('acton-access-token')->willReturn(false);
        $this->conn->expects($this->at(3))->method('setNamespace')
            ->with('gorp');

        $this->assertFalse($this->provider->hasAccessToken());
    }

    /**
     * Tests hasRefreshToken when refresh token exists.
     */
    public function testHasRefreshTokenWhenTokenExists()
    {
        $this->conn->expects($this->at(0))->method('getNamespace')
            ->willReturn('gorp');
        $this->conn->expects($this->at(1))->method('setNamespace')
            ->with('frog');
        $this->conn->expects($this->at(2))->method('exists')
            ->with('acton-refresh-token')->willReturn(true);
        $this->conn->expects($this->at(3))->method('setNamespace')
            ->with('gorp');

        $this->assertTrue($this->provider->hasRefreshToken());
    }

    /**
     * Tests hasRefreshToken when refresh token does not exists.
     */
    public function testHasRefreshTokenWhenTokenNotExists()
    {
        $this->conn->expects($this->at(0))->method('getNamespace')
            ->willReturn('gorp');
        $this->conn->expects($this->at(1))->method('setNamespace')
            ->with('frog');
        $this->conn->expects($this->at(2))->method('exists')
            ->with('acton-refresh-token')->willReturn(false);
        $this->conn->expects($this->at(3))->method('setNamespace')
            ->with('gorp');

        $this->assertFalse($this->provider->hasRefreshToken());
    }

    /**
     * Tests setAccessToken.
     */
    public function testSetAccessToken()
    {
        $this->conn->expects($this->at(0))->method('getNamespace')
            ->willReturn('gorp');
        $this->conn->expects($this->at(1))->method('setNamespace')
            ->with('frog');
        $this->conn->expects($this->at(2))->method('set')
            ->with('acton-access-token', 'frog', 1200);
        $this->conn->expects($this->at(3))->method('setNamespace')
            ->with('gorp');

        $this->provider->setAccessToken('frog', 1200);
    }

    /**
     * Tests setRefreshToken.
     */
    public function testSetRefreshToken()
    {
        $this->conn->expects($this->at(0))->method('getNamespace')
            ->willReturn('gorp');
        $this->conn->expects($this->at(1))->method('setNamespace')
            ->with('frog');
        $this->conn->expects($this->at(2))->method('set')
            ->with('acton-refresh-token', 'bar');
        $this->conn->expects($this->at(3))->method('setNamespace')
            ->with('gorp');

        $this->provider->setRefreshToken('bar');
    }
}
