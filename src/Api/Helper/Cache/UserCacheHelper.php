<?php

namespace Api\Helper\Cache;

use Common\Model\Entity\User;
use Common\Model\Entity\Instance;
use Opennemas\Task\Component\Queue\Queue;
use Opennemas\Task\Component\Task\ServiceTask;

class UserCacheHelper extends CacheHelper
{
    /**
     * The cache service.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Initializes the CacheHelper.
     *
     * @param Instance  $instance  The current instance.
     * @param queue     $queue     The task queue service.
     * @param Container $container The service container.
     */
    public function __construct(?Instance $instance, Queue $queue, $container)
    {
        $this->cache    = $container->get('cache.connection.instance');
        $this->instance = $instance;
        $this->queue    = $queue;
    }

    /**
     * TODO: Remove when using new ORM for users
     *
     * Removes users from old redis cache.
     *
     * @param User $item The user to remove from cache.
     *
     * @return CacheHelper The current helper for method chaining.
     */
    public function deleteItem(User $item) : CacheHelper
    {
        $this->queue->push(new ServiceTask('cache', 'delete', [
            sprintf('user-%s', $item->id)
        ]));

        $isAuthor = array_search(3, array_column($item->user_groups, 'user_group_id'));

        if (false !== $isAuthor) {
            $this->cache->remove('author-' . $item->id);
        }

        return $this;
    }
}
