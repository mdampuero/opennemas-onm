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

use Api\EventSubscriber\UserGroupSubscriber;

/**
 * Defines test cases for UserGroupSubscriber class.
 */
class UserGroupSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->service = $this->getMockBuilder('Api\Service\V1\RedisService')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteItemByPattern' ])
            ->getMock();

        $this->subscriber = new UserGroupSubscriber($this->service);
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(UserGroupSubscriber::getSubscribedEvents());
    }

    /**
     * Tests onUserDelete.
     */
    public function testOnUserDelete()
    {
        $subscriber = $this->getMockBuilder('Api\EventSubscriber\UserGroupSubscriber')
            ->setConstructorArgs([ $this->service ])
            ->setMethods([ 'onUserGroupUpdate' ])
            ->getMock();

        $subscriber->expects($this->once())->method('onUserGroupUpdate');

        $subscriber->onUserGroupDelete();
    }

    /**
     * Tests onUserUpdate when only an user was updated.
     */
    public function testOnUserUpdateForUser()
    {
        $this->service->expects($this->once())->method('deleteItemByPattern')
            ->with('user-*');

        $this->subscriber->onUserGroupUpdate();
    }
}
