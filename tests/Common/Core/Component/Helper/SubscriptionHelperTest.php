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
            ->setMethods([ 'getUser', 'hasExtension', 'hasPermission' ])
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

        $this->assertEquals('000010010000', $this->helper->getToken($this->content));

        $this->content->subscriptions = [ 2 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')
            ->willReturn([ 'items' => [ $subscriptions[2] ], 'total' => 1 ]);

        $this->assertEquals('001000100000', $this->helper->getToken($this->content));

        $this->content->subscriptions = [ 1, 2 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')->willReturn([
            'items' => [ $subscriptions[1], $subscriptions[2] ],
            'total' => 2
        ]);

        $this->assertEquals('001010110000', $this->helper->getToken($this->content));
    }

    /**
     * Tests getToken when user is subscribed to the content.
     */
    public function testGetTokenWhenSubscribed()
    {
        $this->user->user_groups = [
            1 => [ 'user_group_id' => 1, 'expires' => null, 'status' => 1 ],
            2 => [ 'user_group_id' => 2, 'expires' => null, 'status' => 1 ],
            3 => [ 'user_group_id' => 3, 'expires' => null, 'status' => 1 ]
        ];

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
     * Tests hasAdvertisements.
     */
    public function testHasAdvertisements()
    {
        $this->security->expects($this->at(0))->method('hasPermission')
            ->with('MEMBER_HIDE_ADVERTISEMENTS')->willReturn(true);
        $this->security->expects($this->at(1))->method('hasPermission')
            ->with('MEMBER_HIDE_ADVERTISEMENTS')->willReturn(false);

        $this->assertFalse($this->helper->hasAdvertisements());
        $this->assertTrue($this->helper->hasAdvertisements());
    }

    /**
     * Tests isBlocked for subscribed and non-subscribed users with valid and
     * invalid actions.
     */
    public function testIsBlocked()
    {
        $this->assertFalse($this->helper->isBlocked(null, 'wibble'));
        $this->assertFalse($this->helper->isBlocked(0, 'wibble'));

        $this->assertFalse($this->helper->isBlocked('000', 'wibble'));
        $this->assertFalse($this->helper->isBlocked('000', 'browser'));
        $this->assertTrue($this->helper->isBlocked('001', 'browser'));
        $this->assertTrue($this->helper->isBlocked('111', 'browser'));

        $this->assertFalse($this->helper->isBlocked('000000000000', 'wibble'));
        $this->assertFalse($this->helper->isBlocked('000000000000', 'browser'));
        $this->assertTrue($this->helper->isBlocked('000000000001', 'browser'));
        $this->assertTrue($this->helper->isBlocked('001001001001', 'browser'));
    }

    /**
     * Tests isHidden for subscribed and non-subscribed users with valid and
     * invalid actions.
     */
    public function testIsHidden()
    {
        $this->assertFalse($this->helper->isHidden(null, 'wibble'));
        $this->assertFalse($this->helper->isHidden(0, 'wibble'));

        $this->assertFalse($this->helper->isHidden('000', 'wibble'));
        $this->assertFalse($this->helper->isHidden('000', 'print'));
        $this->assertTrue($this->helper->isHidden('101', 'print'));

        $this->assertFalse($this->helper->isHidden('000000000000', 'wibble'));
        $this->assertTrue($this->helper->isHidden('000001000001', 'media'));
        $this->assertTrue($this->helper->isHidden('000000001001', 'tags'));
    }

    /**
     * Tests isRedirected for subscribed and non-subscribed users with valid and
     * invalid actions.
     */
    public function testIsRedirected()
    {
        $this->assertFalse($this->helper->isRedirected(null, 'wibble'));
        $this->assertFalse($this->helper->isRedirected(0, 'wibble'));

        $this->assertFalse($this->helper->isRedirected('000'));
        $this->assertFalse($this->helper->isRedirected('000'));
        $this->assertFalse($this->helper->isRedirected('101'));

        $this->assertFalse($this->helper->isRedirected('000000000000'));
        $this->assertTrue($this->helper->isRedirected('100001000001'));
        $this->assertTrue($this->helper->isRedirected('100000001002'));
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

        $this->user->user_groups = [
            1 => [ 'user_group_id' => 1, 'expires' => null, 'status' => 1 ],
        ];


        $this->assertTrue($this->helper->isSubscribed($this->content));

        $this->content->subscriptions = [ 1, 2 ];
        $this->user->user_groups      = [
            3 => [ 'user_group_id' => 3, 'expires' => null, 'status' => 1 ],
        ];
        $this->assertFalse($this->helper->isSubscribed($this->content));

        $this->content->subscriptions = [ 1, 2, 3 ];
        $this->user->user_groups      = [
            1 => [ 'user_group_id' => 1, 'expires' => null, 'status' => 1 ],
            2 => [ 'user_group_id' => 2, 'expires' => null, 'status' => 1 ],
        ];
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
