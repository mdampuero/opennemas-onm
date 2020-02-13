<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\EventSubscriber;

use Api\Service\V1\RedisService;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserGroupSubscriber implements EventSubscriberInterface
{
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
     * @param RedisService $service The api.service.redis service.
     */
    public function __construct(RedisService $service)
    {
        $this->service = $service;
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
        $this->service->deleteItemByPattern('user-*');
    }
}
