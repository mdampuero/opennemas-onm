<?php
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

class ContentSubscriber implements EventSubscriberInterface
{
    static public function getSubscribedEvents()
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

    public function onContentUpdate(Event $event)
    {
        var_dump('content.update event fired and handled', $event->getArgument('content'));die();

        return false;
    }
}
