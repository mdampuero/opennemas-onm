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
        parent::__construct($instance, $queue);

        $this->cache = $container->get('cache.connection.instance');
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

        if (array_search(3, array_column($item->user_groups, 'user_group_id')) !== false) {
            $this->cache->remove(sprintf('author-%s', $item->id));
        }

        return $this;
    }

    /**
     * Removes caches for a user.
     *
     * @param User $user The user.
     */
    public function deleteItemVarnish(User $user) : void
    {
        $varnishKeys = [
            'content-author-' . $user->id . '-frontpage',
            'opinion-author-' . $user->id . '-frontpage',
            'content-author-' . $user->id . '-frontpage',
            'author-' . $user->id,
            'author-widget-' . $user->id,
            'rss-author-' . $user->id,
            'authors-frontpage',
            'sitemap',
        ];

        $this->queue->push(new ServiceTask('core.template.cache', 'delete', [
            [ 'user', 'show', $user->id ]
        ]));

        $banRegExpr = '';
        foreach ($varnishKeys as $key) {
            $banRegExpr .= '|(' . $key . ')';
        }

        if (!empty($banRegExpr)) {
            $this->queue->push(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ ^instance-%s.*%s',
                    $this->instance->internal_name,
                    '(' . substr($banRegExpr, 1) . ')'
                )
            ]));
        }
    }
}
