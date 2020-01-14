<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Template\Cache;

use Common\Core\Component\Template\Cache\CacheManager;

/**
 * Defines test cases for CacheManager class.
 */
class CacheManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->factory = $this->getMockBuilder('Common\Core\Component\Template\TemplateFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->fs = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->disableOriginalConstructor()
            ->setMethods([ 'dumpFile', 'exists' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([
                'clearAllCache', 'clearCompiledTemplate', 'getCacheDir',
                'getCacheId'
            ])->getMock();

        $this->factory->expects($this->any())->method('get')
            ->with('frontend')->willReturn($this->smarty);

        $this->smarty->expects($this->any())->method('getCacheDir')
            ->willReturn('/glork/quux/corge');

        $this->manager = $this->getMockBuilder('Common\Core\Component\Template\Cache\CacheManager')
            ->setConstructorArgs([ $this->factory ])
            ->setMethods([ 'deleteFile', 'getFile', 'getFiles' ])
            ->getMock();

        $property = new \ReflectionProperty($this->manager, 'fs');
        $property->setAccessible(true);
        $property->setValue($this->manager, $this->fs);
    }

    /**
     * Test __construct.
     */
    public function testConstruct()
    {
        $manager = new CacheManager($this->factory);
        $factory = new \ReflectionProperty($manager, 'factory');

        $factory->setAccessible(true);

        $this->assertInstanceOf(
            'Common\Core\Component\Template\TemplateFactory',
            $factory->getValue($manager)
        );
    }

    /**
     * Tests delete.
     */
    public function testDelete()
    {
        $this->smarty->expects($this->once())->method('getCacheId')
            ->with('garply', 'plugh')->willReturn('garply|plugh');

        $this->manager->expects($this->once())->method('getFiles')
            ->with('/^garply\^plugh\^.*?/')
            ->willReturn([ '/glork/quux/corge/garply^plugh.tpl.php' ]);

        $this->manager->expects($this->once())->method('deleteFile')
            ->with('/glork/quux/corge/garply^plugh.tpl.php');

        $this->manager->delete('garply', 'plugh');
    }

    /**
     * Tests deleteAll.
     */
    public function testDeleteAll()
    {
        $this->smarty->expects($this->once())->method('clearAllCache');

        $this->manager->deleteAll();
    }

    /**
     * Tests deleteCompiles.
     */
    public function testDeleteCompiles()
    {
        $this->smarty->expects($this->once())->method('clearCompiledTemplate');

        $this->manager->deleteCompiles();
    }

    /**
     * Tests read.
     */
    public function testRead()
    {
        $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContents' ])
            ->getMock();

        $file->expects($this->once())->method('getContents')
            ->willReturn("[corge]\ncaching=1\ncache_lifetime=14336");

        $this->fs->expects($this->once())->method('exists')
            ->with('/cache.conf')->willReturn(true);

        $this->manager->expects($this->once())->method('getFile')
            ->with('/cache.conf')->willReturn($file);

        $config = $this->manager->read();

        $this->assertArrayHasKey('corge', $config);
        $this->assertEquals([
            'cache_lifetime' => 14336,
            'caching'        => 1,
        ], $config['corge']);
    }

    /**
     * Tests setPath.
     */
    public function testSetPath()
    {
        $property = new \ReflectionProperty($this->manager, 'path');
        $property->setAccessible(true);

        $this->assertEquals($this->manager, $this->manager->setPath('flob'));
        $this->assertEquals('flob', $property->getValue($this->manager));
    }

    /**
     * Tests write.
     */
    public function testWrite()
    {
        $this->fs->expects($this->once())->method('dumpFile')
            ->with('/cache.conf');

        $this->manager->write([ 'flob' => [
            'cache_lifetime' => 16405,
            'caching'        => 0,
        ],  'frontpages' => [
            'cache_lifetime' => 19179,
            'caching'        => 1,
        ] ]);
    }

    /**
     * Tests getFile.
     */
    public function testGetFile()
    {
        $manager = new CacheManager($this->factory);

        $method = new \ReflectionMethod($manager, 'getFile');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            '\Symfony\Component\Finder\SplFileInfo',
            $method->invokeArgs($manager, [ '/glork/mumble' ])
        );
    }
}
