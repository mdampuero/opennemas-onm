<?php

namespace Api\Helper\Cache;

use Common\Model\Entity\User;
use Opennemas\Task\Component\Task\ServiceTask;

class UserCacheHelper extends CacheHelper
{
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

        return $this;
    }
}
