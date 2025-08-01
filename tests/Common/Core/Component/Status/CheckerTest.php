<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Status;

use Common\Core\Component\Status\Checker;

/**
 * Defines test cases for Checker class.
 */
class CheckerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('CacheConnection')
            ->setMethods([ 'remove', 'get', 'getData', 'set' ])
            ->getMock();

        $this->conn = $this->getMockBuilder('DatabaseConnection')
            ->setMethods([ 'fetchAll', 'getData' ])
            ->getMock();

        $this->cacheManager = $this->getMockBuilder('CacheManager')
            ->setMethods([ 'getConnection' ])
            ->getMock();

        $this->entityManager = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getConnection' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->cacheManager->expects($this->any())->method('getConnection')
            ->willReturn($this->cache);

        $this->entityManager->expects($this->any())->method('getConnection')
            ->willReturn($this->conn);

        $this->container->expects($this->at(0))->method('get')
            ->with('cache.manager')
            ->willReturn($this->cacheManager);

        $this->container->expects($this->at(1))->method('get')
            ->with('orm.manager')
            ->willReturn($this->entityManager);

        $this->checker = new Checker($this->container);
    }

    /**
     * Tests checkCacheConnection when success and failure.
     */
    public function testCheckCacheConnection()
    {
        $this->cache->expects($this->at(1))->method('get')->willReturn('bar');
        $this->cache->expects($this->at(3))->method('get')->willReturn(false);

        $this->assertTrue($this->checker->checkCacheConnection());

        $this->cache->expects($this->at(1))->method('get')->willReturn('bar');
        $this->cache->expects($this->at(2))->method('remove')->willReturn(true);
        $this->cache->expects($this->at(3))->method('get')->willReturn('bar');
        $this->assertFalse($this->checker->checkCacheConnection());

        $this->cache->expects($this->any())->method('get')->will($this->throwException(new \Exception()));
        $this->assertFalse($this->checker->checkCacheConnection());
    }

    /**
     * Tests checkDatabaseConnection when success and failure.
     */
    public function testCheckDatabaseconnection()
    {
        $this->conn->expects($this->at(0))->method('fetchAll')->willReturn([ [ 'Variable_name' => 'version' ] ]);
        $this->assertTrue($this->checker->checkDatabaseConnection());

        $this->conn->expects($this->at(0))->method('fetchAll')->willReturn(null);
        $this->assertFalse($this->checker->checkDatabaseConnection());

        $this->conn->expects($this->at(0))->method('fetchAll')->will($this->throwException(new \Exception()));
        $this->assertFalse($this->checker->checkDatabaseConnection());
    }

    /**
     * Tests checkNfs
     */
    public function testCheckNfs()
    {
        $fs = $this->getMockBuilder('Filesystem')
            ->setMethods([ 'chmod', 'dumpFile', 'exists', 'mkdir', 'remove'])
            ->getMock();

        $property = new \ReflectionProperty($this->checker, 'fs');
        $property->setAccessible(true);
        $property->setvalue($this->checker, $fs);

        $this->assertTrue($this->checker->checkNfs());

        $fs->expects($this->any())->method('exists')->will($this->throwException(new \Exception()));
        $this->assertFalse($this->checker->checkNfs());

        $fs->expects($this->any())->method('mkdir')->will($this->throwException(new \Exception()));
        $this->assertFalse($this->checker->checkNfs());

        $fs->expects($this->any())->method('dumpFile')->will($this->throwException(new \Exception()));
        $this->assertFalse($this->checker->checkNfs());
    }

    /**
     * Tests getCacheConfiguration.
     */
    public function testGetCacheConfiguration()
    {
        $config = [
            'name'   => 'quux',
            'server' => '127.0.0.1',
            'port'   => '6379'
        ];

        $this->cache->expects($this->any())->method('getData')->willReturn($config);

        $this->assertEquals($config, $this->checker->getCacheConfiguration());
    }

    /**
     * Tests getDatabaseConfiguration.
     */
    public function testGetDatabaseConfiguration()
    {
        $config = [
            'name'   => 'quux',
            'server' => '127.0.0.1',
            'port'   => '6379'
        ];

        $this->conn->expects($this->any())->method('getData')->willReturn($config);

        $this->assertEquals($config, $this->checker->getDatabaseConfiguration());
    }
}
