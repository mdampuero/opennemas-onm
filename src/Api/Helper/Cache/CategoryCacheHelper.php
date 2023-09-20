<?php

namespace Api\Helper\Cache;

use Common\Model\Entity\Category;
use Opennemas\Task\Component\Task\ServiceTask;

class CategoryCacheHelper extends CacheHelper
{
    /**
     * Removes caches for contents related to a category.
     *
     * @param  array $ids The list of content ids.
     *
     * @return CacheHelper The current helper for method chaining.
     */
    public function deleteContents(array $ids = []) : CacheHelper
    {
        $this->queue->push(new ServiceTask('cache.connection.instance', 'remove', [
            $ids
        ]))->push(new ServiceTask('cache', 'delete', [
            $ids
        ]));

        return $this;
    }

    /**
     * Removes caches for a category.
     *
     * @param Category $category The category.
     *
     * @return CacheHelper The current helper for method chaining.
     */
    public function deleteItem(Category $category, $vote = false, $action = false) : CacheHelper
    {
        $this->queue->push(new ServiceTask('core.template.cache', 'delete', [
            [ 'category', 'list', $category->id ]
        ]))->push(new ServiceTask('core.service.assetic.dynamic_css', 'deleteTimestamp', [
            '%global%'
        ]))->push(new ServiceTask('core.service.assetic.dynamic_css', 'deleteTimestamp', [
            $category->id
        ]));

        return $this;
    }

    /**
     * Removes caches for a category.
     *
     * @return CacheHelper The current helper for method chaining.
     */
    public function removeVarnishRssCache() : CacheHelper
    {
        $keys = ['rss-index'];
        foreach ($keys as $key) {
            $this->queue->push(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ ^instance-%s.*%s', $this->instance->internal_name, $key)
            ]));
        }

        return $this;
    }
}
