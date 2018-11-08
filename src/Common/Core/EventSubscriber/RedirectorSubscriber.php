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
     * Initializes the RedirectorSubscriber.
     *
     * @param Cache    $cache    The cache connection.
     * @param Instance $instance The current instance.
     */
    public function __construct(Cache $cache, Instance $instance)
    {
        $this->cache    = $cache;
        $this->instance = $instance;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            'url.createItem' => [ [ 'removeUrlsFromCache', 5 ] ],
            'url.deleteItem' => [ [ 'removeUrlsFromCache', 5 ] ],
            'url.deleteList' => [ [ 'removeUrlsFromCache', 5 ] ],
            'url.patchItem'  => [ [ 'removeUrlsFromCache', 5 ] ],
            'url.patchList'  => [ [ 'removeUrlsFromCache', 5 ] ],
            'url.updateItem' => [ [ 'removeUrlsFromCache', 5 ] ]
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
}
