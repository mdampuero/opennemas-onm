<?php

namespace Framework\EventListener;

use Api\Exception\GetItemException;
use Opennemas\Task\Component\Task\ServiceTask;
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
            // Comments Config hooks
            'comments.config' => [
                ['removeSmartyCacheAll', 5],
                ['removeVarnishCacheCurrentInstance', 5]
            ],
            // Content hooks
            'content.update-set-num-views' => [
                ['removeObjectCacheForContent', 5]
            ],
            'content.create' => [
                ['logAction', 5],
                ['removeSmartyCacheForContent', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'content.update' => [
                ['logAction', 5],
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'content.delete' => [
                ['logAction', 5],
                ['removeObjectCacheForContent', 10],
            ],
            'content.createItem' => [
                ['logAction', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'content.updateItem' => [
                ['logAction', 5],
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'content.deleteItem' => [
                ['logAction', 5],
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeVarnishCacheCurrentInstance', 5],
                ['removeCacheForRelatedContents', 5],
            ],
            'content.patchItem' => [
                ['logAction', 5],
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'content.patchList' => [
                ['logAction', 5],
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'content.deleteList' => [
                ['logAction', 5],
                ['removeSmartyCacheForContent', 5],
                ['removeObjectCacheForContent', 10],
                ['removeVarnishCacheCurrentInstance', 5],
                ['removeCacheForRelatedContents', 5],
            ],
            // Frontpage hooks
            'frontpage.save_position' => [
                ['removeVarnishCacheFrontpage', 5],
                ['removeObjectCacheFrontpageMap', 5],
                ['removeVarnishCacheFrontpageCSS', 5],
                ['removeSmartyCacheForFrontpageOfCategory', 5],
                ['removeDynamicCssSettingForFrontpage', 5],
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
            'menu.updateItem' => [
                ['removeSmartyCacheAll', 5],
                ['removeObjectCacheForContent', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'menu.deleteItem' => [
                ['removeSmartyCacheAll', 5],
                ['removeObjectCacheForContent', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            // Opinion hooks
            'opinion.update' => [
                ['removeSmartyCacheOpinion', 5],
            ],
            'opinion.create' => [
                ['removeSmartyCacheAuthorOpinion', 5],
            ]
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

        $this->container->get('task.service.queue')->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ .*ad-%s.*', $ad->id)
            ])
        );

        if (!is_array($ad->positions)) {
            return;
        }

        foreach ($ad->positions as $position) {
            $this->container->get('task.service.queue')->push(
                new ServiceTask('core.varnish', 'ban', [
                    sprintf('obj.http.x-tags ~ .*position-%s.*', $position)
                ])
            );
        }

        if (!empty($ad->old_position)) {
            $this->container->get('task.service.queue')->push(
                new ServiceTask('core.varnish', 'ban', [
                    sprintf('obj.http.x-tags ~ .*position-%s.*', $ad->old_position)
                ])
            );
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

        $this->container->get('task.service.queue')->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s', $instanceName)
            ])
        );
    }

    /**
     * Queues a varnish ban request to delete the frontpage
     *
     * @param Event $event The event to handle.
     */
    public function removeVarnishCacheFrontpage()
    {
        if (!$this->container->hasParameter('varnish')) {
            return false;
        }

        $instanceName = $this->container->get('core.instance')->internal_name;

        $this->container->get('task.service.queue')->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*frontpage-page.*', $instanceName)
            ])
        )->push(
            new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*rss.*', $instanceName)
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
                sprintf('obj.http.x-tags ~ instance-%s.*frontpagecss.*', $instanceName)
            ])
        );
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
                sprintf('obj.http.x-tags ~ instance-%s.*', $instanceName)
            ])
        );
    }

    /**
     * Removes cache for related contents of element deleted
     *
     * @param Event $event The event to handle.
     */
    public function removeCacheForRelatedContents(Event $event)
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
