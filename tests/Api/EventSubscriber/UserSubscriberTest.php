<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Api\EventSubscriber;

use Api\EventSubscriber\UserSubscriber;
use Common\Model\Entity\Instance;
use Common\Model\Entity\User;

/**
 * Defines test cases for UserSubscriber class.
 */
class UserSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'flob' ]);

        $this->cache = $this->getMockBuilder('Cache' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->event = $this->getMockBuilder('Symfony\Component\EventDispatcher\Event')
            ->setMethods([ 'getArgument', 'hasArgument' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('Api\Helper\Cache\UserCacheHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteInstance', 'deleteItem' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([$this, 'serviceContainerCallback']));

        $this->subscriber = new UserSubscriber($this->container, $this->helper);
    }

    /**
     * Returns a mocked service basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'cache.connection.instance':
                return $this->cache;
        }

        return null;
    }

    /**
     * Tests logAction when there is no action.
     */
    public function testLogActionWhenNoAction()
    {
        $this->event->expects($this->once())->method('hasArgument')
            ->with('action')
            ->willReturn(false);

        $this->assertEmpty($this->subscriber->logAction($this->event));
    }

    /**
     * Tests logAction when empty items.
     */
    public function testLogActionWhenEmptyItems()
    {
        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('action')
            ->willReturn(true);

        $this->event->expects($this->at(1))->method('getArgument')
            ->with('action')
            ->willReturn('updateItem');

        $this->event->expects($this->at(2))->method('getArgument')
            ->with('item')
            ->willReturn([]);

        $this->subscriber->logAction($this->event);
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(UserSubscriber::getSubscribedEvents());
    }

    /**
     * Tests onUserDelete.
     */
    public function testOnUserDelete()
    {
        $subscriber = $this->getMockBuilder('Api\EventSubscriber\UserSubscriber')
            ->setConstructorArgs([ $this->container, $this->helper ])
            ->setMethods([ 'onUserUpdate' ])
            ->getMock();

        $subscriber->expects($this->once())->method('onUserUpdate');

        $subscriber->onUserDelete($this->event);
    }

    /**
     * Tests onUserUpdate when only an user was updated.
     */
    public function testOnUserUpdateForUser()
    {
        $user = new User([ 'id' => 3750 ]);

        $this->event->expects($this->any())->method('getArgument')
            ->with('item')->willReturn($user);

        $this->helper->expects($this->once())->method('deleteItem')
            ->with($user);
        $this->helper->expects($this->once())->method('deleteInstance');

        $this->subscriber->onUserUpdate($this->event);
    }

    /**
     * Tests onUserUpdate when more than one users were updated.
     */
    public function testOnUserUpdateForList()
    {
        $users = [
            new User([ 'id' => 3750 ]),
            new User([ 'id' => 1086 ])
        ];

        $this->event->expects($this->any())->method('getArgument')
            ->with('item')->willReturn($users);

        $this->helper->expects($this->at(0))->method('deleteItem')
            ->with($users[0]);
        $this->helper->expects($this->at(1))->method('deleteItem')
            ->with($users[1]);

        $this->helper->expects($this->at(2))->method('deleteInstance');

        $this->subscriber->onUserUpdate($this->event);
    }
}
