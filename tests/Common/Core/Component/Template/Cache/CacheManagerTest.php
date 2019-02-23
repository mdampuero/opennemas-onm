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
        $this->finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->setMethods([ 'files', 'in', 'name' ])
            ->getMock();

        $this->fs = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->setMethods([ 'exists', 'remove' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'clearAllCache', 'getCacheDir', 'getCacheId' ])
            ->getMock();

        $this->templating = $this->getMockBuilder('Onm\Templating\Templating')
            ->disableOriginalConstructor()
            ->setMethods([ 'getTemplate' ])
            ->getMock();

        $this->smarty->expects($this->any())->method('getCacheDir')
            ->willReturn('/glork/quux/corge');

        $this->templating->expects($this->any())->method('getTemplate')
            ->willReturn($this->smarty);

        $this->manager = new CacheManager($this->templating);

        $finder = new \ReflectionProperty($this->manager, 'finder');
        $fs     = new \ReflectionProperty($this->manager, 'fs');

        $finder->setAccessible(true);
        $fs->setAccessible(true);

        $finder->setValue($this->manager, $this->finder);
        $fs->setValue($this->manager, $this->fs);
    }

    /**
     * Test __construct.
     */
    public function testConstruct()
    {
        $manager = new CacheManager($this->templating);

        $finder = new \ReflectionProperty($manager, 'finder');
        $fs     = new \ReflectionProperty($manager, 'fs');

        $finder->setAccessible(true);
        $fs->setAccessible(true);

        $this->assertInstanceOf('Symfony\Component\Finder\Finder', $finder->getValue($manager));
        $this->assertInstanceOf('Symfony\Component\Filesystem\Filesystem', $fs->getValue($manager));
    }

    /**
     * Tests delete.
     */
    public function testDelete()
    {
        $this->smarty->expects($this->once())->method('getCacheId')
            ->with('garply', 'plugh')->willReturn('garply|plugh');

        $this->finder->expects($this->once())->method('in')
            ->with('/glork/quux/corge')->willReturn($this->finder);
        $this->finder->expects($this->once())->method('name')
            ->with('/^garply\^plugh\^.*?/')->willReturn($this->finder);
        $this->finder->expects($this->once())->method('files')
            ->willReturn([ new \SplFileInfo('/glork/quux/corge/garply^plugh.tpl.php') ]);

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
     * Tests delete when the file exists.
     */
    public function testDeleteFileWhenFileExists()
    {
        $method = new \ReflectionMethod($this->manager, 'deleteFile');
        $method->setAccessible(true);

        $this->fs->expects($this->once())->method('exists')
            ->willReturn(true);
        $this->fs->expects($this->once())->method('remove')
            ->with('/glork/quux/corge/wubble^garply');

        $method->invokeArgs($this->manager, [ '/glork/quux/corge/wubble^garply' ]);
    }

    /**
     * Tests delete when the file not exists.
     */
    public function testDeleteFileWhenFileNotExists()
    {
        $method = new \ReflectionMethod($this->manager, 'deleteFile');
        $method->setAccessible(true);

        $this->fs->expects($this->once())->method('exists')
            ->willReturn(false);
        $this->fs->expects($this->never())->method('remove');

        $method->invokeArgs($this->manager, [ '/glork/quux/corge/wubble^garply' ]);
    }
}
