<?php

namespace Tests\Api\Helper\Cache;

use Api\Helper\Cache\NewsstandCacheHelper;
use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;
use Opennemas\Task\Component\Task\ServiceTask;

/**
 * Defines test cases for NewsstandCacheHelper class.
 */
class NewsstandCacheHelperTest extends \PHPUnit\Framework\TestCase
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

        $this->helper = new NewsstandCacheHelper($this->instance, $this->queue);
    }

    /**
     * Tests deleteList.
     */
    public function testDeleteList()
    {
        $this->queue->expects($this->once())->method('push')
            ->with(new ServiceTask('core.template.cache', 'delete', [
                'newsstand', 'list'
            ]));

        $this->helper->deleteList();
    }
}
