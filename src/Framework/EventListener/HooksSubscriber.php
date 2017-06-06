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
     */
    public function __construct($container, $cache, $logger)
    {
        $this->objectCacheHandler = $cache;
        $this->container    = $container;
        $this->logger       = $logger;

        $this->view = $this->container->get('view')->getTemplate();
        $this->smartyCacheHandler = $this->container->get('template_cache_manager')
            ->setSmarty($this->view);
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
            // Author hooks
            'author.create' => [
                ['mockHookAction', 0],
            ],
            'author.update' => [
                ['removeMultiCacheAllAuthors', 5],
            ],
            'author.delete' => [
                ['mockHookAction', 0],
            ],
            // Category hooks
            'category.create' => [
                ['removeSmartyCacheGlobalCss', 5],
                ['removeObjectCacheCategoriesArray', 5]
            ],
            'category.update' => [
                ['removeSmartyCacheGlobalCss', 5],
                ['removeSmartyCacheCategories', 5],
                ['removeObjectCacheCategory', 5],
                ['removeObjectCacheCategoriesArray', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'category.delete' => [
                ['removeSmartyCacheGlobalCss', 5],
                ['removeSmartyCacheCategories', 5],
                ['removeObjectCacheCategory', 5],
                ['removeObjectCacheCategoriesArray', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            // Comment hooks
            'comment.create' => [
                ['mockHookAction', 0],
            ],
            'comment.update' => [
                ['mockHookAction', 0],
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
                ['mockHookAction', 0],
            ],
            'content.set_positions' => [
                ['mockHookAction', 0],
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
                ['removeCacheForInstance', 5],
                ['removeCountries', 5],
            ],
            'instance.update' => [
                ['removeCacheForInstance', 5],
                ['removeSmartyForInstance', 5],
                ['removeVarnishInstanceCacheUsingInstance', 5],
                ['removeCountries', 5],
            ],
            'instance.client.update' => [
                ['removeCacheForInstance', 5],
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
                ['mockHookAction', 0],
            ],
            'user.update' => [
                ['removeObjectCacheUser', 10],
            ],
            'user.delete' => [
                ['mockHookAction', 0],
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
     */
    public function mockHookAction(Event $event)
    {
        return true;
    }

    /**
     * Deletes the Smarty cache when an author is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeMultiCacheAllAuthors(Event $event)
    {
        $authorId = $event->getArgument('id');

        // Delete cache for author profile
        $this->objectCacheHandler->delete('user-' . $authorId);

        // Get the list articles for this author
        $cm = new \ContentManager();
        $opinions = $cm->getOpinionArticlesWithAuthorInfo(
            'opinions.type_opinion=0 AND opinions.fk_author='.$authorId
            .' AND contents.available=1 and contents.content_status=1',
            'ORDER BY created DESC '
        );

        if (!empty($opinions)) {
            foreach ($opinions as &$opinion) {
                $this->smartyCacheHandler
                    ->deleteGroup($this->view->getCacheId('content', $opinion->pk_content));
            }
        }
        // Delete frontpage caches
        $this->smartyCacheHandler
            ->deleteGroup($this->view->getCacheId('frontpage', 'opinion'))
            ->deleteGroup($this->view->getCacheId('frontpage', 'opinion', sprintf('%06d', $authorId)));

        $this->cleanOpcode();
    }

    /**
     * Deletes cache for content_categories object
     */
    public function removeObjectCacheCategoriesArray()
    {
        $this->objectCacheHandler->delete('content_categories');
    }

    /**
     * Deletes a category from cache when it is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeObjectCacheCategory(Event $event)
    {
        if (!$event->hasArgument('category')) {
            return;
        }

        $category = $event->getArgument('category');

        // Delete object cache
        $this->objectCacheHandler->delete('category-' . $category->id);
    }

    /**
     * Deletes the content metadata from cache after it is updated.
     *
     * @param Event $event The event to handle.
     **/
    public function removeObjectCacheContentMeta(Event $event)
    {
        $content = $event->getArgument('content');

        $this->objectCacheHandler->delete("content-meta-" . $content->id);
    }

    /**
     * Deletes a content from cache after it is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeObjectCacheForContent(Event $event)
    {
        $content = $event->getArgument('content');

        $id = $content->id;
        if (!empty($content->content_type_name)) {
            $contentType = $content->content_type_name;
        } else {
            $contentType = \underscore(get_class($content));
        }

        $this->objectCacheHandler->delete($contentType . "-" . $id);
    }

    /**
     * Deletes the list of objects in cache for a frontpage when content
     * positions are updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeObjectCacheFrontpageMap(Event $event)
    {
        $category = $event->getArgument('category');

        $this->objectCacheHandler->delete('frontpage_elements_map_'.$category);
    }

    /**
     * Deletes the user from cache when he is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeObjectCacheUser(Event $event)
    {
        $user = $event->getArgument('user');

        // TODO: Remove when using only new orm for users
        $this->container->get('cache.manager')->getConnection('instance')
            ->remove('user-' . $user->id);

        $this->objectCacheHandler->delete('user-' . $user->id);
        $this->objectCacheHandler->delete('categories_for_user_'.$user->id);
    }

    /**
     * Cleans all the smarty cache elements.
     */
    public function removeSmartyCacheAll()
    {
        $this->smartyCacheHandler->deleteAll();
    }

    /**
     * Deletes Smarty caches when an opinion is created.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheAuthorOpinion(Event $event)
    {
        if (!$event->hasArgument('authorId')) {
            return;
        }

        $authorId = $event->getArgument('authorId');

        // Delete caches for opinion frontpage and author frontpages
        $this->smartyCacheHandler
            ->deleteGroup($this->view->getCacheId('frontpage', 'opinion', sprintf('%06d', $authorId)))
            ->deleteGroup($this->view->getCacheId('frontpage', 'blog', sprintf('%06d', $authorId)));

        $this->cleanOpcode();
    }

    /**
     * Deletes a category from cache when it is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheCategories(Event $event)
    {
        if (!$event->hasArgument('category')) {
            return;
        }

        $category = $event->getArgument('category');

        // Delete smarty cache for frontpage RSS, manual frontpage
        // and blog frontpage frontpage of category
        $this->smartyCacheHandler
            ->deleteGroup($this->view->getCacheId('rss', $category->name))
            ->deleteGroup($this->view->getCacheId('frontpage', $category->name))
            ->deleteGroup($this->view->getCacheId('frontpage', 'category', $category->name));
    }

    /**
     * Deletes the Smarty cache when the updated content is an article.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheForContent(Event $event)
    {
        if (!$event->hasArgument('content')) {
            return;
        }

        $content = $event->getArgument('content');

        // Clean cache for the content
        $this->smartyCacheHandler
            ->deleteGroup($this->view->getCacheId('archive', date('Ymd')))
            ->deleteGroup($this->view->getCacheId('content', $content->pk_content))
            ->deleteGroup($this->view->getCacheId('frontpage', $content->content_type_name))
            ->deleteGroup($this->view->getCacheId('rss', $content->content_type_name));

        if ($content->content_type_name == 'article') {
            $this->smartyCacheHandler
                // Deleting rss cache files
                ->deleteGroup($this->view->getCacheId('rss', 'frontpage', 'home'))
                ->deleteGroup($this->view->getCacheId('rss', 'last'))
                ->deleteGroup($this->view->getCacheId('rss', 'fia'))
                ->deleteGroup($this->view->getCacheId('rss', $content->category_name))
                // Deleting sitemap cache files
                ->deleteGroup($this->view->getCacheId('sitemap', 'image'))
                ->deleteGroup($this->view->getCacheId('sitemap', 'news'))
                ->deleteGroup($this->view->getCacheId('sitemap', 'web'))
                // Deleting frontpage cache files
                ->deleteGroup($this->view->getCacheId('frontpage', 'home'))
                ->deleteGroup($this->view->getCacheId('frontpage', 'category', $content->category_name));
        } elseif ($content->content_type_name == 'video') {
            $this->smartyCacheHandler
                ->deleteGroup($this->view->getCacheId('sitemap', 'video'));
        } elseif ($content->content_type_name == 'opinion') {
            $this->smartyCacheHandler
                ->deleteGroup($this->view->getCacheId('sitemap', 'news'))
                ->deleteGroup($this->view->getCacheId('sitemap', 'web'))
                ->deleteGroup($this->view->getCacheId('frontpage', 'blog'));
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
        $ccm = \ContentCategoryManager::get_instance();

        if ($category == '0' || $category == 'home') {
            $categoryName = 'home';
        } elseif ($category == 'opinion') {
            $categoryName = 'opinion';
        } else {
            $categoryName = $ccm->getName($category);
        }

        $this->smartyCacheHandler
            // Deleting rss cache files
            ->deleteGroup($this->view->getCacheId('rss', 'frontpage', 'home'))
            ->deleteGroup($this->view->getCacheId('rss', 'last'))
            ->deleteGroup($this->view->getCacheId('rss', 'fia'))
            ->deleteGroup($this->view->getCacheId('rss', $category))
            // Deleting frontpage cache files
            ->deleteGroup($this->view->getCacheId('frontpage', $categoryName));

        $this->logger->notice("Cleaning frontpage cache for category: {$category} ($categoryName)");

        $this->cleanOpcode();
    }

    /**
     * Deletes custom CSS from cache when a category is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheGlobalCss(Event $event)
    {
        $this->smartyCacheHandler
            ->deleteGroup($this->view->getCacheId('css', 'global'));
    }

    /**
     * Deletes Smarty caches when an opinion is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheOpinion(Event $event)
    {
        if (!$event->hasArgument('content')) {
            return;
        }

        $content  = $event->getArgument('content');
        if (empty($content->fk_author)) {
            return;
        }

        $author = $this->container->get('user_repository')->find($content->fk_author);

        $this->smartyCacheHandler
            ->deleteGroup($this->view->getCacheId('content', $content->id))
            ->deleteGroup($this->view->getCacheId('frontpage', 'blog'))
            ->deleteGroup($this->view->getCacheId('frontpage', 'opinion'));

        if (is_object($author)) {
            $this->smartyCacheHandler
                ->deleteGroup($this->view->getCacheId('frontpage', 'author', $author->slug))
                ->deleteGroup($this->view->getCacheId('frontpage', 'blog', $author->id))
                ->deleteGroup($this->view->getCacheId('frontpage', 'opinion', $author->id));
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
        $this->view = $this->container->get('core.template');
        $this->view->addInstance($instance);
        $this->smartyCacheHandler = $this->container->get('template_cache_manager')->setSmarty($this->view);

        $this->smartyCacheHandler->deleteAll();
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
            ->addBanMessage(sprintf('obj.http.x-tags ~ .*ad-%s.*', $ad->id))
            ->addBanMessage(sprintf('obj.http.x-tags ~ .*position-%s.*', $ad->type_advertisement));

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
    public function removeVarnishCacheCurrentInstance(Event $event)
    {
        if (!$this->container->hasParameter('varnish')) {
            return false;
        }

        $instanceName = $this->container->get('core.instance')->internal_name;

        $this->container->get('varnish_ban_message_exchanger')
            ->addBanMessage(sprintf('obj.http.x-tags ~ instance-%s', $instanceName));
            // ->addBanMessage(sprintf('obj.http.x-tags ~ "instance-%s.*frontpage-page"', $instanceName))
            // ->addBanMessage(sprintf('obj.http.x-tags ~ "instance-%s.*globalcss"', $instanceName));
    }

    /**
     * Queues a varnish ban request to delete the frontpage
     *
     * @param Event $event The event to handle.
     **/
    public function removeVarnishCacheFrontpage(Event $event)
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
    public function removeVarnishCacheFrontpageCSS(Event $event)
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

    /**
     * Removes the instance from cache.
     *
     * @param Event $event The event object.
     */
    public function removeCacheForInstance(Event $event)
    {
        $instance = $event->getArgument('instance');

        $this->container->get('cache.manager')->getConnection('manager')
            ->remove($instance->domains);
    }

    /**
     * Removes the list of countries for manager from cache.
     *
     * @param Event $event The event object.
     */
    public function removeCountries(Event $event)
    {
        $this->container->get('cache.manager')->getConnection('manager')
            ->removeByPattern('*countries*');
    }
}
