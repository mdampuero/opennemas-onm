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
 * Handles all the events after content updates.
 */
class CronActionSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return array(
            'cron.actions' => [
                array('updateNewsAgency', 5),
            ],
        );
    }

    /**
     * Synchronizes all instances basing on news agencies configuration.
     *
     * @param Event $event The event to handle.
     *
     * @return boolean
     */
    public function updateNewsAgency(Event $event)
    {
        return false;
    }
}
