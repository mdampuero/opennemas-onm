<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

class SubscriptionHelper
{
    /**
     * The Security service.
     *
     * @var Security
     */
    protected $security;

    /**
     * The Subscription service.
     *
     * @var SubscriptionService
     */
    protected $ss;

    /**
     * The list of permissions to compose the not-subscribed token.
     *
     * @var array
     */
    protected $notSubscribedPermissions = [
        'NON_MEMBER_HIDE_TITLE',
        'NON_MEMBER_HIDE_SUMMARY',
        'NON_MEMBER_HIDE_BODY',
        'NON_MEMBER_HIDE_PRETITLE',
        'NON_MEMBER_HIDE_MEDIA',
        'NON_MEMBER_HIDE_RELATED_CONTENTS',
        'NON_MEMBER_HIDE_INFO',
        'NON_MEMBER_HIDE_TAGS',
        'NON_MEMBER_HIDE_PRINT',
        'NON_MEMBER_HIDE_SOCIAL',
        'NON_MEMBER_BLOCK_BROWSER'
    ];

    /**
     * The list of permissions to compose the subscribed token.
     *
     * @var array
     */
    protected $subscribedPermissions = [
        'MEMBER_HIDE_PRINT',
        'MEMBER_HIDE_SOCIAL',
        'MEMBER_BLOCK_BROWSER',
    ];

    /**
     * Initializes the SubscriptionHelper.
     *
     * @param Security $security The Security service.
     */
    public function __construct($security, $ss)
    {
        $this->security = $security;
        $this->ss       = $ss;
    }

    /**
     * Returns the subscription token for the content.
     *
     * @param Content $content The content.
     *
     * @return strign The subscription token.
     */
    public function getSubscriptionToken($content)
    {
        if ($this->isSubscribed($content)) {
            return $this->getToken($content, $this->subscribedPermissions);
        }

        return $this->getToken($content, $this->notSubscribedPermissions);
    }

    /**
     * Checks if the current user is subscribed to the content.
     *
     * @param Content $content The content.
     *
     * @return boolean True if the user is subscribed to the content. False
     *                 otherwise.
     */
    public function isSubscribed($content)
    {
        if (empty($this->security->getUser())) {
            return false;
        }

        $subscriptions = array_intersect(
            $content->subscriptions,
            $this->security->getUser()->user_groups
        );

        return count($subscriptions) > 0;
    }

    /**
     * Checks if the current content requires a subscription.
     *
     * @param Content $content The content to check.
     *
     * @return boolean True if the content requires a subscription. False
     *                 otherwise.
     */
    public function requiresSubscription($content)
    {
        if (!$this->security->hasExtension('CONTENT_SUBSCRIPTIONS')) {
            return false;
        }

        return !empty($content->subscription);
    }

    /**
     * Returns the list of permissions for a content basing on the selected
     * subscriptions.
     *
     * @param Content $content The content.
     *
     * @return array The list of permissions.
     */
    protected function getPermissions($content)
    {
        $response    = $this->ss->getListByIds($content->subscriptions);
        $permissions = [];

        foreach ($response['items'] as $subscription) {
            $permissions = array_merge(
                $permissions,
                \Privilege::getNames($subscription->privileges)
            );
        }

        return array_unique($permissions);
    }

    /**
     * Generates a token for a content basing on the permissions for the content
     * and the list of permissions used to generate the token.
     *
     * @param Content $content            The content.
     * @param array   $permissionsInToken The list of permissions to generate
     *                                    the token.
     *
     * @return string The generated token.
     */
    protected function getToken($content, $permissionsInToken)
    {
        if (empty($content->subscriptions)) {
            return '';
        }

        $token       = '';
        $permissions = $this->getPermissions($content);

        foreach ($permissionsInToken as $permission) {
            $token .= in_array($permission, $permissions) ? '1' : '0';
        }

        return $token;
    }
}
