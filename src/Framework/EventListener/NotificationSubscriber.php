<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Framework\Component\EventDispatcher\Event;
use Framework\ORM\Entity\Notification;

class NotificationSubscriber implements EventSubscriberInterface
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the InstanceSubscriber.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            'notifications.get' => [
                [ 'getNotificationFromInstance', 10 ],
                [ 'getNotifications', 5 ]
            ],
            'notifications.getRead' => [
                [ 'getReadNotifications', 5 ],
            ]
        ];
    }

    /**
     * Returns the number of notifications that match the criteria.
     *
     * @param Event $event The event.
     */
    public function countNotifications(Event $event)
    {
        $criteria = $event->getArgument('criteria');

        $response = $this->container->get('orm.manager')
            ->getRepository('manager.notification')
            ->countBy($criteria);

        $event->setResponse($response);
    }

    /**
     * Returns the list of notifications basing on the instance information.
     *
     * @param Event $event The event.
     */
    public function getNotificationFromInstance(Event $event)
    {
        $instance = $this->container->get('instance');
        $response = $event->getResponse();

        if ($instance->users == 1
            && $instance->page_views < 45000
            && $instance->media_size < 450
        ) {
            return;
        }

        $response[] = $this->container->get('core.service.notification')
            ->getFromInstance($instance);

        $event->setResponse($response);
    }

    /**
     * Returns the list of notifications basing on the instance information.
     *
     * @param Event $event The event object.
     */
    public function getNotifications(Event $event)
    {
        $criteria = $event->getArgument('criteria');
        $order    = $event->getArgument('order');
        $epp      = $event->getArgument('epp');
        $page     = $event->getArgument('page');

        $response = array_merge(
            $event->getResponse(),
            $this->container->get('core.service.notification')
                ->getList($criteria, $order, $epp, $page)
        );

        $event->setResponse($response);
    }

    /**
     * Returns the list of read notifications.
     *
     * @param Event $event The event object.
     */
    public function getReadNotifications(Event $event)
    {
        $repository = $this->container->get('orm.manager')
            ->getRepository('user_notification');

        $notifications = $repository->findBy([
            'user_id' => [ [ 'value' => $event->getArgument('user_id') ] ]
        ]);

        $response = [];
        foreach ($notifications as $notification) {
            $response[$notification->notification_id] =
                $notification->read_time;
        }

        $event->setResponse($response);
    }
}
