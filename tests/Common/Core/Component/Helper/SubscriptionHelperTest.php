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

use Common\Core\Component\Helper\PermissionHelper;
use Common\Core\Component\Helper\SubscriptionHelper;
use Common\Model\Entity\User;
use Common\Model\Entity\UserGroup;

/**
 * Defines test cases for class class.
 */
class SubscriptionHelperTest extends \PHPUnit\Framework\TestCase
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
        $this->ph      = new PermissionHelper($this->security);
        $this->user    = new User([ 'user_groups' => [] ]);

        $this->helper = new SubscriptionHelper($this->ph, $this->security, $this->ss);
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
                'privileges'    => [ 234, 237 ]
            ]),
            new UserGroup([
                'pk_user_group' => 3,
                'privileges'    => [ 232, 236 ]
            ]),
        ];

        $this->content->subscriptions = [ 1 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')
            ->willReturn([ 'items' => [ $subscriptions[1] ], 'total' => 1 ]);

        $this->assertEquals('0000100100000', $this->helper->getToken($this->content));

        $this->content->subscriptions = [ 2 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')
            ->willReturn([ 'items' => [ $subscriptions[2] ], 'total' => 1 ]);

        $this->assertEquals('0010001000000', $this->helper->getToken($this->content));

        $this->content->subscriptions = [ 1, 2 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')->willReturn([
            'items' => [ $subscriptions[1], $subscriptions[2] ],
            'total' => 2
        ]);

        $this->assertEquals('0010101100000', $this->helper->getToken($this->content));
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
     * Tests hasAdvertisements with isHidden.
     */
    public function testHasAdvertisementsWithHidden()
    {
        $helper = $this->getMockBuilder('Common\Core\Component\Helper\SubscriptionHelper')
            ->setConstructorArgs([ $this->ph, $this->security, $this->ss ])
            ->setMethods([ 'isHidden' ])
            ->getMock();

        $this->security->expects($this->any())->method('hasPermission')
            ->with('MEMBER_HIDE_ADVERTISEMENTS')->willReturn(false);

        $helper->expects($this->at(0))->method('isHidden')
            ->with('foobar', 'ADVERTISEMENTS')
            ->willReturn(true);
        $helper->expects($this->at(1))->method('isHidden')
            ->with('foobar', 'ADVERTISEMENTS')
            ->willReturn(false);

        $this->assertFalse($helper->hasAdvertisements('foobar'));
        $this->assertTrue($helper->hasAdvertisements('foobar'));
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

        $this->assertFalse($this->helper->isBlocked('0000000000000', 'wibble'));
        $this->assertFalse($this->helper->isBlocked('0000000000000', 'browser'));
        $this->assertTrue($this->helper->isBlocked('0000000000010', 'browser'));
        $this->assertTrue($this->helper->isBlocked('0010010010010', 'browser'));
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

        $this->assertFalse($this->helper->isHidden('0000000000000', 'wibble'));
        $this->assertTrue($this->helper->isHidden('0000010000010', 'media'));
        $this->assertTrue($this->helper->isHidden('0000000010010', 'tags'));
    }

    /**
     * Tests isIndexable for subscribed and non-subscribed users.
     */
    public function testIsIndexable()
    {
        $this->assertTrue($this->helper->isIndexable(null));
        $this->assertTrue($this->helper->isIndexable(0));
        $this->assertTrue($this->helper->isIndexable('000'));
        $this->assertTrue($this->helper->isIndexable('000'));
        $this->assertTrue($this->helper->isIndexable('001'));
        $this->assertTrue($this->helper->isIndexable('111'));

        $this->assertTrue($this->helper->isIndexable('00000000000000'));
        $this->assertTrue($this->helper->isIndexable('00000000000000'));
        $this->assertFalse($this->helper->isIndexable('00000000000011'));
        $this->assertFalse($this->helper->isIndexable('00010010010011'));
    }

    /**
     * Tests isRestricted for content.
     */
    public function testIsRestricted()
    {
        $subscriptions = [
            new UserGroup([
                'pk_user_group' => 1,
                'privileges'    => [ 234, 237 ]
            ]),
            new UserGroup([
                'pk_user_group' => 2,
                'privileges'    => [ 240 ]
            ]),
        ];


        $this->content->subscriptions = [ 1 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')
            ->willReturn([ 'items' => [ $subscriptions[0] ], 'total' => 2 ]);

        $this->assertTrue($this->helper->isRestricted($this->content));

        $this->content->subscriptions = [ 2 ];
        $this->ss->expects($this->at(0))->method('getListbyIds')
            ->willReturn([ 'items' => [ $subscriptions[1] ], 'total' => 1 ]);

        $this->assertFalse($this->helper->isRestricted($this->content));
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

        $this->content->subscriptions = [ 1 ];
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

        $this->content->subscriptions = [ 1 ];
        $this->assertTrue($this->helper->requiresSubscription($this->content));
    }
}
