<?php

namespace Api\EventSubscriber;

use Api\Helper\Cache\UserGroupCacheHelper;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserGroupSubscriber implements EventSubscriberInterface
{
    /**
     * The helper service.
     *
     * @var UserGroupCacheHelper
     */
    protected $helper;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'user_group.deleteItem' => [ [ 'onUserGroupDelete', 5 ], ],
            'user_group.deleteList' => [ [ 'onUserGroupDelete', 5 ], ],
            'user_group.patchItem'  => [ [ 'onUserGroupUpdate', 5 ], ],
            'user_group.patchList'  => [ [ 'onUserGroupUpdate', 5 ], ],
            'user_group.updateItem' => [ [ 'onUserGroupUpdate', 5 ], ]
        ];
    }

    /**
     * Initializes the UserGroupSubscriber.
     *
     * @param UserGroupCacheHelper $helper The helper to remove user-group
     *                                     caches.
     */
    public function __construct(UserGroupCacheHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Delete users from cache when user group changes in order to always have
     * the right updated privileges.
     */
    public function onUserGroupDelete()
    {
        $this->onUserGroupUpdate();
    }

    /**
     * Delete users from cache when user group changes in order to always have
     * the right updated privileges.
     */
    public function onUserGroupUpdate()
    {
        $this->helper->deleteUsers();
    }
}
