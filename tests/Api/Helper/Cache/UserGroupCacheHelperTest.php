<?php

namespace Tests\Api\Helper\Cache;

use Api\Helper\Cache\UserGroupCacheHelper;
use Common\Model\Entity\Instance;
use Opennemas\Task\Component\Task\ServiceTask;

/**
 * Defines test cases for UserGroupCacheHelper class.
 */
class UserGroupCacheHelperTest extends \PHPUnit\Framework\TestCase
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

        $this->helper = new UserGroupCacheHelper($this->instance, $this->queue);
    }

    /**
     * Tests deleteUsers.
     */
    public function testdeleteUsers()
    {
        $this->queue->expects($this->once())->method('push')
            ->with(new ServiceTask(
                'cache.connection.instance',
                'removeByPattern',
                [ 'user-*' ]
            ));

        $this->helper->deleteUsers();
    }
}
