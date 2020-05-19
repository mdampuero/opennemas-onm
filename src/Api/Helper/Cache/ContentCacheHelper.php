<?php

namespace Api\Helper\Cache;

use Opennemas\Task\Component\Task\ServiceTask;

class ContentCacheHelper extends CacheHelper
{
    /**
     * Removes caches for an item which refers to a file. This is valid for
     * attachments, newsstands and photos.
     *
     * @param Content $item The content to delete cache for.
     *
     * @return CacheHelper The current helper for method chaining.
     */
    public function deleteFile($item) : CacheHelper
    {
        $this->queue->push(new ServiceTask('core.varnish', 'ban', [
            sprintf('req.url ~ %s', $item->path)
        ]));

        return $this;
    }

    /**
     * Removes caches for a content.
     *
     * @param Content $item The content to delete cache for.
     *
     * @return CacheHelper The current helper for method chaining.
     */
    public function deleteItem($item) : CacheHelper
    {
        $this->queue->push(new ServiceTask('core.varnish', 'ban', [ sprintf(
            'obj.http.x-tags ~ %s-%s',
            $item->content_type_name,
            $item->pk_content
        ) ]));

        return $this;
    }
}
