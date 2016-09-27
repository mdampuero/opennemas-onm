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
            ->setMethods([ 'get', 'set' ])
            ->getMock();

        $this->loader = $this->getMockBuilder('Loader')
            ->disableOriginalConstructor()
            ->setMethods([ 'load' ])
            ->getMock();

        $config = [
            'foo' => new Redis([
                'name'      => 'foo',
                'namespace' => 'foo',
                'server'    => 'localhost',
                'port'      => '1234'
            ])
        ];

        $this->loader->expects($this->any())->method('load')->willReturn($config);

        $this->container->expects($this->any())->method('get')
            ->willReturn($this->loader);

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
}
