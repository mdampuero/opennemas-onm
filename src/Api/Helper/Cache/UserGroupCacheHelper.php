<?php

namespace Api\Helper\Cache;

use Opennemas\Task\Component\Task\ServiceTask;

class UserGroupCacheHelper extends CacheHelper
{
    /**
     * Deletes all users from cache.
     */
    public function deleteUsers()
    {
        $this->queue->push(new ServiceTask(
            'cache.connection.instance',
            'removeByPattern',
            [ 'user-*' ]
        ));
    }
}
