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

use Common\Cache\Core\Cache;
use Common\Core\Component\EventDispatcher\Event;
use Common\ORM\Entity\Instance;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Onm\Varnish\MessageExchanger;

class RedirectorSubscriber implements EventSubscriberInterface
{
    /**
     * The cache connection.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The Varnish message exchanger service.
     *
     * @var MessageExchanger
     */
    protected $varnish;

    /**
     * Initializes the RedirectorSubscriber.
     *
     * @param Cache    $cache    The cache connection.
     * @param Instance $instance The current instance.
     */
    public function __construct(Cache $cache, ?Instance $instance, MessageExchanger $varnish)
    {
        $this->cache    = $cache;
        $this->instance = $instance;
        $this->varnish  = $varnish;
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
        $pattern = '*' . $this->instance->internal_name . '_redirector*';

        $this->cache->removeByPattern($pattern);
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

        $this->varnish->addBanMessage(
            sprintf('obj.http.x-tags ~ %s', implode('|', $ids))
        );
    }
}
