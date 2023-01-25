<?php

namespace Tests\Api\Helper\Cache;

use Api\Helper\Cache\TagCacheHelper;
use Common\Model\Entity\Instance;
use Common\Model\Entity\Tag;
use Opennemas\Task\Component\Task\ServiceTask;

/**
 * Defines test cases for Defin class.
 */
class TagCacheHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'flob' ]);

        $this->queue = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->disableOriginalConstructor()
            ->setMethods([ 'push' ])
            ->getMock();

        $this->helper = new TagCacheHelper($this->instance, $this->queue);
    }
    /**
     * Tests deleteTagItem.
     */
    public function testDeleteItem()
    {
        $tag = new Tag([ 'id' => 3750 ]);

        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('core.template.cache', 'delete', [
                [ 'tag', 'show', 3750 ]
            ]));
        $this->queue->expects($this->at(1))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                'obj.http.x-tags ~ ^instance-flob,.*,tag,show,tag-3750'
            ]));

        $this->helper->deleteItem($tag);
    }

    /**
     * Tests deleteTagList.
     */
    public function testDeleteTagList()
    {
        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('core.template.cache', 'delete', [
                [ 'tag', 'list' ]
            ]));
        $this->queue->expects($this->at(1))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                'obj.http.x-tags ~ ^instance-flob,.*,tag,list'
            ]));

        $this->helper->deleteList();
    }
}
