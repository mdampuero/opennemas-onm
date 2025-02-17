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
     * Removes a user from the old Redis cache.
     *
     * This method removes the specified user from the cache and, if the user
     * belongs to a specific user group (ID 3), it also clears related template
     * and Varnish cache entries.
     *
     * @param User $item The user to remove from cache.
     *
     * @return void
     */
    public function deleteItem(User $item) : void
    {
        $this->queue->push(new ServiceTask('cache', 'delete', [
            sprintf('user-%s', $item->id)
        ]));

        if (array_search(3, array_column($item->user_groups, 'user_group_id')) !== false) {
            $this->queue->push(new ServiceTask('core.template.cache', 'delete', [
                [ 'author', 'show', $item->id ]
            ]));

            $this->queue->push(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ ^instance-%s,.*,author,show,author-%s',
                    $this->instance->internal_name,
                    $item->id
                )
            ]));
        }
    }

    /**
     * Removes the author list cache.
     *
     * This method clears the cache related to the author list and
     * also invalidates the Varnish cache for the list of authors.
     *
     * @return void
     */
    public function deleteList() : void
    {
        $this->queue->push(new ServiceTask('cache', 'delete', [
            [ 'author', 'list' ]
        ]));

        $this->queue->push(new ServiceTask('core.varnish', 'ban', [
            sprintf(
                'obj.http.x-tags ~ ^instance-%s,.*,author,list',
                $this->instance->internal_name
            )
        ]));
    }

    /**
     * Removes caches for a user.
     *
     * @param User $user The user.
     */
    public function deleteItemVarnish(User $user) : void
    {
        if (empty($user) || $user->type === 1) {
            return;
        }

        $varnishKeys = [
            'author-' . $user->id . '(,|$)',
            'author-widget-' . $user->id . '(,|$)',
            'content-author-' . $user->id . '-frontpage',
            'opinion-author-' . $user->id . '-frontpage',
            'author-widget-all',
            'sitemap,authors',
            'archive',
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
