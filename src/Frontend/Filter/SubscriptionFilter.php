<?php
/**
 * Defines the subscription filter class
 *
 * @package Frontend_Filter
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Filter;

use Onm\Module\ModuleManager;

/**
 * Defines the subscription filter class
 **/
class SubscriptionFilter
{
    /**
     * Initializes the filter
     *
     * @param Template $template the template object to render views
     * @param User $user the session user object
     *
     * @return void
     **/
    public function __construct($template, $user)
    {
        $this->template = $template;
        $this->user     = $user;
    }

    /**
     * Check and modify content if required basing on subscription constraints.
     *
     * @param Content $content The content to check.
     *
     * @return boolean True if the content is cacheable. Otherwise, returns
     *                 false.
     */
    public function subscriptionHook(&$content)
    {
        $cacheable = true;


        if (ModuleManager::isActivated('CONTENT_SUBSCRIPTIONS')
            && $content->isOnlyAvailableForRegistered()) {
            $cacheable = false;

            $this->registeredHook($content);
        }

        if (ModuleManager::isActivated('PAYWALL')
            && $content->isOnlyAvailableForSubscribers()
        ) {
            $cacheable = false;

            $this->paywallHook($content);
        }

        // Disable smarty cache for content
        if (!$cacheable) {
            $this->template->caching = 0;
        }

        return $cacheable;
    }

    /**
     * Replaces article body for unregistered users.
     *
     * @param Article $content The article.
     */
    public function registeredHook(&$content)
    {
        $restrictedContent = $this->template->fetch(
            'article/partials/content_only_for_registered.tpl',
            array('id' => $content->id)
        );

        if (empty($this->user)) {
            $this->replaceContent($content, $restrictedContent);
        }
    }

    /**
     * Replaces article body for unsubscribed users.
     *
     * @return Article $content The article.
     */
    public function paywallHook(&$content)
    {
        $restrictedContent = $this->template->fetch(
            'paywall/partials/content_only_for_subscribers.tpl',
            array('id' => $content->id)
        );

        if (empty($this->user) || empty($user->getMeta('paywall_time_limit'))) {
            if ($limit < $now) {
                $this->replaceContent($content, $restrictedContent);
            }
            return;
        }

        $limit = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $this->user->getMeta('paywall_time_limit'),
            new \DateTimeZone('UTC')
        );

        $now = new \DateTime('now', new \DateTimeZone('UTC'));

        if ($limit < $now) {
            $this->replaceContent($content, $restrictedContent);
        }
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    private function replaceContent(&$content, $restrictedContent)
    {
        $content->body = $restrictedContent;
        $content->img       = null;
        $content->img2      = null;
        $content->fk_video2 = null;

        if ($content->content_type_name == 'video') {
            $content->description = $restrictedContent;
            $content->video_content_replaced = true;
        }

        if ($content->content_type_name == 'album') {
            $content->description = $restrictedContent;
            $content->album_content_replaced = true;
        }
    }
}
