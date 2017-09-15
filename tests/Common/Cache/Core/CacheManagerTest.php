<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core;

use Common\Cache\Redis\Redis;
use Common\Cache\Core\CacheManager;
use Common\Cache\Core\Exception\InvalidConnectionException;

/**
 * Defines test cases for CacheManager class.
 */
class CacheManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->disableOriginalConstructor()
            ->setMethods([ 'get', 'getParameter', 'set' ])
            ->getMock();

        $defaults = [ 'type' => 'redis' ];
        $config   = [
            'foo' => [
                'name'      => 'foo',
                'namespace' => 'foo',
                'server'    => 'localhost',
                'type'      => 'redis',
                'port'      => '1234'
            ]
        ];

        $this->container->expects($this->at(0))->method('getParameter')
            ->with('cache')
            ->willReturn($config);
        $this->container->expects($this->at(1))->method('getParameter')
            ->with('cache.default')
            ->willReturn($defaults);

        $this->cm = new CacheManager($this->container);
    }

    /**
     * Tests getConnection for an invalid connection name.
     *
     * @expectedException \Common\Cache\Core\Exception\InvalidConnectionException
     */
    public function testGetConnectionInvalid()
    {
        $this->cm->getConnection('Foobar');
    }

    /**
     * Tests getConnection for a valid connection name.
     */
    public function testGetConnectionValid()
    {
        $this->assertNotEmpty($this->cm->getConnection('foo'));
    }

    /**
     * Tests hasConnection for existing and unexisting cache connections.
     */
    public function testHasConnection()
    {
        $this->assertFalse($this->cm->hasConnection('Foobar'));
        $this->assertTrue($this->cm->hasConnection('foo'));
    }
}
