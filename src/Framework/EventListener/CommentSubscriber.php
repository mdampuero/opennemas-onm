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
use Common\Core\Component\EventDispatcher\Event;

class CommentSubscriber implements EventSubscriberInterface
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the CommentSubscriber.
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
            ]
        ];
    }

    /**
     * Returns the list of notifications basing on the pending comments.
     *
     * @param Event $event The event object.
     */
    public function getNotifications(Event $event)
    {
        $system = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('comment_system');

        if (empty($system) || $system !== 'onm') {
            return;
        }

        $response = $event->getResponse();
        $comments = $this->container->get('comment_repository')
            ->countPendingComments();

        if ((int) $comments === 0) {
            return;
        }

        $response[] = $this->container->get('core.service.notification')
            ->getFromComments($comments);

        $event->setResponse($response);
    }
}
