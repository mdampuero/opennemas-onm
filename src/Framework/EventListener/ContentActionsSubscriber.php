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
class ContentActionsSubscriber implements EventSubscriberInterface
{
    /**
     * Initializes the object
     *
     * @param AbstractCache   $cache  The cache service.
     * @param LoggerInterface $logger The logger service.
     */
    public function __construct($cache, $logger)
    {
        $this->cacheHandler = $cache;
        $this->logger       = $logger;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return array(
            'article.update' => array(
                array('deleteCustomCss', 5)
            ),
            'content.update' => array(
                array('deleteEntityRepositoryCache', 10),
                array('deleteContentMetaCache', 10),
                array('deleteSmartyCache', 5),
                array('sendVarnishRequestCleaner', 5),
            ),
            'content.set_positions' => array(
                array('refreshFrontpage', 10),
            ),
            'author.update' => array(
                array('deleteAllAuthorsCaches', 5),
                array('deleteUsersCache', 10),
            ),
            'opinion.update' => array(
                array('deleteOpinionUpdateCaches', 5),
            ),
            'opinion.create' => array(
                array('deleteOpinionCreateCaches', 5),
            ),
            'frontpage.save_position' => array(
                array('cleanFrontpage', 5),
                array('deleteCustomCss', 5),
                array('cleanFrontpageObjectCache', 5)
            ),
            'category.update' => array(
                array('deleteCustomCss', 5),
                array('deleteCategoryCache', 5)
            ),
            'user.update' => array(
                array('deleteUserCache', 10),
            ),
        );
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
        $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);

        $content = $event->getArgument('content');

        if (property_exists($content, 'pk_article')) {
            $tplManager->delete(
                preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name).'|'.$content->pk_article
            );

            // Deleting frontpage cache files
            $tplManager->delete('frontpage|home');
            $tplManager->delete('home|RSS');
            $tplManager->delete('last|RSS');
            $tplManager->delete(
                'blog|'.preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name)
            );
            $tplManager->delete(
                'frontpage|'.preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name)
            );
            $tplManager->delete(
                preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name) . '|RSS'
            );

            $this->cleanOpcode();
        } elseif (property_exists($content, 'pk_opinion')) {
            $tplManager->delete('opinion', 'opinion_frontpage.tpl');
            $tplManager->delete('blog', 'blog_frontpage.tpl');
        }
    }

    /**
     * Queues a varnish ban request.
     *
     * @param Event $event The event to handle.
     */
    public function sendVarnishRequestCleaner(Event $event)
    {
        global $kernel;
        $container = $kernel->getContainer();
        if (!$container->hasParameter('varnish')) {
            return false;
        }

        $content = $event->getArgument('content');

        $instanceName = getService('instance_manager')->current_instance->internal_name;


        $response = $kernel->getContainer()->get('varnish_ban_message_exchanger')
            ->addBanMessage("obj.http.x-instance ~ {$instanceName}");

        // $baseRequest = "obj.http.x-instance ~ {$instanceName} && ";
        // $response = $kernel->getContainer()->get('varnish_ban_message_exchanger')
        //     ->addBanMessage($baseRequest."obj.http.x-tags ~ {$content->id}")
        //     ->addBanMessage($baseRequest.'obj.http.x-tags ~ sitemap')
        //     ->addBanMessage('obj.http.x-tags ~ rss')
    }

    /**
     * Deletes the category frontpage when content positions are updated.
     *
     * @param Event $event The event to handle.
     */
    public function refreshFrontpage(Event $event)
    {
        $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);

        $content = $event->getArgument('content');

        if (isset($_REQUEST['category'])) {
            $ccm = \ContentCategoryManager::get_instance();
            $categoryName = $ccm->getName($_REQUEST['category']);
            $tplManager->delete(
                preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName) . '|RSS'
            );
            $tplManager->delete(
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
        $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);

        $category = $event->getArgument('category');

        if (isset($category)) {
            $ccm = \ContentCategoryManager::get_instance();

            if ($category == '0' || $category == 'home') {
                $categoryName = 'home';
            } elseif ($category == 'opinion') {
                $categoryName = 'opinion';
                $tplManager->delete($categoryName, 'opinion_frontpage.tpl');
            } else {
                $categoryName = $ccm->getName($category);
            }

            $categoryName = preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName);
            $categoryName = preg_replace('@-@', '', $categoryName);

            $tplManager->delete($categoryName . '|RSS');
            $tplManager->delete('last|RSS');

            $tplManager->delete('frontpage|'.$categoryName);
            $this->cacheHandler->delete('frontpage_elements_map_' . $category);

            $this->logger->notice("Cleaning frontpage cache for category: {$category} ($categoryName)");

            $this->cleanOpcode();
        }
    }

    /**
     * Deletes the Smarty cache when an author is updated.
     *
     * @param Event $event The event to handle.
     */
    public function deleteAllAuthorsCaches(Event $event)
    {
        $authorId = $event->getArgument('id');

        // Delete caches for all author opinions and frontpages
        $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
        // Get the list articles for this author
        $cm = new \ContentManager();
        $opinions = $cm->getOpinionArticlesWithAuthorInfo(
            'opinions.type_opinion=0 AND opinions.fk_author='.$authorId
            .' AND contents.available=1 and contents.content_status=1',
            'ORDER BY created DESC '
        );

        if (!empty($opinions)) {
            foreach ($opinions as &$opinion) {
                $tplManager->delete('opinion|'.$opinion['id']);
            }
        }
        // Delete opinions frontpage caches
        $tplManager->delete('opinion', 'opinion_frontpage.tpl');

        // Delete author frontpages caches
        $tplManager->delete(sprintf('%06d', $authorId), 'opinion_author_index.tpl');

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
        $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);

        $authorSlug = preg_replace('/[^a-zA-Z0-9\s]+/', '', $authorSlug);
        $tplManager->delete($authorSlug, 'blog_author_index.tpl');
        $tplManager->delete('opinion', 'opinion_frontpage.tpl');
        $tplManager->delete('opinion|'.$opinionId);
        $tplManager->delete('blog', 'blog_frontpage.tpl');
        $tplManager->delete('blog|'.$opinionId);
        $tplManager->delete(sprintf('%06d', $authorId), 'opinion_author_index.tpl');

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
        $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
        $tplManager->delete(sprintf('%06d', $authorId), 'opinion_author_index.tpl');
        $tplManager->delete('opinion', 'opinion_frontpage.tpl');
        $tplManager->delete('blog', 'blog_frontpage.tpl');

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
                $categoryManager = getService('category_repository');

                if (is_object($category)) {
                    $categoryName = $category->name;
                } else {
                    $category = $categoryManager->find($category);
                    $categoryName = $category->name;
                }
            }

            $this->cacheHandler->delete('custom_css|' . $categoryName);
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
        $id = $event->getArgument('category')->pk_content_category;
        $this->cacheHandler->delete('category_' . $id);

        $name = $event->getArgument('category')->name;
        $this->cacheHandler->delete('category_' . $name);
    }

    /**
     * Deletes the user from cache when he is updated.
     *
     * @param Event $event The event to handle.
     */
    public function deleteUserCache(Event $event)
    {
        $id = $event->getArgument('id');

        $this->cacheHandler->delete('user-' . $id);
    }
}
