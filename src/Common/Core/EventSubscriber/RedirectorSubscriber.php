<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\EventSubscriber;

use Common\Core\Component\EventDispatcher\Event;
use Common\Model\Entity\Instance;
use Opennemas\Task\Component\Queue\Queue;
use Opennemas\Task\Component\Task\ServiceTask;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RedirectorSubscriber implements EventSubscriberInterface
{
    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The task queue service.
     *
     * @var Queue
     */
    protected $queue;

    /**
     * Initializes the RedirectorSubscriber.
     *
     * @param Instance  $instance The current instance.
     * @param TaskQueue $tq       The task queue service.
     */
    public function __construct(?Instance $instance, Queue $queue)
    {
        $this->instance = $instance;
        $this->queue    = $queue;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            'url.createItem' => [
                [ 'removeUrlsFromCache', 5 ],
                [ 'removeUrlsFromVarnish', 5 ]
            ],
            'url.deleteItem' => [
                [ 'removeUrlsFromCache', 5 ],
                [ 'removeUrlsFromVarnish', 5 ]
            ],
            'url.deleteList' => [
                [ 'removeUrlsFromCache', 5 ],
                [ 'removeUrlsFromVarnish', 5 ]
            ],
            'url.patchItem'  => [
                [ 'removeUrlsFromCache', 5 ],
                [ 'removeUrlsFromVarnish', 5 ]
            ],
            'url.patchList'  => [
                [ 'removeUrlsFromCache', 5 ],
                [ 'removeUrlsFromVarnish', 5 ]
            ],
            'url.updateItem' => [
                [ 'removeUrlsFromCache', 5 ],
                [ 'removeUrlsFromVarnish', 5 ]
            ]
        ];
    }

    /**
     * Removes all cached URLs stored by the core.redirector service in the
     * cache service.
     */
    public function removeUrlsFromCache()
    {
        $pattern = 'redirector*';

        $this->queue->push(new ServiceTask(
            'cache.connection.instance',
            'removeByPattern',
            [ $pattern ]
        ));
    }

    /**
     * Removes cached responses from varnish when one or more URLs are created,
     * updated or deleted.
     *
     * @param Event $event The event object.
     */
    public function removeUrlsFromVarnish($event)
    {
        $ids = [];

        if ($event->hasArgument('id')) {
            $ids[] = 'url-' . $event->getArgument('id');
        }

        if ($event->hasArgument('ids')) {
            $ids = array_merge($ids, array_map(function ($a) {
                return 'url-' . $a;
            }, $event->getArgument('ids')));
        }

        $request = sprintf('obj.http.x-tags ~ %s', implode('|', $ids));

        $this->queue->push(new ServiceTask(
            'core.varnish',
            'ban',
            [ $request ]
        ));
    }
}
