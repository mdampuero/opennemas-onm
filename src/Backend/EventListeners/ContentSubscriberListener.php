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
namespace Backend\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Handles all the events after content updates
 *
 * @package Backend_EventListeners
 **/
class ContentSubscriberListener implements EventSubscriberInterface
{
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
            // 'content.create' => array(),
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
        global $sc;
        $cacheHandler = $sc->get('cache');

        $content = $event->getArgument('content');

        $id = $content->id;
        $contentType = \underscore(get_class($content));

        $cacheHandler->delete(INSTANCE_UNIQUE_NAME . "_" . $contentType . "_" . $id);

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
                preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name) . '|' . $content->pk_article
            );

            // Deleting home cache files
            // if (isset($content->in_home) && $content->in_home) {
                $tplManager->delete('home|0');
            // }
            $tplManager->delete('home|RSS');
            $tplManager->delete('last|RSS');
            $tplManager->delete('blog|'.preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name));

            if (isset($content->frontpage)
                && $content->frontpage
            ) {
                $tplManager->delete(
                    preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name) . '|0'
                );
                $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $content->category_name) . '|RSS');
            }
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
        global $sc;
        if (!$sc->hasParameter('varnish')) {
            return false;
        }

        $content = $event->getArgument('content');

        $banRequest =
            'obj.http.x-tags ~ '.$content->id
            .' || obj.http.x-tags ~ sitemap '
            .' || obj.http.x-tags ~ rss ';


        $sc->setParameter('varnish_ban_request', $banRequest);
    }

    public function refreshFrontpage(Event $event)
    {
        $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);

        $content = $event->getArgument('content');

        if (isset($_REQUEST['category'])) {
            $ccm = \ContentCategoryManager::get_instance();
            $categoryName = $ccm->get_name($_REQUEST['category']);
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName) . '|RSS');
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName) . '|0');

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

    }
}
