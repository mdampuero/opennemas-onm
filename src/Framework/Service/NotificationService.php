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
            ->getRepository('Notification')
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

        $notification->id        = 'comments';
        $notification->instances = [ $this->container->get('core.instance')->id ];
        $notification->fixed     = 0;
        $notification->forced    = 1;
        $notification->read      = 0;
        $notification->style     = [
            'background_color' => '#0090d9',
            'font_color'       => '#ffffff',
            'icon'             => 'comment'
        ];

        $notification->type  = 'comment';
        $notification->start = new \DateTime();
        $notification->end   = new \Datetime(date('Y-m-d H:i:s', time() + 86400));

        $notification->title = [
            CURRENT_LANGUAGE_SHORT => sprintf(
                _('You have %s pending comments. Click <a href="%s">here</a> to moderate.'),
                $comments,
                $this->container->get('router')->generate('backend_comments')
            )
        ];

        $notification->body = [
            CURRENT_LANGUAGE_SHORT => sprintf(
                _('You have %s pending comments. Click <a href="%s">here</a> to moderate.'),
                $comments,
                $this->container->get('router')->generate('backend_comments')
            )
        ];


        return $notification;
    }

    /**
     * Creates a non-persisted notification basing on the instance media.
     *
     * @param Instance $instance The instance.
     *
     * @return Notification The notification.
     */
    public function getFromMedia($instance)
    {
        $notification = new Notification();

        $notification->id        = 'media';
        $notification->instances = [ $instance->id ];
        $notification->creator   = 'cron.update_instances';
        $notification->fixed     = 0;
        $notification->forced    = 1;
        $notification->generated = 1;
        $notification->read      = 0;
        $notification->style     = 'danger';
        $notification->type      = 'info';
        $notification->start     = new \DateTime();
        $notification->end       = new \Datetime(date('Y-m-d H:i:s', time() + 86400));

        $notification->title = [
            CURRENT_LANGUAGE_SHORT =>
                sprintf(_('Your are using %d Mb of storage.'), $instance->media_size)
        ];

        $notification->body = [
            CURRENT_LANGUAGE_SHORT => '<li>'
                . sprintf(_('Your are using %d Mb of storage.'), $instance->media_size)
                . sprintf(
                    _('Note that <a href="http://help.opennemas.com/knowledgebase/articles/666994'
                    . '-pricing-opennemas-page-views-and-storage-space" target="_blank" '
                    . 'title="Learn more">the cost %s € Mb/month</a>.'),
                    0.01
                )
            . '</li>'
        ];

        return $notification;
    }

    /**
     * Creates a non-persisted notification basing on the instance users.
     *
     * @param Instance $instance The instance.
     *
     * @return Notification The notification.
     */
    public function getFromUsers($instance)
    {
        $notification = new Notification();

        $notification->id        = 'users';
        $notification->instances = [ $instance->id ];
        $notification->creator   = 'cron.update_instances';
        $notification->fixed     = 0;
        $notification->forced    = 1;
        $notification->generated = 1;
        $notification->read      = 0;
        $notification->style     = [
            'background_color' => '#f35958',
            'font_color'       => '#ffffff',
            'icon'             => 'user'
        ];
        $notification->type      = 'info';
        $notification->start     = new \DateTime();
        $notification->end       = new \Datetime(date('Y-m-d H:i:s', time() + 86400));

        $notification->title = [
            CURRENT_LANGUAGE_SHORT =>
                sprintf(_('You have %d activated users.'), $instance->users)
        ];

        $notification->body = [
            CURRENT_LANGUAGE_SHORT => '<li>'
                . sprintf(_('You have %d activated users.'), $instance->users)
                . sprintf(
                    _(
                        'Note that <a href="http://help.opennemas.com/knowledgebase/articles/566172-pricing-'
                        . 'opennemas-user-licenses" '
                        . 'target="_blank" title="Learn more">the cost is %s € user/month</a>.'
                    ),
                    12
                )
            . '</li>'
        ];

        return $notification;
    }

    /**
     * Creates a non-persisted notification basing on the page views.
     *
     * @param Instance $instance The instance.
     *
     * @return Notification The notification.
     */
    public function getFromViews($instance)
    {
        $notification = new Notification();

        $notification->id        = 'views';
        $notification->instances = [ $instance->id ];
        $notification->creator   = 'cron.update_instances';
        $notification->fixed     = 0;
        $notification->forced    = 1;
        $notification->generated = 1;
        $notification->read      = 0;
        $notification->style     = 'danger';
        $notification->type      = 'info';
        $notification->start     = new \DateTime();
        $notification->end       = new \Datetime(date('Y-m-d H:i:s', time() + 86400));

        $notification->title = [
            CURRENT_LANGUAGE_SHORT =>
                sprintf(_('This month you\'re recording %d page views. '), $instance->page_views)
        ];

        $notification->body = [
            CURRENT_LANGUAGE_SHORT =>
                '<li>'
                . sprintf(_('This month you\'re recording %d page views. '), $instance->page_views)
                . sprintf(
                    _('Note that <a href="http://help.opennemas.com/knowledgebase/articles/666994-pricing-'
                    . 'opennemas-page-views-and-storage-space" target="_blank" title="Learn more">'
                    . 'the cost %s € pv/month</a>.'),
                    number_format(0.00009, 5)
                )
                . '</li>'
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
            ->getRepository('Notification')
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
    public function getList($oql)
    {
        return $this->container->get('orm.manager')
            ->getRepository('Notification')
            ->findBy($oql);
    }
}
