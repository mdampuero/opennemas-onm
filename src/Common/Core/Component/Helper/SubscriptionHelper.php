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
        'NON_MEMBER_BLOCK_ACCESS',
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
        'NON_MEMBER_BLOCK_BROWSER',
        'NON_MEMBER_NO_INDEX'
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
     * @param Security            $security The Security service.
     * @param SubscriptionService $ss       The subscription API service.
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
    public function getToken($content)
    {
        if ($this->isSubscribed($content)) {
            return $this->generateToken($content, $this->subscribedPermissions);
        }

        return $this->generateToken($content, $this->notSubscribedPermissions);
    }

    /**
     * Checks if the item is blocked basing on the token value.
     *
     * @param string $token The subscription token.
     * @param string $item  The item name.
     *
     * @return boolean True if the item is blocked. False otherwise.
     */
    public function isBlocked($token, $item)
    {
        return $this->checkToken($token, 'BLOCK', $item);
    }

    /**
     * Checks if the item is hidden basing on the token value.
     *
     * @param string $token The subscription token.
     * @param string $item  The item name.
     *
     * @return boolean True if the item is hidden. False otherwise.
     */
    public function isHidden($token, $item)
    {
        return $this->checkToken($token, 'HIDE', $item);
    }

    /**
     * Checks if the item can or cannot be indexed by search engines basing on
     * the token value.
     *
     * @param string $token The subscription token.
     *
     * @return boolean True if the can be indexed by search engines. False
     *                  otherwise.
     */
    public function isIndexable($token)
    {
        return !$this->checkToken($token, 'NO_INDEX');
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
        if (empty($this->security->getUser())
            || empty($this->security->getUser()->user_groups)
        ) {
            return false;
        }

        $subscriptions = array_filter(
            $this->security->getUser()->user_groups,
            function ($a) use ($content) {
                return !empty($content->subscriptions)
                    && in_array($a['user_group_id'], $content->subscriptions)
                    && $a['status'] === 1;
            }
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
     * Checks if an action has to be executed for an item basing on a token
     * value.
     *
     * @param string $token  The subscription token.
     * @param string $action The action name.
     * @param string $item   The item name.
     *
     * @return boolean True if the action has to be executed. False otherwise.
     */
    protected function checkToken($token, $action, $name = '')
    {
        if (empty($token)) {
            return false;
        }

        $member      = strlen($token) === 3;
        $prefix      = $member ? 'MEMBER' : 'NON_MEMBER';
        $permission  = $prefix . '_' . strtoupper($action);
        $permissions = $member ? $this->subscribedPermissions
            : $this->notSubscribedPermissions;

        if (!empty($name)) {
            $permission .= '_' . strtoupper($name);
        }

        $index = array_search($permission, $permissions);

        if ($index === false || $index >= count($permissions)) {
            return false;
        }

        return $token[$index] === '1';
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
    protected function generateToken($content, $permissionsInToken)
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
}
