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
     * The array of custom keys to remove in varnish.
     *
     * @var array
     */
    protected $varnishKeys = [];

    /**
     * The array of custom keys to remove in redis.
     *
     * @var array
     */
    protected $redisKeys = [];

    /**
     * The array of default keys to remove in varnish.
     *
     * @var array
     */
    protected $defaultVarnishKeys = [
        'archive-page-{{starttime}}',
        'authors-frontpage',
        'category-{{categories}}',
        'content-author-{{fk_author}}-frontpage',
        '{{content_type_name}}-frontpage$',
        '{{content_type_name}}-frontpage,category-{{content_type_name}}-{{categories}}',
        '{{content_type_name}}-{{pk_content}}',
        'content_type_name-widget-{{content_type_name}}' .
        '.*category-widget-(({{categories}})|(all))' .
        '.*tag-widget-(({{tags}})|(all))' .
        '.*author-widget-(({{fk_author}})|(all))',
        'last-suggested-{{categories}}',
        'rss-author-{{fk_author}}',
        'rss-{{content_type_name}}$',
        'rss-google-news-showcase',
        'sitemap',
        'tag-{{tags}}',
        'header-date',
    ];

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
        $keys = array_merge(
            $this->cache->getSetMembers('Widget_Keys'),
            [ 'Widget_Keys' ],
            $this->replaceWildcards($item, $this->redisKeys)
        );

        $this->cache->remove($keys);

        $this->removeVarnishCache(
            $this->replaceWildcards($item, array_merge($this->varnishKeys, $this->defaultVarnishKeys)),
            $item
        );

        return $this;
    }

    /**
     * Returns the cache ids for the specific content type.
     *
     * @param Content $item   The content to get the x-tags for.
     * @param array   $params An array of parameters.
     *
     * @return String The x-tags for the specific content type.
     */
    public function getXTags(Content $content)
    {
        $xtags = [];

        if (!empty($content->tags)) {
            $xtags = array_map(function ($tag) {
                return sprintf('tag-%d', $tag);
            }, $content->tags);
        }

        $xtags[] = sprintf('%s-%d', $content->content_type_name, $content->pk_content);

        return implode(',', $xtags);
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
                $pattern = sprintf('@{{%s}}@', $match);

                if (empty($item->{$match})) {
                    $key = preg_replace($pattern, '0', $key);
                    continue;
                }

                $method = sprintf('replace%s', ucfirst($match));

                $replacement = method_exists($this, $method)
                    ? $this->{$method}($item)
                    : $item->{$match};

                $key = preg_replace($pattern, $replacement, $key);
            }
        }

        return $keys;
    }

    /**
     * Custom replace function for starttime.
     *
     * @param Content $item The item to get the starttime from.
     *
     * @return String The key with the starttime replaced in it.
     */
    protected function replaceStarttime(Content $item)
    {
        return $item->starttime->format('Y-m-d');
    }

    /**
     * Custom replace function for tags.
     *
     * @param Content $item The item to get the tags from.
     *
     * @return String The key with the tags replaced in it.
     */
    protected function replaceTags(Content $item)
    {
        return implode('|', array_map(function ($tag) {
            return sprintf('(' . $tag . ')');
        }, $item->tags));
    }

    /**
     * Custom replace function for categories.
     *
     * @param Content $item The item to get the tags from.
     *
     * @return String The key with the tags replaced in it.
     */
    protected function replaceCategories(Content $item)
    {
        return $item->categories[0];
    }
}
