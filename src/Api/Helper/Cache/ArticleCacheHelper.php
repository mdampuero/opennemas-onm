<?php

namespace Api\Helper\Cache;

use Common\Model\Entity\Content;
use Opennemas\Task\Component\Task\ServiceTask;

class ArticleCacheHelper extends ContentCacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $instance;

    /**
     * {@inheritdoc}
     */
    protected $queue;

    /**
     * {@inheritdoc}
     */
    protected $cache;

    /**
     * The array of keys to remove in varnish.
     *
     * @var array
     */
    protected $varnishKeys = [
        'article-{{pk_content}}-inner',
        'category-{{category_id}}',
        'frontpage-page',
        'sitemap',
        'rss-article,{{category_id}}',
        'rss-frontpage$',
        'rss-author-{{fk_author}}',
        'authors-frontpage',
        'content-author-{{fk_author}}',
        'tag-{{tag_id}}',
        'archive-content',
    ];

    /**
     * The array of keys to remove in redis.
     *
     * @var array
     */
    protected $redisKeys = [
        'WidgetLastInSectionWithPhoto-*-{{category_id}}',
        'WidgetInfiniteScroll-*-{{pk_content}}-*-{{category_id}}',
        'WidgetNextPrevious-*-article-*-{{category_id}}',
        'suggested_contents_{{content_type_name}}_{{category_id}}'
    ];

    /**
     * Removes caches for a content.
     *
     * @param Content $item The content to delete cache for.
     *
     * @return CacheHelper The current helper for method chaining.
     */
    public function deleteItem($item) : CacheHelper
    {
        $redisKeys   = $this->replaceWildcards($item, $this->redisKeys);
        $varnishKeys = $this->replaceWildcards($item, $this->varnishKeys);

        $this->removeRedisCache($redisKeys);
        $this->removeVarnishCache($varnishKeys);

        return $this;
    }

    /**
     * Queues all the varnish bans based on the content.
     *
     * @param array $item The content to delete cache for.
     */
    protected function removeVarnishCache($keys)
    {
        foreach ($keys as $key) {
            $this->queue->push(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', $this->instance->internal_name, $key)
            ]));
        }
    }

    /**
     * Removes the redis cache for the current object.
     *
     * @param array $item The object to ban redis cache.
     */
    protected function removeRedisCache($keys)
    {
        foreach ($keys as $key) {
            $this->cache->delete($key);
        }
    }

    /**
     * Replace the keys when the property of the item doesn't have a standard name.
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
