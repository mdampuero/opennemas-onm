<?php

namespace Api\EventSubscriber;

use Api\Helper\Cache\UserCacheHelper;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * The helper service.
     *
     * @var UserCacheHelper
     */
    protected $helper;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'user.createItem' => [
                ['logAction', 5],
            ],
            'user.deleteItem' => [
                ['logAction', 5],
                [ 'onUserDelete', 5 ],
            ],
            'user.deleteList' => [
                ['logAction', 5],
                [ 'onUserDelete', 5 ],
            ],
            'user.patchItem'  => [
                ['logAction', 5],
                [ 'onUserUpdate', 5 ],
            ],
            'user.patchList'  => [
                ['logAction', 5],
                [ 'onUserUpdate', 5 ],
            ],
            'user.updateItem' => [
                ['logAction', 5],
                [ 'onUserUpdate', 5 ],
            ],
            'user.moveItem' => [
                ['logAction', 5],
                [ 'onUserMove', 5 ],
            ]
        ];
    }

    /**
     * Initializes the UserSubscriber.
     *
     * @param Container       $container The service container.
     * @param UserCacheHelper $helper The helper to remove user caches.
     * @param Cache           $redis  The cache service for redis.
     */
    public function __construct($container, UserCacheHelper $helper, $redis)
    {
        $this->container = $container;
        $this->helper    = $helper;
        $this->redis     = $redis;
    }

    /**
     * Logs the action.
     *
     * @param Event $event The event object.
     */
    public function logAction(Event $event)
    {
        if (empty($event->hasArgument('action'))) {
            return;
        }

        $action = $event->getArgument('action');
        $users  = is_array($event->getArgument('item'))
            ? $event->getArgument('item')
            : [ $event->getArgument('item') ];

        if (!empty($users)) {
            foreach ($users as $content) {
                logContentEvent($action, $content);
            }

            return;
        }
    }

    /**
     * Delete caches for users and contents created by users when a user or a
     * list of users is updated.
     *
     * @param Event $event The dispatched event.
     */
    public function onUserDelete(Event $event)
    {
        $this->onUserUpdate($event);
    }

    /**
     * Delete caches for users and contents created by users when a user or a
     * list of users is updated.
     *
     * @param Event $event The dispatched event.
     */
    public function onUserUpdate(Event $event)
    {
        $users = is_array($event->getArgument('item'))
            ? $event->getArgument('item')
            : [ $event->getArgument('item') ];

        foreach ($users as $user) {
            $this->helper->deleteItem($user);
            if ($user->type == 1) {
                continue;
            }
            $this->helper->deleteItemVarnish($user);
        }
    }

    /**
     * Removes contents from cache, user list actions and varnish caches for
     * the instance after moving contents from a user to another.
     *
     * @param Event $event The dispatched event.
     */
    public function onUserMove(Event $event)
    {
        if (!$event->hasArgument('contents')) {
            return;
        }
        $contents = $event->getArgument('contents');
        $cacheIds = [];
        foreach ($contents as $content) {
            $cacheIds[] = 'content-' . $content['id'];
            $cacheIds[] = $content['type'] . '-' . $content['id'];
        }
        $source = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');
        foreach ($source as $user) {
            $this->helper->deleteItemVarnish($user);
        }
        foreach ($cacheIds as $cacheId) {
            $this->redis->remove($cacheId);
        }
        $this->helper->deleteInstance();
    }
}
