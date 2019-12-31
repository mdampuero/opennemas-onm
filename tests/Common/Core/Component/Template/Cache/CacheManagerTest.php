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
        $this->smarty = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'clearAllCache', 'getCacheDir', 'getCacheId' ])
            ->getMock();

        $this->templating = $this->getMockBuilder('Common\Core\Component\Template\TemplateFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'getBundleName', 'getTemplate' ])
            ->getMock();

        $this->smarty->expects($this->any())->method('getCacheDir')
            ->willReturn('/glork/quux/corge');

        $this->templating->expects($this->any())->method('getTemplate')
            ->willReturn($this->smarty);

        $this->manager = $this->getMockBuilder('Common\Core\Component\Template\Cache\CacheManager')
            ->setConstructorArgs([ $this->templating ])
            ->setMethods([ 'deleteFile', 'getFiles' ])
            ->getMock();
    }

    /**
     * Test __construct.
     */
    public function testConstruct()
    {
        $manager  = new CacheManager($this->templating);
        $template = new \ReflectionProperty($manager, 'template');

        $template->setAccessible(true);

        $this->assertInstanceOf(
            'Common\Core\Component\Template\TemplateFactory',
            $template->getValue($manager)
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
}
