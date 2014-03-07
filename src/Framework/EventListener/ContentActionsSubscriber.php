<?php
/**
 * Handles all the events after content updates
 *
 * @package Backend_EventListeners
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Handles all the events after content updates
 *
 * @package Backend_EventListeners
 **/
class ContentActionsSubscriber implements EventSubscriberInterface
{
    /**
     * Initializes the object
     *
     * @return void
     **/
    public function __construct($cache, $logger)
    {
        $this->cacheHandler = $cache;
        $this->logger       = $logger;
    }

    /**
     * Register the content event handler
     *
     * @return void
     **/
    public static function getSubscribedEvents()
    {
        return array(
            'content.update' => array(
                array('deleteEntityRepositoryCache', 10),
                array('deleteSmartyCache', 5),
                array('sendVarnishRequestCleaner', 5),
            ),
            'article.update' => array(
                array('deleteCustomCss', 5)
            ),
            'content.set_positions' => array(
                array('refreshFrontpage', 10),
            ),
            'author.update' => array(
                array('deleteAllAuthorsCaches', 5),
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
                array('deleteCustomCss', 5)
            )
        );
    }

    /**
     * Perform the actions after update a content
     *
     * @param Event $event The event to handle
     *
     * @return void
     **/
    public function deleteEntityRepositoryCache(Event $event)
    {
        $content = $event->getArgument('content');

        $id = $content->id;
        $contentType = \underscore(get_class($content));

        $this->cacheHandler->delete($contentType . "_" . $id);

        $this->cleanOpcode();

        return false;
    }

    /**
     * Perform the actions after update a content
     *
     * @param Event $event The event to handle
     *
     * @return void
     **/
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
        }

        return false;
    }

    /**
     * Queues a varnish ban request
     *
     * @return void
     **/
    public function sendVarnishRequestCleaner(Event $event)
    {
        global $kernel;
        $container = $kernel->getContainer();
        if (!$container->hasParameter('varnish')) {
            return false;
        }

        $content = $event->getArgument('content');

        $banRequest = 'obj.http.x-tags ~ '.$content->id;
        $kernel->getContainer()->get('varnish_ban_message_exchanger')->addBanMessage($banRequest);
        $banRequest = 'obj.http.x-tags ~ sitemap ';
        $kernel->getContainer()->get('varnish_ban_message_exchanger')->addBanMessage($banRequest);
        $banRequest = 'obj.http.x-tags ~ rss ';
        $kernel->getContainer()->get('varnish_ban_message_exchanger')->addBanMessage($banRequest);
    }

    public function refreshFrontpage(Event $event)
    {
        $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);

        $content = $event->getArgument('content');

        if (isset($_REQUEST['category'])) {
            $ccm = \ContentCategoryManager::get_instance();
            $categoryName = $ccm->get_name($_REQUEST['category']);
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
     * Cleans the category frontpage given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
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
                $categoryName = $ccm->get_name($category);
            }

            $categoryName = preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName);
            $categoryName = preg_replace('@-@', '', $categoryName);

            $tplManager->delete($categoryName . '|RSS');

            $tplManager->delete('frontpage|'.$categoryName);
            $this->cacheHandler->delete('frontpage_elements_' . $category);

            $this->logger->notice("Cleaning frontpage cache for category: {$category} ($categoryName)");

            $this->cleanOpcode();
        }
    }

    /**
     * Perform the actions after update an author
     *
     * @param Event $event The event to handle
     *
     * @return void
     **/
    public function deleteAllAuthorsCaches(Event $event)
    {
        $authorId = $event->getArgument('authorId');

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
     * Perform the actions after update an author
     *
     * @param Event $event The event to handle
     *
     * @return void
     **/
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
     * Perform the actions after update an author
     *
     * @param Event $event The event to handle
     *
     * @return void
     **/
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
     *
     **/
    public function cleanOpcode()
    {
        if (extension_loaded('Zend Opcache')) {
            opcache_reset();
        }
    }

    /**
     * Deletes custom css from cache.
     *
     * @param Event $event
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
                $ccm = \ContentCategoryManager::get_instance();
                $categoryName = $ccm->get_name($category);
            }

            $this->cacheHandler->delete('custom_css|' . $categoryName);
        }
    }

    /**
     * Deletes the list of objects in cache for a frontpage.
     *
     * @return void
     * @author
     **/
    public function cleanFrontpageObjectCache(Event $event)
    {
        $category = $event->getArgument('category');

        $this->cacheHandler->delete('fronpage_elements_'.$category);
    }
}
