<?php

namespace Api\Helper\Cache;

use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;
use Opennemas\Cache\Core\Cache;
use Opennemas\Task\Component\Queue\Queue;
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
     * The array of keys to remove in varnish.
     *
     * @var array
     */
    protected $varnishKeys = [];

    /**
     * The array of keys to remove in redis.
     *
     * @var array
     */
    protected $redisKeys = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(?Instance $instance, Queue $queue, Cache $cache)
    {
        $this->instance = $instance;
        $this->queue    = $queue;
        $this->cache    = $cache;
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
        $this->removeRedisCache($this->replaceWildcards($item, $this->redisKeys));
        $this->removeVarnishCache($this->replaceWildcards($item, $this->varnishKeys), $item);

        return $this;
    }

    /**
     * Returns the cache ids for the specific content type.
     *
     * @param Content $item The content to get the x-tags for.
     *
     * @return String The x-tags for the specific content type.
     */
    public function getXTags(Content $content)
    {
        if ($content->content_type_name === 'article') {
            return sprintf(
                'article-%d-inner,category-%d',
                $content->pk_content,
                $content->categories[0]
            );
        }

        return sprintf('%s-%d-inner', $content->content_type_name, $content->pk_content);
    }

    /**
     * Removes the redis cache for the current object.
     *
     * @param array $item The object to ban redis cache.
     */
    protected function removeRedisCache($keys)
    {
        foreach ($keys as $pattern) {
            $this->cache->removeByPattern($pattern);
        }
    }

    /**
     * Queues all the varnish bans based on the content.
     *
     * @param array   $keys The array of keys to delete.
     * @param Content $item The content to delete cache for.
     */
    protected function removeVarnishCache($keys, $item)
    {
        if (!empty($item->path)) {
            $this->queue->push(new ServiceTask('core.varnish', 'ban', [
                sprintf('req.url ~ %s', $item->path)
            ]));
        }

        foreach ($keys as $key) {
            $this->queue->push(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', $this->instance->internal_name, $key)
            ]));
        }
    }

    /**
     * Replace the keys with the property of the item.
     *
     * @param Content $item The item to get the properties from.
     * @param array   $keys The array of keys to loop over.
     *
     * @return String The key of the varnish cache.
     */
    protected function replaceWildcards($item, $keys)
    {
        foreach ($keys as &$key) {
            preg_match_all('@{{([A-Za-z0-9_-]+)}}@', $key, $matches);

            foreach ($matches[1] as $match) {
                if (in_array($match, [ 'starttime', 'created', 'endtime' ])) {
                    $key = !empty($item->{$match}) ?
                        preg_replace(sprintf('@{{%s}}@', $match), $item->{$match}->format('Y-m-d'), $key) :
                        null;
                    continue;
                }

                if ($match === 'category_id') {
                    $key = preg_replace(sprintf('@{{%s}}@', $match), $item->categories[0], $key);
                    continue;
                }

                if ($match === 'tag_id') {
                    $key = implode('|', array_map(function ($tag) use ($match, $key) {
                        return '(' . preg_replace(sprintf('@{{%s}}@', $match), $tag, $key) . ')';
                    }, $item->tags));
                    continue;
                }

                $key = !empty($item->{$match}) ?
                    preg_replace(sprintf('@{{%s}}@', $match), $item->{$match}, $key) :
                    null;
            }
        }

        return array_filter($keys);
    }
}
