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
    public function deleteItem(Category $category) : CacheHelper
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
}
