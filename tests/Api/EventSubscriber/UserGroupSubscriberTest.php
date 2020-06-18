<?php

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
        $this->helper = $this->getMockBuilder('Api\Helper\Cache\UserGroupCacheHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteUsers' ])
            ->getMock();

        $this->subscriber = new UserGroupSubscriber($this->helper);
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(UserGroupSubscriber::getSubscribedEvents());
    }

    /**
     * Tests onUserGroupDelete.
     */
    public function testOnUserGroupDelete()
    {
        $subscriber = $this->getMockBuilder('Api\EventSubscriber\UserGroupSubscriber')
            ->setConstructorArgs([ $this->helper ])
            ->setMethods([ 'onUserGroupUpdate' ])
            ->getMock();

        $subscriber->expects($this->once())->method('onUserGroupUpdate');

        $subscriber->onUserGroupDelete();
    }

    /**
     * Tests onUserGroupUpdate.
     */
    public function testOnUserUpdateForUser()
    {
        $this->helper->expects($this->once())->method('deleteUsers');

        $this->subscriber->onUserGroupUpdate();
    }
}
