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
            // Article hooks
            'article.update' => [
                ['deleteCustomCss', 5]
            ],
            // Author hooks
            'author.create' => [
                ['mockHookAction', 0],
            ],
            'author.update' => [
                ['deleteAllAuthorsCaches', 5],
            ],
            'author.delete' => [
                ['mockHookAction', 0],
            ],
            // Category hooks
            'category.create' => [
                ['deleteCategoriesArrayCache', 5]
            ],
            'category.update' => [
                ['deleteCustomCss', 5],
                ['deleteCategoryCache', 5],
                ['deleteCategoriesArrayCache', 5]
            ],
            'category.delete' => [
                ['deleteCustomCss', 5],
                ['deleteCategoryCache', 5],
                ['deleteCategoriesArrayCache', 5]
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
            'content.repository-delete' => [
                ['deleteEntityRepositoryCache', 5]
            ],
            'content.create' => [
                ['mockHookAction', 0],
            ],
            'content.update' => [
                ['deleteEntityRepositoryCache', 10],
                ['deleteContentMetaCache', 10],
                ['deleteSmartyCache', 5],
                ['sendVarnishRequestCleaner', 5],
            ],
            'content.delete' => [
                ['mockHookAction', 0],
            ],
            'content.set_positions' => [
                ['refreshFrontpage', 10],
            ],
            // Frontpage hooks
            'frontpage.save_position' => [
                ['cleanFrontpage', 5],
                ['deleteCustomCss', 5],
                ['cleanFrontpageObjectCache', 5],
                ['sendVarnishRequestCleaner', 5],
            ],
            'frontpage.pick_layout' => [
                ['mockHookAction', 0],
            ],
            // Instance hooks
            'instance.update' => [
                ['sendVarnishRequestCleanerWithInternalName', 5],
            ],
            // Menu hooks
            'menu.create' => [
                ['mockHookAction', 0],
            ],
            'menu.update' => [
                ['deleteEntityRepositoryCache', 5],
                ['cleanAllSmartyCache', 5],
                ['sendVarnishRequestCleaner', 5],
            ],
            'menu.delete' => [
                ['mockHookAction', 0],
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
                ['deleteOpinionUpdateCaches', 5],
            ],
            'opinion.create' => [
                ['deleteOpinionCreateCaches', 5],
            ],
            // Setting hooks
            'setting.update' => [
                ['mockHookAction', 0],
            ],
            // User hooks
            'user.create' => [
                ['mockHookAction', 0],
            ],
            'user.update' => [
                ['deleteUserCache', 10],
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
     * Mock action for hook events
     *
     * @param Event $event The event to handle.
     */
    public function mockHookAction(Event $event)
    {
        return true;
    }

    /**
     * Deletes a content from cache after it is updated.
     *
     * @param Event $event The event to handle.
     */
    public function deleteEntityRepositoryCache(Event $event)
    {
        $content = $event->getArgument('content');

        $id = $content->id;
        $contentType = \underscore(get_class($content));

        $this->cacheHandler->delete($contentType . "-" . $id);
    }

    /**
     * Deletes the content metadata from cache after it is updated.
     *
     * @param Event $event The event to handle.
     **/
    public function deleteContentMetaCache(Event $event)
    {
        $content = $event->getArgument('content');

        $this->cacheHandler->delete("content-meta-" . $content->id);
    }

    /**
     * Deletes the Smarty cache when the updated content is an article.
     *
     * @param Event $event The event to handle.
     */
    public function deleteSmartyCache(Event $event)
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
        }
    }

    /**
     * Queues a varnish ban request.
     *
     * @param Event $event The event to handle.
     */
    public function sendVarnishRequestCleaner(Event $event)
    {
        if (!$this->container->hasParameter('varnish')) {
            return false;
        }

        $instanceName = $this->container->get('instance')->internal_name;

        $this->container->get('varnish_ban_message_exchanger')
            ->addBanMessage("obj.http.x-instance ~ {$instanceName}")
            ->addBanMessage("obj.http.x-tags ~ frontpage-page")
            ->addBanMessage("obj.http.x-tags ~ globalcss");
    }

    /**
     * Queues a varnish ban request.
     *
     * @param Event $event The event to handle.
     */
    public function sendVarnishRequestCleanerWithInternalName(Event $event)
    {
        if (!$this->container->hasParameter('varnish')) {
            return false;
        }

        $instanceName = $event->getArgument('instance');

        $this->container->get('varnish_ban_message_exchanger')
            ->addBanMessage("obj.http.x-instance ~ {$instanceName}")
            ->addBanMessage("obj.http.x-tags ~ frontpage-page")
            ->addBanMessage("obj.http.x-tags ~ globalcss");
    }

    /**
     * Deletes the category frontpage when content positions are updated.
     *
     * @param Event $event The event to handle.
     */
    public function refreshFrontpage(Event $event)
    {
        $cacheManager = $this->container->get('template_cache_manager');
        $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));

        if (isset($_REQUEST['category'])) {
            $ccm = \ContentCategoryManager::get_instance();
            $categoryName = $ccm->getName($_REQUEST['category']);
            $cacheManager->delete(
                preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName) . '|RSS'
            );
            $cacheManager->delete(
                'frontpage|'.preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName)
            );

            $this->cleanOpcode();
        }
    }

    /**
     * Cleans the category frontpage given its id.
     *
     * @param Event $event The event to handle.
     */
    public function cleanFrontpage(Event $event)
    {
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
            $this->cacheHandler->delete('frontpage_elements_map_' . $category);

            $this->logger->notice("Cleaning frontpage cache for category: {$category} ($categoryName)");

            $this->cleanOpcode();
        }
    }

    /**
     * Cleans all the smarty cache elements.
     */
    public function cleanAllSmartyCache()
    {
        // Initialization of the frontend template object
        $frontpageTemplate = new \Template(TEMPLATE_USER);
        $frontpageTemplate->clearAllCache();
    }

    /**
     * Deletes the Smarty cache when an author is updated.
     *
     * @param Event $event The event to handle.
     */
    public function deleteAllAuthorsCaches(Event $event)
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
     * Deletes Smarty caches when an opinion is updated.
     *
     * @param Event $event The event to handle.
     */
    public function deleteOpinionUpdateCaches(Event $event)
    {
        $authorId = $event->getArgument('authorId');
        $authorSlug = $event->getArgument('authorSlug');
        $opinionId = $event->getArgument('opinionId');

        // Delete caches for opinion inner, opinion frontpages and author frontpages
        $cacheManager = $this->container->get('template_cache_manager');
        $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));

        $authorSlug = preg_replace('/[^a-zA-Z0-9\s]+/', '', $authorSlug);
        $cacheManager->delete($authorSlug, 'blog_author_index.tpl');
        $cacheManager->delete('opinion', 'opinion_frontpage.tpl');
        $cacheManager->delete('opinion|'.$opinionId);
        $cacheManager->delete('blog', 'blog_frontpage.tpl');
        $cacheManager->delete('blog|'.$opinionId);
        $cacheManager->delete(sprintf('%06d', $authorId), 'opinion_author_index.tpl');

        $this->cleanOpcode();
    }

    /**
     * Deletes Smarty caches when an opinion is created.
     *
     * @param Event $event The event to handle.
     */
    public function deleteOpinionCreateCaches(Event $event)
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
     * Resets the PHP 5.5 Opcode if supported
     */
    public function cleanOpcode()
    {
        if (extension_loaded('Zend Opcache')) {
            opcache_reset();
        }
    }

    /**
     * Deletes custom CSS from cache when a category is updated.
     *
     * @param Event $event The event to handle.
     */
    public function deleteCustomCss(Event $event)
    {
        $category = $event->getArgument('category');

        if (isset($category)) {
            if ($category == '0' || $category == 'home') {
                $categoryName = 'home';
            } elseif ($category == 'opinion') {
                $categoryName = 'opinion';
            } else {
                $categoryManager = $this->container->get('category_repository');

                if (is_object($category)) {
                    $categoryName = $category->name;
                } else {
                    $category = $categoryManager->find($category);
                    $categoryName = $category->name;
                }
            }

            $cacheManager = $this->container->get('template_cache_manager');
            $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));
            $cacheManager->delete('css|global|' . $categoryName);
        }
    }

    /**
     * Deletes the list of objects in cache for a frontpage when content
     * positions are updated.
     *
     * @param Event $event The event to handle.
     */
    public function cleanFrontpageObjectCache(Event $event)
    {
        $category = $event->getArgument('category');

        $this->cacheHandler->delete('frontpage_elements_map_'.$category);
    }

    /**
     * Deletes a category from cache when it is updated.
     *
     * @param Event $event The event to handle.
     */
    public function deleteCategoryCache(Event $event)
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
     * Deletes cache for content_categories object
     */
    public function deleteCategoriesArrayCache()
    {
        $this->cacheHandler->delete(CACHE_PREFIX.'_content_categories');
    }

    /**
     * Deletes the user from cache when he is updated.
     *
     * @param Event $event The event to handle.
     */
    public function deleteUserCache(Event $event)
    {
        $user = $event->getArgument('user');

        $this->cacheHandler->delete('user-' . $user->id);
    }
}
