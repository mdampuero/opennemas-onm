<?php

namespace Api\Helper\Cache;

use Common\Model\Entity\Tag;
use Opennemas\Task\Component\Task\ServiceTask;

class TagCacheHelper extends CacheHelper
{
    /**
     * Removes caches for a tag.
     *
     * @param Tag $tag The tag.
     */
    public function deleteItem(Tag $tag, $vote = false, $action = false) : void
    {
        $this->queue->push(new ServiceTask('core.template.cache', 'delete', [
            [ 'tag', 'show', $tag->id ]
        ]));

        $this->queue->push(new ServiceTask('core.varnish', 'ban', [
            sprintf(
                'obj.http.x-tags ~ ^instance-%s,.*,tag,show,tag-%s',
                $this->instance->internal_name,
                $tag->id
            )
        ]));

        $this->queue->push(new ServiceTask('core.varnish', 'ban', [
            sprintf(
                'obj.http.x-tags ~ ^instance-%s.*tag-%d.*',
                $this->instance->internal_name,
                $tag->id
            )
        ]));
    }

    /**
     * Removes caches for the tag list.
     */
    public function deleteList() : void
    {
        $this->queue->push(new ServiceTask('core.template.cache', 'delete', [
            [ 'tag', 'list' ]
        ]));

        $this->queue->push(new ServiceTask('core.varnish', 'ban', [
            sprintf(
                'obj.http.x-tags ~ ^instance-%s,.*,tag,list',
                $this->instance->internal_name
            )
        ]));
    }
}
