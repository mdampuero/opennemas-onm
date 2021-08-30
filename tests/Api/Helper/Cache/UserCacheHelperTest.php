<?php

namespace Tests\Api\Helper\Cache;

use Api\Helper\Cache\UserCacheHelper;
use Common\Model\Entity\Instance;
use Common\Model\Entity\User;
use Opennemas\Task\Component\Task\ServiceTask;

/**
 * Defines test cases for UserCacheHelper class.
 */
class UserCacheHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->instance = new Instance();

        $this->queue = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->disableOriginalConstructor()
            ->setMethods([ 'push' ])
            ->getMock();

        $this->helper = new UserCacheHelper($this->instance, $this->queue, $this->container);
    }

    /**
     * Tests deleteItem.
     */
    public function testDeleteItem()
    {
        $user = new User([ 'id' => 552, 'user_groups' => [1, 3] ]);

        $this->queue->expects($this->once())->method('push')
            ->with(new ServiceTask('cache', 'delete', [ 'user-552' ]));

        $this->helper->deleteItem($user);
    }
}
