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
                array('onContentUpdate', 10),
                // array('onKernelResponseMid', 5),
                // array('onKernelResponsePost', 0),
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
    public function onContentUpdate(Event $event)
    {
        var_dump('content.update event fired and handled', $event->getArgument('content'));
        die();

        return false;
    }
}
