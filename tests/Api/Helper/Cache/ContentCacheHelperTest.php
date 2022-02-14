<?php

namespace Tests\Api\Helper\Cache;

use Api\Helper\Cache\ContentCacheHelper;
use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;
use Opennemas\Task\Component\Task\ServiceTask;

/**
 * Defines test cases for ContentCacheHelper class.
 */
class ContentCacheHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance();

        $this->queue = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->disableOriginalConstructor()
            ->setMethods([ 'push' ])
            ->getMock();

        $this->cache = $this->getMockBuilder('Opennemas\Cache\Core\Cache')
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper = new ContentCacheHelper($this->instance, $this->queue, $this->cache);
    }

    /**
     * Tests deleteItem.
     */
    public function testDeleteItem()
    {
        $item = new Content([
            'content_type_name' => 'attachment',
            'path'              => '/flob/norf.pdf',
            'pk_content'        => 648
        ]);

        $this->queue->expects($this->at(0))->method('push')
            ->with(new ServiceTask('core.template.cache', 'delete', [
                'content', 648
            ]));

        $this->queue->expects($this->at(1))->method('push')
            ->with(new ServiceTask('core.varnish', 'ban', [
                'obj.http.x-tags ~ attachment-648'
            ]));

        $this->helper->deleteItem($item);
    }
}
