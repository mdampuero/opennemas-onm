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
            'user.deleteItem' => [ [ 'onUserDelete', 5 ], ],
            'user.deleteList' => [ [ 'onUserDelete', 5 ], ],
            'user.patchItem'  => [ [ 'onUserUpdate', 5 ], ],
            'user.patchList'  => [ [ 'onUserUpdate', 5 ], ],
            'user.updateItem' => [ [ 'onUserUpdate', 5 ], ]
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
