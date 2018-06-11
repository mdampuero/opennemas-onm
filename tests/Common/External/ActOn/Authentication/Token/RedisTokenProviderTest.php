<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\External\ActOn\Authentication\Token;

use Common\External\ActOn\Authentication\Token\RedisTokenProvider;

/**
 * Defines test cases for RedisTokenProvider class.
 */
class RedisTokenProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Connection')
            ->setMethods([ 'exists', 'get', 'set' ])
            ->getMock();

        $this->provider = new RedisTokenProvider($this->conn);
    }

    /**
     * Tests getAccessToken.
     */
    public function testGetAccessToken()
    {
        $this->conn->expects($this->at(0))->method('get')
            ->with('acton-access-token')->willReturn(true);
        $this->conn->expects($this->at(1))->method('get')
            ->with('acton-access-token')->willReturn(false);

        $this->assertTrue($this->provider->getAccessToken());
        $this->assertFalse($this->provider->getAccessToken());
    }

    /**
     * Tests getRefreshToken.
     */
    public function testGetRefreshToken()
    {
        $this->conn->expects($this->at(0))->method('get')
            ->with('acton-refresh-token')->willReturn(true);
        $this->conn->expects($this->at(1))->method('get')
            ->with('acton-refresh-token')->willReturn(false);

        $this->assertTrue($this->provider->getRefreshToken());
        $this->assertFalse($this->provider->getRefreshToken());
    }

    /**
     * Tests hasAccessToken.
     */
    public function testHasAccessToken()
    {
        $this->conn->expects($this->at(0))->method('exists')
            ->with('acton-access-token')->willReturn(true);
        $this->conn->expects($this->at(1))->method('exists')
            ->with('acton-access-token')->willReturn(false);

        $this->assertTrue($this->provider->hasAccessToken());
        $this->assertFalse($this->provider->hasAccessToken());
    }

    /**
     * Tests hasRefreshToken.
     */
    public function testHasRefreshToken()
    {
        $this->conn->expects($this->at(0))->method('exists')
            ->with('acton-refresh-token')->willReturn(true);
        $this->conn->expects($this->at(1))->method('exists')
            ->with('acton-refresh-token')->willReturn(false);

        $this->assertTrue($this->provider->hasRefreshToken());
        $this->assertFalse($this->provider->hasRefreshToken());
    }

    /**
     * Tests setAccessToken.
     */
    public function testSetAccessToken()
    {
        $this->conn->expects($this->once())->method('set')
            ->with('acton-access-token', 'frog', 1200);

        $this->provider->setAccessToken('frog', 1200);
    }

    /**
     * Tests setRefreshToken.
     */
    public function testSetRefreshToken()
    {
        $this->conn->expects($this->once())->method('set')
            ->with('acton-refresh-token', 'bar');

        $this->provider->setRefreshToken('bar');
    }
}
