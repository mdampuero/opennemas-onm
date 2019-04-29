<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\TemplateCacheHelper;
use Common\ORM\Entity\Category;

/**
 * Defines test cases for TemplateCacheHelper class.
 */
class TemplateCacheHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Common\Core\Component\Template\Cache\CacheManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'delete' ])
            ->getMock();

        $this->helper = new TemplateCacheHelper($this->cache);
    }

    /**
     * Tests deleteCategories.
     */
    public function testDeleteCategories()
    {
        $this->cache->expects($this->at(0))->method('delete')
            ->with('category', 'list', 2866);
        $this->cache->expects($this->at(1))->method('delete')
            ->with('category', 'list', 18701);

        $this->helper->deleteCategories([
            new Category([ 'pk_content_category' => 2866 ]),
            new Category([ 'pk_content_category' => 18701 ])
        ]);
    }

    /**
     * Tests deleteDynamicCss.
     */
    public function testDeleteDynamicCss()
    {
        $this->cache->expects($this->once())->method('delete')
            ->with('css', 'global');

        $this->helper->deleteDynamicCss();
    }
}
