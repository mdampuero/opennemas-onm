<?php

namespace Tests\Api\Helper\Cache;

use Api\Helper\Cache\CategoryCacheHelper;
use Common\Model\Entity\Category;
use Common\Model\Entity\Instance;
use Opennemas\Task\Component\Task\ServiceTask;

/**
 * Defines test cases for CategoryCacheHelper class.
 */
class CategoryCacheHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'bar' ]);

        $this->queue = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->disableOriginalConstructor()
            ->setMethods([ 'push' ])
            ->getMock();

        $this->helper = new CategoryCacheHelper($this->instance, $this->queue);
    }

    /**
     * Tests deleteContents.
     */
    public function testDeleteContents()
    {
        $ids = [ 'flob', 'xyzzy' ];

        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('cache.connection.instance', 'remove', [
                $ids
            ]))->willReturn($this->queue);
        $this->queue->expects($this->at(1))->method('push')
            ->with(new ServiceTask('cache', 'delete', [
                $ids
            ]))->willReturn($this->queue);

        $this->assertEquals($this->helper, $this->helper->deleteContents($ids));
    }

    /**
     * Tests deleteItem.
     */
    public function testDeleteItem()
    {
        $category = new Category([ 'id' => 203 ]);

        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('core.template.cache', 'delete', [
                [ 'category', 'list', 203 ]
            ]))->willReturn($this->queue);
        $this->queue->expects($this->at(1))->method('push')
            ->with(new ServiceTask('core.service.assetic.dynamic_css', 'deleteTimestamp', [
                '%global%'
            ]))->willReturn($this->queue);
        $this->queue->expects($this->at(2))->method('push')
            ->with(new ServiceTask('core.service.assetic.dynamic_css', 'deleteTimestamp', [
                203
            ]));

        $this->assertEquals($this->helper, $this->helper->deleteItem($category));
    }

    /**
     * Tests deleteItem.
     */
    public function testRemoveVarnishRssCache()
    {
        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', $this->instance->internal_name, 'rss-index')
            ]));

        $this->assertEquals($this->helper, $this->helper->removeVarnishRssCache());
    }
}
