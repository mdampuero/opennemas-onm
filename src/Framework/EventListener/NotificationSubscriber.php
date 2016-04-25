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
use Common\ORM\Entity\Notification;

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

        if ($instance->users > 1) {
            $response[] = $this->container->get('core.service.notification')
                ->getFromUsers($instance);
        }

        if ($instance->page_views > 50000) {
            $response[] = $this->container->get('core.service.notification')
                ->getFromView($instance);
        }

        if ($instance->media_size > 500) {
            $response[] = $this->container->get('core.service.notification')
                ->getFromMedia($instance);
        }

        $event->setResponse($response);
    }

    /**
     * Returns the list of notifications basing on the instance information.
     *
     * @param Event $event The event object.
     */
    public function getNotifications(Event $event)
    {
        $oql = $event->getArgument('oql');

        $response = $event->getResponse();
        if (!is_array($response)) {
            $response = [];
        }

        $response = array_merge(
            $response,
            $this->container->get('core.service.notification')->getList($oql)
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

        $oql = sprintf('user_id = %s', $event->getArgument('user_id'));

        $notifications = $repository->findBy($oql);

        $response = [];
        foreach ($notifications as $notification) {
            $response[$notification->notification_id] =
                $notification->read_time;
        }

        $event->setResponse($response);
    }
}
