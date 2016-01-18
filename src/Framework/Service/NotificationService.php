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

use Framework\ORM\Entity\Notification;

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
                CURRENT_LANGUAGE_SHORT => _('Usage of your newspaper')
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
            $body .=
                '<li>'
                .sprintf(
                    _('You have %d activated users. Note that <a href="http://help.opennemas.com/knowledgebase/articles/566172-pricing-opennemas-user-licenses" target="_blank" title="Learn more">the cost is %s € user/month</a>'),
                    $instance->users,
                    12
                )
                .'</li>';
        }

        if ($instance->page_views > 45000) {
            $body .=
                '<li>'
                .sprintf(_('This month you\'re recording %d page views. '), $instance->page_views);

            if ($instance->page_views > 50000) {
                $body .= sprintf(
                    _('Note that <a href="http://help.opennemas.com/knowledgebase/articles/666994-pricing-opennemas-page-views-and-storage-space" target="_blank" title="Learn more">the cost %s € pv/month</a>.'),
                    number_format(0.00009, 5)
                );
            }

            $body .= '</li>';
        }

        if ($instance->media_size > 450) {

            $body .= '<li>'.sprintf(
                _('Your are using %d Mb of storage. '),
                $instance->media_size
            );

            if ($instance->media_size > 500) {
                $body .= sprintf(
                    _('Note that <a href="http://help.opennemas.com/knowledgebase/articles/666994-pricing-opennemas-page-views-and-storage-space" target="_blank" title="Learn more">the cost %s € Mb/month</a>.'),
                    0.01
                );
            }

            $body .= '</li>';
        }

        return '<ul>' . $body . '</ul>';
    }
}
