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
        $this->cacheHandler = $cache;
        $this->container    = $container;
        $this->logger       = $logger;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
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
                ['removeObjectCacheCategoriesArray', 5],
                ['removeVarnishCacheCurrentInstance', 5],
            ],
            'category.delete' => [
                ['removeSmartyCacheGlobalCss', 5],
                ['removeSmartyCacheCategories', 5],
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
                // ['refreshFrontpage', 10], //This seems old code and the functions stinks
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
            'instance.update' => [
                ['removeVarnishInstanceCacheUsingInstance', 5],
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
     * Resets the PHP 5.5 Opcode if supported
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
        $this->cacheHandler->delete('user-' . $authorId);

        // Delete caches for all author opinions and frontpages
        $cacheManager = $this->container->get('template_cache_manager');
        $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));

        // Get the list articles for this author
        $cm = new \ContentManager();
        $opinions = $cm->getOpinionArticlesWithAuthorInfo(
            'opinions.type_opinion=0 AND opinions.fk_author='.$authorId
            .' AND contents.available=1 and contents.content_status=1',
            'ORDER BY created DESC '
        );

        if (!empty($opinions)) {
            foreach ($opinions as &$opinion) {
                $cacheManager->delete('opinion|'.$opinion['id']);
            }
        }
        // Delete opinions frontpage caches
        $cacheManager->delete('opinion', 'opinion_frontpage.tpl');

        // Delete author frontpages caches
        $cacheManager->delete(sprintf('%06d', $authorId), 'opinion_author_index.tpl');

        $this->cleanOpcode();
    }

    /**
     * Deletes cache for content_categories object
     */
    public function removeObjectCacheCategoriesArray()
    {
        $this->cacheHandler->delete(CACHE_PREFIX.'_content_categories');
    }

    /**
     * Deletes the content metadata from cache after it is updated.
     *
     * @param Event $event The event to handle.
     **/
    public function removeObjectCacheContentMeta(Event $event)
    {
        $content = $event->getArgument('content');

        $this->cacheHandler->delete("content-meta-" . $content->id);
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
        $contentType = \underscore(get_class($content));

        $this->cacheHandler->delete($contentType . "-" . $id);
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

        $this->cacheHandler->delete('frontpage_elements_map_'.$category);
    }

    /**
     * Deletes the user from cache when he is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeObjectCacheUser(Event $event)
    {
        $user = $event->getArgument('user');

        $this->cacheHandler->delete('user-' . $user->id);
    }

    /**
     * Cleans all the smarty cache elements.
     */
    public function removeSmartyCacheAll()
    {
        // Initialization of the frontend template object
        $frontpageTemplate = new \Template(TEMPLATE_USER);
        $frontpageTemplate->clearAllCache();
    }

    /**
     * Deletes Smarty caches when an opinion is created.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheAuthorOpinion(Event $event)
    {
        $authorId = $event->getArgument('authorId');

        // Delete caches for opinion frontpages and author frontpages
        $cacheManager = $this->container->get('template_cache_manager');
        $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));
        $cacheManager->delete(sprintf('%06d', $authorId), 'opinion_author_index.tpl');
        $cacheManager->delete('opinion', 'opinion_frontpage.tpl');
        $cacheManager->delete('blog', 'blog_frontpage.tpl');

        $this->cleanOpcode();
    }

    /**
     * Deletes a category from cache when it is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheCategories(Event $event)
    {
        $category = $event->getArgument('category');

        $cacheManager = $this->container->get('template_cache_manager');
        $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));

        // Delete smarty cache for RSS frontpage of category
        $cacheManager->delete($category->name.'|RSS');
        // Delete smarty cache for blog frontpage of category
        $cacheManager->delete('category|'.$category->name.'|1');
        // Delete smarty cache for manual frontpage of category
        $cacheManager->delete('frontpage|'.$category->name);
        // Delete object cache
        $this->cacheHandler->delete('category-' . $category->id);
    }

    /**
     * Deletes the Smarty cache when the updated content is an article.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheForContent(Event $event)
    {
        $cacheManager = $this->container->get('template_cache_manager');
        $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));

        $content = $event->getArgument('content');

        if (property_exists($content, 'pk_article')) {
            $cacheManager->delete(
                preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name).'|'.$content->pk_article
            );

            // Deleting frontpage cache files
            $cacheManager->delete('frontpage|home');
            $cacheManager->delete('home|RSS');
            $cacheManager->delete('last|RSS');
            $cacheManager->delete('instantArticles|RSS');
            $cacheManager->delete(
                'blog|'.preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name)
            );
            $cacheManager->delete(
                'frontpage|'.preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name)
            );
            $cacheManager->delete(
                'category|'.preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name)
            );
            $cacheManager->delete(
                preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name) . '|RSS'
            );

            $this->cleanOpcode();
        } elseif (property_exists($content, 'pk_opinion')) {
            $cacheManager->delete('opinion', 'opinion_frontpage.tpl');
            $cacheManager->delete('blog', 'blog_frontpage.tpl');
        } elseif (property_exists($content, 'pk_video')) {
            $cacheManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name).'|'.$content->id);
            $cacheManager->delete('home|1');
            $cacheManager->delete('videos|RSS');
        } elseif (property_exists($content, 'pk_album')) {
            $cacheManager->delete('albums|RSS');
        }
    }

    /**
     * Cleans the category frontpage given its id.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheForFrontpageOfCategory(Event $event)
    {
        // Clean smarty cache
        $cacheManager = $this->container->get('template_cache_manager');
        $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));

        $category = $event->getArgument('category');

        if (isset($category)) {
            $ccm = \ContentCategoryManager::get_instance();

            if ($category == '0' || $category == 'home') {
                $categoryName = 'home';
            } elseif ($category == 'opinion') {
                $categoryName = 'opinion';
                $cacheManager->delete($categoryName, 'opinion_frontpage.tpl');
            } else {
                $categoryName = $ccm->getName($category);
            }

            $categoryName = preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName);
            $categoryName = preg_replace('@-@', '', $categoryName);

            $cacheManager->delete($categoryName . '|RSS');
            $cacheManager->delete('last|RSS');

            $cacheManager->delete('frontpage|'.$categoryName);

            $this->logger->notice("Cleaning frontpage cache for category: {$category} ($categoryName)");

            $this->cleanOpcode();
        }
    }

    /**
     * Deletes custom CSS from cache when a category is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheGlobalCss(Event $event)
    {
        $cacheManager = $this->container->get('template_cache_manager');
        $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));
        $cacheManager->delete('css|global');
    }

    /**
     * Deletes Smarty caches when an opinion is updated.
     *
     * @param Event $event The event to handle.
     */
    public function removeSmartyCacheOpinion(Event $event)
    {
        $authorId = $event->getArgument('authorId');
        $authorSlug = $event->getArgument('authorSlug');
        $opinionId = $event->getArgument('opinionId');

        // Delete caches for opinion inner, opinion frontpages and author frontpages
        $cacheManager = $this->container->get('template_cache_manager');
        $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));

        $authorSlug = preg_replace('/[^a-zA-Z0-9\s]+/', '', $authorSlug);

        $cacheManager->delete('blog|'.$opinionId);
        $cacheManager->delete('blog', 'blog_frontpage.tpl');
        $cacheManager->delete($authorSlug, 'blog_author_index.tpl');

        $cacheManager->delete('opinion|'.$opinionId);
        $cacheManager->delete('opinion', 'opinion_frontpage.tpl');
        $cacheManager->delete(sprintf('%06d', $authorId), 'opinion_author_index.tpl');

        $this->cleanOpcode();
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

        $instanceName = $this->container->get('instance')->internal_name;

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
            $instanceName = $this->container->get('instance')->internal_name;

            $this->container->get('varnish_ban_message_exchanger')
                ->addBanMessage(sprintf('obj.http.x-tags ~ instance-%s.*frontpage-page.*', $instanceName));
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

        $instanceName = $this->container->get('instance')->internal_name;

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

        $instanceName = $event->getArgument('instance');

        $this->container->get('varnish_ban_message_exchanger')
            ->addBanMessage(sprintf('obj.http.x-tags ~ instance-%s.*', $instanceName));
    }

    /**
     * Deletes the category frontpage when content positions are updated.
     *
     * @param Event $event The event to handle.
     *
     * @todo  this code stinks!!!!!!!!!!!!!!!!!!!!!!!!!!1
     */
    // public function refreshFrontpage(Event $event)
    // {
    //     $cacheManager = $this->container->get('template_cache_manager');
    //     $cacheManager->setSmarty(new \Template(TEMPLATE_USER));

    //     if (isset($_REQUEST['category'])) {
    //         $ccm = \ContentCategoryManager::get_instance();
    //         $categoryName = $ccm->getName($_REQUEST['category']);
    //         $cacheManager->delete(
    //             preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName) . '|RSS'
    //         );
    //         $cacheManager->delete(
    //             'frontpage|'.preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName)
    //         );

    //         $this->cleanOpcode();
    //     }
    // }
}
