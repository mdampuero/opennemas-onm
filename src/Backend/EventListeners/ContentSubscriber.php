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
class ContentSubscriber implements EventSubscriberInterface
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
            ),
            // 'store.order'     => array('onStoreOrder', 0),
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
            $tplManager->delete('blog|'.$content->category_name);

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
}
