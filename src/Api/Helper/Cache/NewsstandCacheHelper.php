<?php

namespace Api\Helper\Cache;

use Opennemas\Task\Component\Task\ServiceTask;

class NewsstandCacheHelper extends ContentCacheHelper
{
    /**
     * Removes caches for the list of newsstands.
     *
     * @return CacheHelper The current helper for method chaining.
     */
    public function deleteList() : CacheHelper
    {
        $this->queue->push(new ServiceTask('core.template.cache', 'delete', [
            'newsstand', 'list'
        ]));

        return $this;
    }
}
