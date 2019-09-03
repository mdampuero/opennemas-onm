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

use Common\Core\Component\Helper\TemplateCacheHelper;
use Common\Core\Component\Helper\VarnishHelper;
use Common\Orm\Entity\Instance;
use Onm\Cache\AbstractCache;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
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
     * @param Instance            $instance The current instance.
     * @param TemplateCacheHelper $th       The TemplateCacheHelper service.
     * @param VarnishHelper       $vh       The VarnishHelper service.
     * @param AbstractCache       $cache    The old cache connection.
     */
    public function __construct(
        Instance            $instance,
        TemplateCacheHelper $th,
        VarnishHelper       $vh,
        AbstractCache       $cache
    ) {
        $this->instance = $instance;
        $this->template = $th;
        $this->varnish  = $vh;
        $this->oldCache = $cache;
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
        $users = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        // TODO: Remove when using new ORM for users
        foreach ($users as $user) {
            $this->oldCache->delete('user-' . $user->id);
        }

        $this->template->deleteUsers($users);
        $this->template->deleteContentsByUsers($users);
        $this->varnish->deleteInstance($this->instance);
    }
}
