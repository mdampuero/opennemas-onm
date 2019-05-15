<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Handles all the events after content updates
 */
class HooksSubscriber implements EventSubscriberInterface
{
    /**
     * Initializes the object
     *
     * @param Container       $container The service container.
     * @param AbstractCache   $cache     The cache service.
     * @param LoggerInterface $logger    The logger service.
     * @param CacheManager    $template  The CacheManager services for template.
     */
    public function __construct($container, $cache, $logger, $template)
    {
        $this->cache     = $cache;
        $this->container = $container;
        $this->logger    = $logger;
        $this->template  = $template;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            'advertisement.create' => [
                [ 'removeVarnishCacheForAdvertisement', 5 ],
            ],
            'advertisement.update' => [
                [ 'removeVarnishCacheForAdvertisement', 5 ],
            ],
            'advertisement.delete' => [
                [ 'removeVarnishCacheForAdvertisement', 5 ],
            ],
            // Comment hooks
            'comment.create' => [
                ['mockHookAction', 0],
            ],
            'comment.update' => [
                ['removeObjectCacheForContent', 10],
            ],
            'comment.delete' => [
                ['mockHookAction', 0],
            ],
            // Content hooks
            'content.update-set-num-views' => [
                ['removeObjectCacheForContent', 5]
            ],
            'content.create' => [
                ['removeSmartyCacheForContent', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'content.update' => [
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeObjectCacheContentMeta', 10],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'content.delete' => [
                ['removeObjectCacheForContent', 10],
                ['removeObjectCacheContentMeta', 10],
            ],
            'content.set_positions' => [
                ['mockHookAction', 0],
            ],
            'content.createItem' => [
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'content.updateItem' => [
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeObjectCacheContentMeta', 10],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'content.deleteItem' => [
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeObjectCacheContentMeta', 10],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'content.patchItem'     => [
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeObjectCacheContentMeta', 10],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'content.updateList' => [
                [ 'mockHookAction', 5 ],
            ],
            'content.deleteList' => [
                [ 'mockHookAction', 5 ],
            ],
            'content.patchList' => [
                [ 'mockHookAction', 5 ],
            ],
            // Frontpage hooks
            'frontpage.save_position' => [
                ['removeVarnishCacheFrontpage', 5],
                ['removeObjectCacheFrontpageMap', 5],
                ['removeVarnishCacheFrontpageCSS', 5],
                ['removeSmartyCacheForFrontpageOfCategory', 5],
            ],
            'frontpage.pick_layout' => [
                ['removeVarnishCacheFrontpage', 5],
                ['removeObjectCacheFrontpageMap', 5],
                ['removeSmartyCacheForFrontpageOfCategory', 5],
            ],
            // Instance hooks
            'instance.delete' => [
                ['removeObjectCacheForInstance', 5],
                ['removeObjectCacheCountries', 5],
            ],
            'instance.update' => [
                ['removeObjectCacheForInstance', 5],
                ['removeSmartyForInstance', 5],
                ['removeVarnishInstanceCacheUsingInstance', 5],
                ['removeObjectCacheCountries', 5],
            ],
            'instance.client.update' => [
                ['removeObjectCacheForInstance', 5],
            ],
            'theme.change' => [
                ['removeSmartyCacheAll', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            // Menu hooks
            'menu.create' => [
                ['mockHookAction', 0],
            ],
            'menu.update' => [
                ['removeSmartyCacheAll', 5],
                ['removeObjectCacheForContent', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'menu.delete' => [
                ['removeSmartyCacheAll', 5],
                ['removeObjectCacheForContent', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            // Newsletter subscriptor
            'newsletter_subscriptor.create' => [
                ['mockHookAction', 0],
            ],
            'newsletter_subscriptor.update' => [
                ['mockHookAction', 0],
            ],
            'newsletter_subscriptor.delete' => [
                ['mockHookAction', 0],
            ],
            // Opinion hooks
            'opinion.update' => [
                ['removeSmartyCacheOpinion', 5],
            ],
            'opinion.create' => [
                ['removeSmartyCacheAuthorOpinion', 5],
            ],
            // Setting hooks
            'setting.update' => [
                ['removeSmartyCacheAll', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            // User hooks
            'user.create' => [
                ['removeSmartyCacheAuthor', 5],
            ],
            'user.update' => [
                ['removeObjectCacheUser', 10],
                ['removeSmartyCacheAuthor', 5],
                ['removeObjectCacheMultiCacheAllAuthors', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'user.delete' => [
                ['removeObjectCacheUser', 10],
                ['removeSmartyCacheAuthor', 5],
                ['removeObjectCacheMultiCacheAllAuthors', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'user.social.connect' => [
                ['mockHookAction', 0],
            ],
            'user.social.disconnect' => [
                ['mockHookAction', 0],
            ],
            // UserGroup hooks
            'usergroup.create' => [
                ['mockHookAction', 0],
            ],
            'usergroup.update' => [
                ['mockHookAction', 0],
            ],
            'usergroup.delete' => [
                ['mockHookAction', 0],
            ]
        ];
    }

    /**
     * Resets the PHP Opcode if supported
     *
     * @return null
     */
    public function cleanOpcode()
    {
        if (extension_loaded('Zend Opcache')) {
            opcache_reset();
        }
    }

    /**
     * Mock action for hook events
     *
     * @param Event $event The event to handle.
     *
     * @return boolean
     */
    public function mockHookAction()
    {
        return true;
    }

    /**
     * Removes the list of countries for manager from cache.
     *
     * @param Event $event The event object.
     *
     * @return null
     */
    public function removeObjectCacheCountries()
    {
        $this->container->get('cache.manager')->getConnection('manager')
            ->removeByPattern('*countries*');
    }

    /**
     * Removes the instance from cache.
     *
     * @param Event $event The event object.
     *
     * @return null
     */
    public function removeObjectCacheForInstance(Event $event)
    {
        $instance = $event->getArgument('instance');

        $this->container->get('cache.manager')->getConnection('manager')
            ->remove($instance->domains);
    }

    /**
     * Deletes the Smarty cache when an author is updated.
     *
     * @param Event $event The event to handle.
     *
     * @return null
     */
    public function removeObjectCacheMultiCacheAllAuthors(Event $event)
    {
        $authorId = $event->getArgument('id');

        // Delete cache for author profile
        $this->cache->delete('user-' . $authorId);

        // Get the all contents assigned to this author
        $criteria = [
            'fk_author'       => [[ 'value' => $authorId ]],
            'fk_content_type' => [[ 'value' => [1, 4, 7, 9], 'operator' => 'IN' ]],
            'content_status'  => [[ 'value' => 1 ]],
            'in_litter'       => [[ 'value' => 0 ]],
            'starttime'       => [[
                'value' => date('Y-m-d H:i:s', strtotime("-1 day")),
                'operator' => '>='
            ]],
        ];

        $contents = $this->container->get('entity_repository')->findBy($criteria);

        foreach ($contents as $content) {
            $this->template->delete('content', $content->id);
        }

        // Delete frontpage caches
        $this->template->delete('frontpage', 'opinion')
            ->delete('frontpage', 'opinion', sprintf('%06d', $authorId));

        $this->cleanOpcode();
    }

    /**
     * Deletes the content metadata from cache after it is updated.
     *
     * @param Event $event The event to handle.
     *
     * @return null
     */
    public function removeObjectCacheContentMeta(Event $event)
    {
        $content = $event->getArgument('item');

        $this->cache->delete("content-meta-" . $content->id);
    }

    /**
     * Deletes a content from cache after it is updated.
     *
     * @param Event $event The event to handle.
     *
     * @return null
     */
    public function removeObjectCacheForContent(Event $event)
    {
        $content = $event->getArgument('item');

        if (!empty($content->content_type_name)) {
            $contentType = $content->content_type_name;
        } else {
            $contentType = \underscore(get_class($content));
        }

        $this->cache->delete($contentType . "-" . $content->id);
    }

    /**
     * Deletes the list of objects in cache for a frontpage when content
     * positions are updated.
     *
     * @param Event $event The event to handle.
     *
     * @return null
     */
    public function removeObjectCacheFrontpageMap(Event $event)
    {
        $category    = $event->getArgument('category');
        $frontpageId = $event->getArgument('frontpageId');

        $this->cache->delete(
            empty($frontpageId) ?
                'frontpage_elements_map_' . $category :
                'frontpage_elements_map_' . $category . '_' . $frontpageId
        );
    }

    /**
     * Deletes the user from cache when he is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeObjectCacheUser(Event $event)
    {
        if (!$event->hasArgument('id')) {
            return;
        }

        $id = $event->getArgument('id');

        // TODO: Remove when using only new orm for users
        $this->container->get('cache.manager')->getConnection('instance')
            ->remove('user-' . $id);

        $this->cache->delete('user-' . $id);
        $this->cache->delete('categories_for_user_' . $id);
    }

    /**
     * Cleans all the smarty cache elements.
     */
    public function removeSmartyCacheAll()
    {
        $this->template->deleteAll();
    }

    /**
     * Deletes Smarty caches when an opinion is created.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheAuthorOpinion(Event $event)
    {
        if (!$event->hasArgument('authorId') || empty($event->getArgument('authorId'))) {
            return;
        }

        $authorId = $event->getArgument('authorId');

        // Delete caches for opinion frontpage and author frontpages
        $this->template
            ->delete('opinion', 'list', $authorId)
            ->delete('blog', 'list', $authorId);

        $this->cleanOpcode();
    }

    /**
     * Deletes Smarty caches for a give author
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheAuthor(Event $event)
    {
        if (!$event->hasArgument('id')) {
            return;
        }

        $id = $event->getArgument('id');

        // Delete caches for opinion frontpage and author frontpages
        $this->template
            ->delete('frontpage', 'author', $id)
            ->delete('frontpage', 'authors');

        $this->cleanOpcode();
    }

    /**
     * Deletes the Smarty cache when the updated content is an article.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheForContent(Event $event)
    {
        if (!$event->hasArgument('item')) {
            return;
        }

        $content = $event->getArgument('item');

        // Clean cache for the content
        $this->template
            ->delete('content', $content->pk_content)
            ->delete('archive', date('Ymd'))
            ->delete('rss', $content->content_type_name)
            ->delete('frontpage', $content->content_type_name)
            ->delete('category', 'list', $content->pk_fk_content_category)
            ->delete($content->content_type_name, 'frontpage')
            ->delete($content->content_type_name, 'list');

        if ($content->content_type_name == 'article') {
            $this->template
                ->delete('rss', 'frontpage', 'home')
                ->delete('rss', 'last')
                ->delete('rss', 'fia')
                ->delete('rss', $content->category_name)
                ->delete('sitemap', 'image')
                ->delete('sitemap', 'news')
                ->delete('sitemap', 'web')
                ->delete('frontpage', 'home')
                ->delete('frontpage', 'category', $content->category_name);
        } elseif ($content->content_type_name == 'video') {
            $this->template->delete('sitemap', 'video');
        } elseif ($content->content_type_name == 'opinion') {
            $this->template
                ->delete('blog', 'list')
                ->delete('blog', 'listauthor')
                ->delete($content->content_type_name, 'list')
                ->delete($content->content_type_name, 'listauthor', $content->fk_author)
                ->delete('sitemap', 'news')
                ->delete('sitemap', 'web');
        }

        $this->cleanOpcode();
    }

    /**
     * Cleans the category frontpage given its id.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheForFrontpageOfCategory(Event $event)
    {
        $category = $event->getArgument('category');

        if (!isset($category)) {
            return;
        }

        if ($category != '0' && $category != 'home') {
            $this->container->get('core.locale')->setContext('frontend');

            $category = $this->container->get('api.service.category')
                ->getItem($category);

            $this->container->get('core.locale')->setContext('backend');

            $category = $category->name;
        }

        $this->template
            ->delete('frontpage', $category)
            ->delete('frontpage', 'category', $category)
            ->delete('rss', 'frontpage', 'home')
            ->delete('rss', 'last')
            ->delete('rss', 'fia');

        $this->logger->notice("Cleaning frontpage cache for category: {$category} ($category)");
        $this->cleanOpcode();
    }

    /**
     * Deletes Smarty caches when an opinion is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheOpinion(Event $event)
    {
        if (!$event->hasArgument('item')) {
            return;
        }

        $content = $event->getArgument('item');
        if (empty($content->fk_author)) {
            return;
        }

        $author = $this->container->get('user_repository')->find($content->fk_author);

        if (is_object($author)) {
            $this->template
                ->delete('frontpage', 'author', $author->slug)
                ->delete('frontpage', 'blog', $author->id)
                ->delete('frontpage', 'opinion', $author->id);
        }

        $this->cleanOpcode();
    }

    /**
     * Removes the Smarty cache for an instance.
     *
     * @param Event $event The event object.
     */
    public function removeSmartyForInstance(Event $event)
    {
        if (!$event->hasArgument('instance')) {
            return false;
        }

        $instance = $event->getArgument('instance');

        // Setup cache manager from the target instance
        $this->container->get('core.template')->addInstance($instance);
        $this->template->deleteAll();
    }

    /**
     * Removes varnish cache for advertisement when an advertisement is created,
     * updated or deleted.
     *
     * @param Event $event The event to handle.
     */
    public function removeVarnishCacheForAdvertisement(Event $event)
    {
        if (!$this->container->hasParameter('varnish')
            || !$event->hasArgument('advertisement')
        ) {
            return false;
        }

        $ad = $event->getArgument('advertisement');

        $this->container->get('varnish_ban_message_exchanger')
            ->addBanMessage(sprintf('obj.http.x-tags ~ .*ad-%s.*', $ad->id));

        if (!is_array($ad->positions)) {
            return;
        }

        foreach ($ad->positions as $position) {
            $this->container->get('varnish_ban_message_exchanger')
                ->addBanMessage(sprintf('obj.http.x-tags ~ .*position-%s.*', $position));
        }

        if (!empty($ad->old_position)) {
            $this->container->get('varnish_ban_message_exchanger')
                ->addBanMessage(sprintf(
                    'obj.http.x-tags ~ .*position-%s.*',
                    $ad->old_position
                ));
        }
    }

    /**
     * Queues a varnish ban request.
     *
     * @param Event $event The event to handle.
     */
    public function removeVarnishCacheCurrentInstance()
    {
        if (!$this->container->hasParameter('varnish')) {
            return false;
        }

        $instanceName = $this->container->get('core.instance')->internal_name;

        $this->container->get('varnish_ban_message_exchanger')
            ->addBanMessage(sprintf('obj.http.x-tags ~ instance-%s', $instanceName));
    }

    /**
     * Queues a varnish ban request to delete the frontpage
     *
     * @param Event $event The event to handle.
     */
    public function removeVarnishCacheFrontpage()
    {
        // Clean varnish cache for frontpage
        if ($this->container->hasParameter('varnish')) {
            $instanceName = $this->container->get('core.instance')->internal_name;

            $this->container->get('varnish_ban_message_exchanger')
                ->addBanMessage(sprintf('obj.http.x-tags ~ instance-%s.*frontpage-page.*', $instanceName))
                ->addBanMessage(sprintf('obj.http.x-tags ~ instance-%s.*rss.*', $instanceName));
        }
    }

    /**
     * Queues a varnish ban request.
     *
     * @param Event $event The event to handle.
     */
    public function removeVarnishCacheFrontpageCSS()
    {
        if (!$this->container->hasParameter('varnish')) {
            return false;
        }

        $instanceName = $this->container->get('core.instance')->internal_name;

        $this->container->get('varnish_ban_message_exchanger')
            ->addBanMessage(sprintf('obj.http.x-tags ~ instance-%s.*frontpagecss.*', $instanceName));
    }

    /**
     * Queues a varnish ban request.
     *
     * @param Event $event The event to handle.
     */
    public function removeVarnishInstanceCacheUsingInstance(Event $event)
    {
        if (!$this->container->hasParameter('varnish')) {
            return false;
        }

        $instanceName = $event->getArgument('instance')->internal_name;

        $this->container->get('varnish_ban_message_exchanger')
            ->addBanMessage(sprintf('obj.http.x-tags ~ instance-%s.*', $instanceName));
    }
}
