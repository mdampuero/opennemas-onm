<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Service;

use Common\ORM\Entity\Notification;

class NotificationService
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
     * Counts the number of notifications that match the criteria.
     *
     * @param arary $criteria The search criteria.
     *
     * @return Integer The number of notifications that match the criteria.
     */
    public function count($criteria)
    {
        return $this->container->get('orm.manager')
            ->getRepository('manager.notification')
            ->countBy($criteria);
    }

    /**
     * Creates a non-persisted notification basing on the pending comments.
     *
     * @param integer $comments The number of pending comments.
     *
     * @return Notification The notification.
     */
    public function getFromComments($comments)
    {
        $notification = new Notification();

        $notification->id          = time();
        $notification->instance_id = $this->container->get('instance')->id;
        $notification->fixed       = 1;
        $notification->generated   = 1;
        $notification->read        = 0;
        $notification->style       = 'info';
        $notification->type        = 'comment';
        $notification->start       = date('Y-m-d H:i:s');
        $notification->end         = date('Y-m-d H:i:s', time() + 86400);

        $notification->title = [
            CURRENT_LANGUAGE_SHORT => _('Comments'),
        ];

        $notification->body = [
            CURRENT_LANGUAGE_SHORT => sprintf(
                _('You have %s pending comments. Click <a href="%s">here</a> to moderate.'),
                $comments,
                $this->container->get('router')->generate('admin_comments')
            )
        ];


        return $notification;
    }

    /**
     * Creates a non-persisted notification basing on the instance information.
     *
     * @param Instance $instance The instance.
     *
     * @return Notification The notification.
     */
    public function getFromInstance($instance)
    {
        $notification = new Notification();

        $notification->id          = time();
        $notification->instance_id = $instance->id;
        $notification->creator     = 'cron.update_instances';
        $notification->fixed       = 1;
        $notification->generated   = 1;
        $notification->read        = 0;
        $notification->style       = 'warning';
        $notification->type        = 'info';
        $notification->start       = date('Y-m-d H:i:s');
        $notification->end         = date('Y-m-d H:i:s', time() + 86400);

        $notification->title = [
                CURRENT_LANGUAGE_SHORT => _('Instance information')
        ];

        $notification->body = [
            CURRENT_LANGUAGE_SHORT =>
                $this->getBody($instance, CURRENT_LANGUAGE_SHORT),
        ];

        return $notification;
    }

    /**
     * Returns a notification.
     *
     * @param integer $id The notification id.
     *
     * @return Notification The notification.
     */
    public function getItem($id)
    {
        return $this->container->get('orm.manager')
            ->getRepository('manager.notification')
            ->find($id);
    }

    /**
     * Returns a list of notifications.
     *
     * @param array $criteria The search criteria.
     * @param array $order    The order criteria.
     * @param array $epp      The number of elements per page.
     * @param array $page     The current page.
     *
     * @return array A list of notifications.
     */
    public function getList($criteria, $order = [], $epp = 10, $page = 1)
    {
        return $this->container->get('orm.manager')
            ->getRepository('manager.notification')
            ->findBy($criteria, $order, $epp, $page);
    }

    /**
     * Returns the notification body for the instance.
     *
     * @param Instance $instance The instance.
     *
     * @return string The notification body.
     */
    private function getBody($instance)
    {
        $body = '';

        if ($instance->users > 1) {
            $body .= sprintf(
                _('<li>You have %d activated users. The cost is %d €/day or %s €/month'),
                $instance->users,
                ($instance->users - 1) * 0.40,
                ($instance->users - 1) * 10
            );
        }

        if ($instance->page_views > 45000) {
            $body .= sprintf(_('<li>You have %d page views'), $instance->page_views);

            if ($instance->page_views > 50000) {
                $body .= sprintf(
                    _(' The cost is %d €/month'),
                    number_format($instance->page_views * 0.000075, 2)
                );
            }

            $body .= '</li>';
        }

        if ($instance->media_size > 450) {
            $body .= sprintf(
                _('<li>Your storage size is %d MB'),
                round($instance->media_size)
            );

            if ($instance->media_size > 500) {
                $body .= sprintf(
                    _(' The cost is %d €/month'),
                    number_format($instance->media_size * 0.01, 2)
                );
            }

            $body .= '</li>';
        }

        return '<ul>' . $body . '</ul>';
    }
}
