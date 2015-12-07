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
     * @param Event $event The event object.
     */
    public function getNotificationFromInstance(Event $event)
    {
        $instance = $this->container->get('instance');
        $response = $event->getResponse();

        if ($instance->users === 1
            && $instance->page_views < 45000
            && $instance->media_size < 450
        ) {
            return;
        }

        $notification = new Notification();

        $notification->id          = time();
        $notification->instance_id = $instance->id;
        $notification->creator     = 'cron.update_instances';
        $notification->fixed       = 1;
        $notification->generated   = 1;
        $notification->style       = 'warning';
        $notification->type        = 'info';
        $notification->start       = date('Y-m-d H:i:s');
        $notification->end         = date('Y-m-d H:i:s', time() + 86400);

        $notification->title = [
            'en' => 'Instance information',
            'es' => 'Información de la instancia',
            'gl' => 'Información da instancia',
        ];

        $notification->body = [
            'en' => $this->getBody($instance, 'en'),
            'es' => $this->getBody($instance, 'es'),
            'gl' => $this->getBody($instance, 'gl')
        ];

        $response[] = $notification;

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
        $epp      = $event->getArgument('epp');
        $page     = $event->getArgument('page');

        $response = array_merge(
            $event->getResponse(),
            $this->container->get('orm.manager')
                ->getRepository('manager.notification')
                ->findBy($criteria, [ 'fixed' => 'desc' ], $epp, $page)
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

    /**
     * Returns the notification body for the instance.
     *
     * @param Instance $instance The instance object.
     * @param string   $language The language of the body.
     *
     * @return string The notification body.
     */
    private function getBody($instance, $language)
    {
        $body = '';

        if ($language === 'en') {
            if ($instance->users > 1) {
                $body .= sprintf(
                    '<li>You have %d activated users. The cost is %d €/day or %s €/month',
                    $instance->users,
                    ($instance->users - 1) * 0.40,
                    ($instance->users - 1) * 10
                );
            }

            if ($instance->page_views > 45000) {
                $body .= sprintf('<li>You have %d page views', $instance->page_views);

                if ($instance->page_views > 50000) {
                    $body .= sprintf(
                        ' The cost is %d €/month',
                        number_format($instance->page_views * 0.000075, 2)
                    );
                }

                $body .= '</li>';
            }

            if ($instance->media_size > 450) {
                $body .= sprintf(
                    '<li>Your storage size is %d MB',
                    round($instance->media_size)
                );

                if ($instance->media_size > 500) {
                    $body .= sprintf(
                        ' The cost is %d €/month',
                        number_format($instance->media_size * 0.01, 2)
                    );
                }

                $body .= '</li>';
            }

            $body = '<ul>' . $body . '</ul>';

            return $body;
        }

        if ($language === 'es') {
            if ($instance->users > 1) {
                $body .= sprintf(
                    '<li>Tienes %d usuarios activados. El coste es de %d €/día o %s €/mes',
                    $instance->users,
                    ($instance->users - 1) * 0.40,
                    ($instance->users - 1) * 10
                );
            }

            if ($instance->page_views > 45000) {
                $body .= sprintf('<li>Tienes %d páginas vistas', $instance->page_views);

                if ($instance->page_views > 50000) {
                    $body .= sprintf(
                        ' El coste es de %d €/mes',
                        number_format($instance->page_views * 0.000075, 2)
                    );
                }

                $body .= '</li>';
            }

            if ($instance->media_size > 450) {
                $body .= sprintf(
                    '<li>El tamaño ocupado es de %d MB',
                    round($instance->media_size)
                );

                if ($instance->media_size > 500) {
                    $body .= sprintf(
                        ' El coste es de %d €/mes',
                        number_format($instance->media_size * 0.01, 2)
                    );
                }

                $body .= '</li>';
            }

            $body = '<ul>' . $body . '</ul>';

            return $body;
        }

        if ($language === 'gl') {
            if ($instance->users > 1) {
                $body .= sprintf(
                    '<li>Tes %d usuarios activados. O custo é de %d €/día ou %s €/mes',
                    $instance->users,
                    ($instance->users - 1) * 0.40,
                    ($instance->users - 1) * 10
                );
            }

            if ($instance->page_views > 45000) {
                $body .= sprintf('<li>Tes %d páxinas vistas', $instance->page_views);

                if ($instance->page_views > 50000) {
                    $body .= sprintf(
                        ' O custo é de %d €/mes',
                        number_format($instance->page_views * 0.000075, 2)
                    );
                }

                $body .= '</li>';
            }

            if ($instance->media_size > 450) {
                $body .= sprintf(
                    '<li>O tamaño ocupado é de %d MB',
                    round($instance->media_size)
                );

                if ($instance->media_size > 500) {
                    $body .= sprintf(
                        ' O custo é de %d €/mes',
                        number_format($instance->media_size * 0.01, 2)
                    );
                }

                $body .= '</li>';
            }

            $body = '<ul>' . $body . '</ul>';

            return $body;
        }
    }
}
