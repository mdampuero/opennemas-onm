<?php

namespace Framework\EventListener;

use Api\Exception\GetItemException;
use Opennemas\Task\Component\Task\ServiceTask;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
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
     * @param Cache           $redis     The cache service for redis.
     */
    public function __construct($container, $cache, $logger, $template, $redis)
    {
        $this->cache     = $cache;
        $this->container = $container;
        $this->logger    = $logger;
        $this->redis     = $redis;
        $this->template  = $template;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * Priority order:
     * Redis 20
     * Smarty 15
     * Varnish 10
     * Log 5
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            'advertisement.create' => [
                [ 'removeVarnishCacheCurrentInstance', 10 ],
            ],
            'advertisement.update' => [
                [ 'removeVarnishCacheCurrentInstance', 10 ],
            ],
            'advertisement.delete' => [
                [ 'removeVarnishCacheCurrentInstance', 10 ],
            ],
            // Comments Config hooks
            'comments.config' => [
                ['removeSmartyCacheAll', 15],
                ['removeVarnishCacheForAllComments', 10]
            ],
            // Comments hooks
            'comment.createItem' => [
                [ 'removeVarnishCacheForComments', 10 ]
            ],
            'comment.updateItem' => [
                [ 'removeVarnishCacheForComments', 10 ]
            ],
            'comment.patchItem'  => [
                [ 'removeVarnishCacheForComments', 10 ]
            ],
            'comment.patchList'  => [
                [ 'removeVarnishCacheForComments', 10 ]
            ],
            // Content hooks
            'content.update-set-num-views' => [
                ['removeObjectCacheForContent', 20]
            ],
            'content.create' => [
                ['logAction', 5],
            ],
            'content.update' => [
                ['removeObjectCacheForContent', 20],
                ['logAction', 5],
            ],
            'content.delete' => [
                ['removeObjectCacheForContent', 20],
                ['logAction', 5],
            ],
            'content.createItem' => [
                ['removeVarnishCacheForContent', 10],
                ['logAction', 5],
            ],
            'content.updateItem' => [
                ['removeObjectCacheForContent', 20],
                ['removeSmartyCacheForContent', 15],
                ['removeVarnishCacheForContent', 10],
                ['removeSavedSitemaps', 5],
                ['logAction', 5],
            ],
            'content.updateVotedItem' => [
                ['removeObjectCacheForContent', 20],
                ['removeSmartyCacheForContent', 15],
                ['removeVarnishCacheForVotedContent', 10],
                ['logAction', 5],
            ],
            'content.deleteItem' => [
                ['removeObjectCacheForContent', 20],
                ['removeObjectCacheForRelatedContents', 20],
                ['removeSmartyCacheForContent', 15],
                ['removeVarnishCacheForContent', 10],
                ['removeSavedSitemaps', 5],
                ['logAction', 5],
            ],
            'content.patchItem' => [
                ['removeObjectCacheForContent', 20],
                ['removeSmartyCacheForContent', 15],
                ['removeVarnishCacheForContent', 10],
                ['removeSavedSitemaps', 5],
                ['logAction', 5],
            ],
            'content.patchList' => [
                ['removeObjectCacheForContent', 20],
                ['removeSmartyCacheForContent', 15],
                ['removeVarnishCacheForContent', 10],
                ['removeSavedSitemaps', 5],
                ['logAction', 5],
            ],
            'content.deleteList' => [
                ['removeObjectCacheForContent', 20],
                ['removeObjectCacheForRelatedContents', 20],
                ['removeSmartyCacheForContent', 15],
                ['removeVarnishCacheForContent', 10],
                ['removeSavedSitemaps', 5],
                ['logAction', 5],
            ],
            // Frontpage hooks
            'frontpage.save_position' => [
                ['removeObjectCacheFrontpageMap', 20],
                ['removeObjectCacheForWidgets', 20],
                ['removeDynamicCssSettingForFrontpage', 20],
                ['removeSmartyCacheForFrontpageOfCategory', 15],
                ['removeVarnishCacheFrontpageCSS', 10],
                ['removeVarnishCacheFrontpage', 10],
            ],
            'frontpage.pick_layout' => [
                ['removeObjectCacheFrontpageMap', 20],
                ['removeSmartyCacheForFrontpageOfCategory', 15],
                ['removeVarnishCacheFrontpage', 10],
            ],
            // Instance hooks
            'instance.delete' => [
                ['removeObjectCacheForInstance', 20],
                ['removeObjectCacheCountries', 20],
            ],
            'instance.update' => [
                ['removeObjectCacheForInstance', 20],
                ['removeObjectCacheCountries', 20],
                ['removeSmartyForInstance', 15],
                ['removeVarnishInstanceCacheUsingInstance', 10],
            ],
            'instance.client.update' => [
                ['removeObjectCacheForInstance', 20],
            ],
            'theme.change' => [
                ['removeSmartyCacheAll', 15],
                ['removeVarnishCacheCurrentInstance', 10],
            ],
            // Menu hooks
            'menu.updateItem' => [
                ['removeObjectCacheForContent', 20],
                ['removeSmartyCacheAll', 15],
                ['removeVarnishCacheCurrentInstance', 10],
            ],
            'menu.deleteItem' => [
                ['removeObjectCacheForContent', 20],
                ['removeSmartyCacheAll', 15],
                ['removeVarnishCacheCurrentInstance', 10],
            ],

            // Web Push notifications hooks
            'web_push_notifications.patchItem' => [
                ['removeObjectCacheForWebPushNotifications', 20]
            ],
        ];
    }

    /**
     * Logs the action.
     *
     * @param Event $event The event object.
     */
    public function logAction(Event $event)
    {
        if (empty($event->hasArgument('action'))) {
            return;
        }

        $action = $event->getArgument('action');
        $item   = $event->getArgument('item');
        $items  = is_array($item) ? $item : [ $item ];

        if (!empty($items)) {
            foreach ($items as $content) {
                logContentEvent($action, $content);
            }

            return;
        }
    }

    /**
     * Queues the necessary bans for an specific content.
     */
    public function removeVarnishCacheForContent(Event $event)
    {
        $item   = $event->getArgument('item');
        $action = $event->getArgument('action');

        $items = !is_array($item) ? [ $item ] : $item;

        foreach ($items as $item) {
            try {
                $this->container->get(sprintf('api.helper.cache.%s', $item->content_type_name))
                    ->deleteItem($item, [ 'action' => $action ]);
            } catch (ServiceNotFoundException $e) {
                $this->container->get(sprintf('api.helper.cache.content'))
                    ->deleteItem($item, [ 'action' => $action ]);
            }
        }
    }

    /**
     * Queues the necessary bans for an specific voted content.
     */
    public function removeVarnishCacheForVotedContent(Event $event)
    {
        $item = $event->getArgument('item');

        $items = !is_array($item) ? [ $item ] : $item;

        foreach ($items as $item) {
            try {
                $this->container->get(sprintf('api.helper.cache.%s', $item->content_type_name))
                    ->deleteItem($item, [ 'vote' => true ]);
            } catch (ServiceNotFoundException $e) {
                $this->container->get(sprintf('api.helper.cache.content'))
                    ->deleteItem($item, [ 'vote' => true ]);
            }
        }
    }

    /**
     * Removes varnish cache for comments snippet.
     *
     * @param Event $event The event object.
     *
     * @return null
     */
    public function removeVarnishCacheForComments(Event $event)
    {
        $item   = $event->getArgument('item');
        $action = $event->getArgument('action');

        $action     = explode("::", $action);
        $actionName = end($action);

        $items = is_array($item) ? $item : [ $item ];
        $items = array_filter($items, function ($item) use ($actionName) {
            if ($actionName != 'createItem' || ( $actionName == 'createItem' && $item->status != 'pending')) {
                return $item;
            }
        });

        $itemKeys = array_map(function ($item) {
            return $item->content_id;
        }, $items);

        $instanceName = $this->container->get('core.instance')->internal_name;

        foreach ($itemKeys as $key) {
            $this->container->get('task.service.queue')->push(
                new ServiceTask('core.varnish', 'ban', [
                    sprintf('obj.http.x-tags ~ ^instance-%s.*comments-%s(,|$)', $instanceName, $key)
                ])
            );
        }
    }

    /**
     * Removes varnish cache for all comments snippets.
     *
     * @return null
     */
    public function removeVarnishCacheForAllComments()
    {
        $instanceName = $this->container->get('core.instance')->internal_name;

        $this->container->get('task.service.queue')->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ ^instance-%s.*comments-*', $instanceName)
            ])
        );
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
        $this->container->get('task.service.queue')->push(
            new ServiceTask('cache.connection.manager', 'removeByPattern', [
                '*countries*'
            ])
        );
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
     * Deletes a content from cache after it is updated.
     *
     * @param Event $event The event to handle.
     *
     * @return null
     */
    public function removeObjectCacheForContent(Event $event)
    {
        $item  = $event->getArgument('item');
        $items = is_array($item) ? $item : [ $item ];

        foreach ($items as $object) {
            if (!empty($object->content_type_name)) {
                $this->cache->delete($object->content_type_name . "-" . $object->id);
                return;
            }

            $this->cache->delete(\underscore(get_class($object)) . '-' . $object->id);
        }
    }

    /**
     * Removes the redis cache for the content listing widgets in frontpage.
     *
     * @param Event $event The event to handle.
     */
    public function removeObjectCacheForWidgets()
    {
        $cache = $this->container->get('cache.connection.instance');

        $cache->remove($cache->getSetMembers('Widget_Keys'));
        $cache->remove('Widget_Keys');
    }

    /**
     * Deletes the Web Push notification content from cache after the notification is updated.
     *
     * @param Event $event The event to handle.
     *
     * @return null
     */
    public function removeObjectCacheForWebPushNotifications(Event $event)
    {
        $redis = $this->container->get('cache.connection.instance');
        $item  = $event->getArgument('item');
        $items = is_array($item) ? $item : [ $item ];

        foreach ($items as $object) {
            $redis->remove('content-' . $object->fk_content);
        }
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

        // Updates the cache for last_saved
        $lastSavedCacheId = 'frontpage_last_saved_' . $category . '_' . $frontpageId;
        $date             = new \Datetime("now");

        $this->cache->save($lastSavedCacheId, $date->format(\DateTime::ISO8601));
        $this->cache->delete(
            empty($frontpageId) ?
                'frontpage_elements_map_' . $category :
                'frontpage_elements_map_' . $category . '_' . $frontpageId
        );
    }

    /**
     * Remove dynamic css for specific frontpage
     */
    public function removeDynamicCssSettingForFrontpage(Event $event)
    {
        $categoryId = (int) $event->getArgument('category');

        $key = ($categoryId == 0) ? 'home' :
            $categoryId;

        $this->container->get('core.service.assetic.dynamic_css')
            ->deleteTimestamp($key);
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

        $item  = $event->getArgument('item');
        $items = is_array($item) ? $item : [ $item ];

        foreach ($items as $content) {
            // Clean cache for the content
            $this->template
                ->delete('content', $content->pk_content)
                ->delete('archive', date('Ymd'))
                ->delete('rss', $content->content_type_name)
                ->delete('frontpage', $content->content_type_name)
                ->delete('category', 'list', $content->category_id)
                ->delete($content->content_type_name, 'frontpage')
                ->delete($content->content_type_name, 'list')
                ->delete('sitemap', 'contents');

            if ($content->content_type_name == 'article') {
                $this->template
                    ->delete('rss', 'frontpage', 'home')
                    ->delete('rss', 'last')
                    ->delete('rss', 'fia')
                    ->delete('rss', 'frontpage', $content->category_id)
                    ->delete('sitemap', 'news')
                    ->delete('frontpage', 'category', 'home')
                    ->delete('frontpage', 'category', $content->category_id);
            } elseif ($content->content_type_name == 'opinion') {
                $this->template
                    ->delete('blog', 'list')
                    ->delete('blog', 'listauthor')
                    ->delete($content->content_type_name, 'list')
                    ->delete($content->content_type_name, 'listauthor', $content->fk_author)
                    ->delete('sitemap', 'news');
            }
        }
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

            $category = $category->id;
        }

        $this->template
            ->delete('frontpage', $category)
            ->delete('frontpage', 'category', $category)
            ->delete('rss', 'frontpage', 'home')
            ->delete('rss', 'frontpage', $category)
            ->delete('rss', 'last')
            ->delete('rss', 'fia');

        $this->logger->info("Cleaning frontpage cache for category: {$category} ($category)");
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

        try {
            $author = $this->container->get('api.service.author')
                ->getItem($content->fk_author);
        } catch (GetItemException $e) {
            return;
        }

        if (is_object($author)) {
            $this->template
                ->delete('frontpage', 'author', $author->slug)
                ->delete('frontpage', 'blog', $author->id)
                ->delete('frontpage', 'opinion', $author->id);
        }
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
     * Queues a varnish ban request.
     */
    public function removeVarnishCacheCurrentInstance()
    {
        if (!$this->container->hasParameter('varnish')) {
            return false;
        }

        $instanceName = $this->container->get('core.instance')->internal_name;

        $this->container->get('task.service.queue')->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ ^instance-%s', $instanceName)
            ])
        );
    }

    /**
     * Queues a varnish ban request to delete the frontpage
     *
     * @param Event $event The event to handle.
     */
    public function removeVarnishCacheFrontpage(Event $event)
    {
        $items = $event->hasArgument('items') ? $event->getArgument('items') : [];

        if (!$this->container->hasParameter('varnish')) {
            return false;
        }

        $category     = $event->getArgument('category');
        $instanceName = $this->container->get('core.instance')->internal_name;

        $this->container->get('api.helper.cache.frontpage')->deleteItems($items, $category);
        $this->container->get('task.service.queue')->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ ^instance-%s.*header-date', $instanceName, $category)
            ])
        )->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ ^instance-%s.*rss-frontpage-%s.*', $instanceName, $category)
            ])
        )->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ ^instance-%s.*frontpage-page-%s$', $instanceName, $category)
            ])
        );
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

        $this->container->get('task.service.queue')->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ ^instance-%s.*frontpagecss.*', $instanceName)
            ])
        );
    }

    /**
     * Remove saved sitemaps.
     *
     * @param Event $event The event to handle.
     */
    public function removeSavedSitemaps(Event $event)
    {
        if (!$event->hasArgument('item') || !$event->hasArgument('last_changed')) {
            return;
        }

        $sh          = $this->container->get('core.helper.sitemap');
        $timezone    = $this->container->get('core.locale')->getTimeZone();
        $now         = new \DateTime(null, $timezone);
        $content     = $event->getArgument('item');
        $lastChanged = $event->getArgument('last_changed');

        $lastChanged = !is_array($lastChanged) ? [ $lastChanged ] : $lastChanged;
        $content     = !is_array($content) ? [ $content ] : $content;

        foreach ($lastChanged as $key => $value) {
            if (empty($value)
            || !in_array($content[0]->content_type_name, $sh->getTypes($sh->getSettings(), ['tag']))
            || $now->format('Y-m') == $value->format('Y-m')) {
                continue;
            }
            $sh->removeSitemapsByPattern(
                $value->format('Y'),
                $value->format('m')
            );
        }
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

        $this->container->get('task.service.queue')->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ ^instance-%s.*', $instanceName)
            ])
        );
    }

    /**
     * Removes cache for related contents of element deleted
     *
     * @param Event $event The event to handle.
     */
    public function removeObjectCacheForRelatedContents(Event $event)
    {
        if (!$event->hasArgument('related')) {
            return false;
        }

        $cache   = $this->container->get('cache.connection.instance');
        $item    = $event->getArgument('related');
        $related = is_array($item) ? $item : [ $item ];

        foreach ($related as $content) {
            $cache->remove('content-' . $content->pk_content);
        }
    }
}
