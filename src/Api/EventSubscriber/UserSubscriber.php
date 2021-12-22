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
            ]
        ];
    }

    /**
     * Initializes the UserSubscriber.
     *
     * @param UserCacheHelper $helper The helper to remove user caches.
     */
    public function __construct(UserCacheHelper $helper)
    {
        $this->helper = $helper;
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

        // TODO: Remove when using new ORM for users
        foreach ($users as $user) {
            $this->helper->deleteItem($user);
        }

        $this->helper->deleteInstance();
    }
}
