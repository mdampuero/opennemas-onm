<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
        $this->instance = new Instance();

        $this->queue = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->disableOriginalConstructor()
            ->setMethods([ 'push' ])
            ->getMock();

        $this->helper = new UserCacheHelper($this->instance, $this->queue);
    }

    /**
     * Tests deleteItem.
     */
    public function testDeleteItem()
    {
        $user = new User([ 'id' => 552 ]);

        $this->queue->expects($this->once())->method('push')
            ->with(new ServiceTask('cache', 'delete', [ 'user-552' ]));

        $this->helper->deleteItem($user);
    }
}
