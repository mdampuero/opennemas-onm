<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\NewsAgency\Component\Synchronizer;

use Common\NewsAgency\Component\Synchronizer\Synchronizer;
use Common\ORM\Entity\Instance;

/**
 * Defines test cases for Synchronizer class.
 */
class SynchronizerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->instance = new Instance([ 'internal_name' => 'qux' ]);

        $this->file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContents', 'isWriteable' ])
            ->getMock();

        $this->finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->setMethods([ 'files', 'in', 'name' ])
            ->getMock();

        $this->fs = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->setMethods([ 'dumpFile', 'exists', 'mkdir', 'remove', 'touch' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->container->expects($this->any())->method('getParameter')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->synchronizer = $this->getMockBuilder('Common\NewsAgency\Component\Synchronizer\Synchronizer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getFile', 'getFinder' ])
            ->getMock();

        $this->synchronizer->expects($this->any())->method('getFinder')
            ->willReturn($this->finder);

        $property = new \ReflectionProperty($this->synchronizer, 'fs');
        $property->setAccessible(true);
        $property->setValue($this->synchronizer, $this->fs);
    }

    /**
     * Returns the mock basing on the requested service.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.instance':
                return $this->instance;

            case 'core.paths.cache':
                return '/plugh/corge';

            default:
                return null;
        }
    }

    /**
     * Tests getResourceStats.
     */
    public function testResourceStats()
    {
        $this->assertIsArray($this->synchronizer->getResourceStats());
    }

    /**
     * Tests getServerStats when .sync file was found.
     */
    public function testGetServerStatsWhenFileFound()
    {
        $this->fs->expects($this->once())->method('exists')
            ->willReturn(true);

        $this->synchronizer->expects($this->once())->method('getFile')
            ->willReturn($this->file);

        $this->file->expects($this->once())->method('getContents')
            ->willReturn(serialize([ 'last_import' => 6765 ]));

        $params = $this->synchronizer->getServerStats();

        $this->assertIsArray($params);
        $this->assertEquals(6765, $params['last_import']);
    }

    /**
     * Tests getServerStats when .sync file was not found.
     */
    public function testGetServerStatsWhenNoFileFound()
    {
        $this->fs->expects($this->once())->method('exists')
            ->willReturn(false);


        $params = $this->synchronizer->getServerStats();

        $this->assertIsArray($params);
        $this->assertEmpty($params);
    }

    /**
     * Tests resetStats.
     */
    public function testResetStats()
    {
        $property = new \ReflectionProperty($this->synchronizer, 'stats');
        $property->setAccessible(true);

        $this->assertEquals($this->synchronizer, $this->synchronizer->resetStats());
        $this->assertEquals([
            'contents'   => 0,
            'deleted'    => 0,
            'downloaded' => 0,
            'parsed'     => 0,
            'invalid'    => 0,
            'valid'      => 0,
        ], $property->getValue($this->synchronizer));
    }

    /**
     * Tests isSyncEnvironmentReady.
     */
    public function testIsSyncEnvironmentReady()
    {
        $this->fs->expects($this->once())->method('exists')
            ->with('/plugh/corge/qux/importers')
            ->willReturn(false);

        $method = new \ReflectionMethod($this->synchronizer, 'isSyncEnvironmentReady');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->synchronizer, []));
    }

    /**
     * Tests lockSync.
     */
    public function testLockSync()
    {
        $this->fs->expects($this->once())->method('touch')
            ->with('/plugh/corge/qux/importers/.lock')
            ->willReturn(true);

        $method = new \ReflectionMethod($this->synchronizer, 'lockSync');
        $method->setAccessible(true);

        $method->invokeArgs($this->synchronizer, []);
    }

    /**
     * Tests setupSyncEnvironment.
     */
    public function testSetupSyncEnvironment()
    {
        $this->fs->expects($this->once())->method('exists')
            ->with('/plugh/corge/qux/importers')
            ->willReturn(false);
        $this->fs->expects($this->once())->method('mkdir')
            ->with('/plugh/corge/qux/importers');

        $method = new \ReflectionMethod($this->synchronizer, 'setupSyncEnvironment');
        $method->setAccessible(true);

        $method->invokeArgs($this->synchronizer, []);
    }

    /**
     * Tests unlockSync.
     */
    public function testUnlockSync()
    {
        $this->fs->expects($this->once())->method('exists')
            ->with('/plugh/corge/qux/importers/.lock')
            ->willReturn(true);
        $this->fs->expects($this->once())->method('remove')
            ->with('/plugh/corge/qux/importers/.lock');

        $method = new \ReflectionMethod($this->synchronizer, 'unlockSync');
        $method->setAccessible(true);

        $method->invokeArgs($this->synchronizer, []);
    }

    /**
     * Tests updateSyncFile.
     */
    public function testUpdateSyncFile()
    {
        $this->fs->expects($this->once())->method('dumpFile')
            ->with('/plugh/corge/qux/importers/.sync');

        $method = new \ReflectionMethod($this->synchronizer, 'updateSyncFile');
        $method->setAccessible(true);

        $method->invokeArgs($this->synchronizer, []);
    }
}
