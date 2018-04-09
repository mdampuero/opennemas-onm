<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\SubscriptionHelper;
use Common\ORM\Entity\User;
use Common\ORM\Entity\UserGroup;

/**
 * Defines test cases for class class.
 */
class SubscriptionHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->security = $this->getMockBuilder('Security')
            ->setMethods([ 'getUser', 'hasExtension' ])
            ->getMock();

        $this->ss = $this->getMockBuilder('SubscriptionService')
            ->setMethods([ 'getListbyIds' ])
            ->getMock();

        $this->content = new \Content();
        $this->user    = new User([ 'user_groups' => [] ]);

        $this->helper = new SubscriptionHelper($this->security, $this->ss);
    }

    /**
     * Tests getToken when content has no subscriptions.
     */
    public function testGetTokenWhenNoSubscriptions()
    {
        $this->assertEmpty($this->helper->getToken($this->content));
    }

    /**
     * Tests getToken when user is not subscribed.
     */
    public function testGetTokenWhenNotSubscribed()
    {
        $subscriptions = [
            new UserGroup([
                'pk_user_group' => 1,
                'privileges'    => [ 1000 ]
            ]),
            new UserGroup([
                'pk_user_group' => 2,
                'privileges'    => [ 234, 237 ] ]),
            new UserGroup([
                'pk_user_group' => 3,
                'privileges'    => [ 232, 236 ]
            ]),
        ];

        $this->content->subscriptions = [ 1 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')
            ->willReturn([ 'items' => [ $subscriptions[1] ], 'total' => 1 ]);

        $this->assertEquals('00010010000', $this->helper->getToken($this->content));

        $this->content->subscriptions = [ 2 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')
            ->willReturn([ 'items' => [ $subscriptions[2] ], 'total' => 1 ]);

        $this->assertEquals('01000100000', $this->helper->getToken($this->content));

        $this->content->subscriptions = [ 1, 2 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')->willReturn([
            'items' => [ $subscriptions[1], $subscriptions[2] ],
            'total' => 2
        ]);

        $this->assertEquals('01010110000', $this->helper->getToken($this->content));
    }

    /**
     * Tests getToken when user is subscribed to the content.
     */
    public function testGetTokenWhenSubscribed()
    {
        $this->user->user_groups = [ 1, 2, 3 ];

        $this->security->expects($this->any())->method('getUser')
            ->willReturn($this->user);

        $subscriptions = [
            new UserGroup([
                'pk_user_group' => 1,
                'privileges'    => [ 1000 ]
            ]),
            new UserGroup([
                'pk_user_group' => 2,
                'privileges'    => [ 227 ] ]),
            new UserGroup([
                'pk_user_group' => 3,
                'privileges'    => [ 228, 229 ]
            ]),
        ];

        $this->content->subscriptions = [ 1 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')
            ->willReturn([ 'items' => [ $subscriptions[1] ], 'total' => 1 ]);

        $this->assertEquals('100', $this->helper->getToken($this->content));

        $this->content->subscriptions = [ 2 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')
            ->willReturn([ 'items' => [ $subscriptions[2] ], 'total' => 1 ]);

        $this->assertEquals('011', $this->helper->getToken($this->content));

        $this->content->subscriptions = [ 1, 2 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')->willReturn([
            'items' => [ $subscriptions[1], $subscriptions[2] ],
            'total' => 2
        ]);

        $this->assertEquals('111', $this->helper->getToken($this->content));
    }

    /**
     * Test isSubscribed when an user is logged in the system.
     */
    public function testIsSubscribed()
    {
        $this->security->expects($this->any())->method('getUser')
            ->willReturn($this->user);

        $this->content->subscriptions = [ 1 ];

        $this->assertFalse($this->helper->isSubscribed($this->content));

        $this->user->user_groups = [ 1 ];
        $this->assertTrue($this->helper->isSubscribed($this->content));

        $this->content->subscriptions = [ 1, 2 ];
        $this->user->user_groups      = [ 3 ];
        $this->assertFalse($this->helper->isSubscribed($this->content));

        $this->content->subscriptions = [ 1, 2, 3 ];
        $this->user->user_groups      = [ 1, 2 ];
        $this->assertTrue($this->helper->isSubscribed($this->content));
    }

    /**
     * Test isSubscribed when no user logged in the system.
     */
    public function testIsSubscribedWhenNoUser()
    {
        $this->security->expects($this->any())->method('getUser')
            ->willReturn(null);

        $this->assertFalse($this->helper->isSubscribed($this->content));

        $this->content->subscriptions = [ 1 ];
        $this->assertFalse($this->helper->isSubscribed($this->content));
    }

    /**
     * Tests requiresSubscription when subscriptions extension is disabled.
     */
    public function testRequiresSubscriptionWhenExtensionDisabled()
    {
        $this->security->expects($this->any())->method('hasExtension')
            ->with('CONTENT_SUBSCRIPTIONS')->willReturn(false);

        $this->content = new \Content();

        $this->assertFalse($this->helper->requiresSubscription($this->content));

        $this->content->subscription = [ 1 ];
        $this->assertFalse($this->helper->requiresSubscription($this->content));
    }

    /**
     * Tests requiresSubscription when subscriptions extension is enabled.
     */
    public function testRequiresSubscriptionWhenExtensionEnabled()
    {
        $this->security->expects($this->any())->method('hasExtension')
            ->with('CONTENT_SUBSCRIPTIONS')->willReturn(true);

        $this->content = new \Content();

        $this->assertFalse($this->helper->requiresSubscription($this->content));

        $this->content->subscription = [ 1 ];
        $this->assertTrue($this->helper->requiresSubscription($this->content));
    }
}
