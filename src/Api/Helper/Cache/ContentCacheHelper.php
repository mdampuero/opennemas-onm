<?php

namespace Api\Helper\Cache;

use Opennemas\Task\Component\Task\ServiceTask;

class ContentCacheHelper extends CacheHelper
{
    /**
     * TODO: Remove when using newsstand as content type name.
     *
     * Array to map content type names to extensions.
     *
     * @var array
     */
    protected $map = [ 'kiosko' => 'newsstand' ];

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
        // TODO: Remove when using newsstand as content type name
        $extension = $this->map[$item->content_type_name] ?? $item->content_type_name;

        $this->queue->push(new ServiceTask('core.template.cache', 'delete', [
            'content', $item->pk_content
        ]));

        $this->queue->push(new ServiceTask('core.varnish', 'ban', [ sprintf(
            'obj.http.x-tags ~ %s-%s',
            $extension,
            $item->pk_content
        ) ]));

        return $this;
    }
}
