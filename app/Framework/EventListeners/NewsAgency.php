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
namespace Framework\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Handles all the events after content updates
 *
 * @package Backend_EventListeners
 **/
class NewsAgency implements EventSubscriberInterface
{
    /**
     * Register the content event handler
     *
     * @return void
     **/
    public static function getSubscribedEvents()
    {
        return array(
            'cron.actions' => array(
                array('updateNewsAgency', 5),
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
    public function updateNewsAgency(Event $event)
    {
        $output = $event->output;
        $input  = $event->input;

        $output->writeln(' - Executing news agency actions');

        $synchronizer = new \Onm\Import\Synchronizer\Synchronizer();

        try {
            $message = $synchronizer->syncMultiple($servers);
            $event->output->writeln(' - '.$message);
        } catch (\Exception $e) {
            $output->writeln("\t<fg=White;bg=red>Migrating: ".$e->getMessage()."</fg=white;bg=red>");
        }

        $event->output->writeln(' - [DONE] news agency actions');

        return false;
    }
}
