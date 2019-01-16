<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Handles all the events after content updates
 */
class CacheSubscriber implements EventSubscriberInterface
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
        $this->objectCacheHandler = $cache;
        $this->container          = $container;
        $this->logger             = $logger;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            'content.createItem' => [
                [ 'mockHookAction', 5 ],
            ],
            'content.updateItem' => [
                [ 'mockHookAction', 5 ],
            ],
            'content.updateList' => [
                [ 'mockHookAction', 5 ],
            ],
            'content.deleteItem' => [
                [ 'mockHookAction', 5 ],
            ],
            'content.deleteList' => [
                [ 'mockHookAction', 5 ],
            ],
            'content.patchItem'     => [
                [ 'mockHookAction', 5 ],
            ],
            'content.patchList' => [
                [ 'mockHookAction', 5 ],
            ],
        ];
    }

    /**
     * Mock action for hook eventsF
     *
     * @param Event $event The event to handle.
     *
     * @return boolean
     */
    public function mockHookAction(Event $event)
    {
        // var_dump($event);
        // die();
        // $this->logger->notice('Event called');
        // return true;
    }
}
