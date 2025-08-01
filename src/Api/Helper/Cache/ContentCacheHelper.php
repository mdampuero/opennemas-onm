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
     * The array of default keys in voted items to remove in varnish.
     *
     * @var array
     */
    protected $defaultVoteVarnishKeys = [
        '{{content_type_name}}-frontpage$',
        '{{content_type_name}}-frontpage',
        '{{content_type_name}}-{{pk_content}}',
        'content_type_name-widget-{{content_type_name}}',
        'header-date',
    ];

    /**
     * Array of varnish keys who needs to be module checked before added to queue
     */
    protected $varnishModuleKeys = [];

    /**
     * Array of Varnish cache keys that are changed based on categories.
     */
    protected $varnishKeysChangedCategory = [
        'category-{{categories}}',
        '{{content_type_name}}-frontpage,category-{{content_type_name}}-{{categories}}'
    ];

    /**
     * Array of Varnish cache keys that are changed based on author.
     */
    protected $varnishKeysChangedAuthor = [
        'content-author-{{fk_author}}-frontpage'
    ];

    /**
     * Array of Varnish cache keys that are changed based on tags.
     */
    protected $varnishKeysChangedTags = [
        'tag-{{tags}}'
    ];

    /**
     * An array is defined for the keys of the old data.
     */
    protected $varnishKeysOldData = [];

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
    public function deleteItem($item, $params = []) : CacheHelper
    {
        $keys = array_merge(
            $this->cache->getSetMembers('Widget_Keys'),
            [ 'Widget_Keys' ],
            $this->replaceWildcards($item, $this->redisKeys)
        );

        $this->cache->remove($keys);

        $varnishKeys = array_key_exists('vote', $params) && $params['vote'] ?
            $this->defaultVoteVarnishKeys :
            $this->varnishKeys;

        $varnishKeys = array_merge($varnishKeys, $this->getModuleKeys());

        $this->removeVarnishCache(
            $this->replaceWildcards($item, $varnishKeys),
            $item,
            array_key_exists('action', $params) ? $params['action'] : null
        );

        // Check if 'itemOldData' exists in params and if old data needs to be cleared.
        if (($params["itemOldData"] ?? false) && $this->needClearOldData($item, $params["itemOldData"])) {
            // Remove Varnish cache for the old data based on the replaced wildcards.
            $this->removeVarnishCache(
                $this->replaceWildcards($params["itemOldData"], $this->varnishKeysOldData),
                $params["itemOldData"],
                array_key_exists('action', $params) ? $params['action'] : null
            );
        }

        return $this;
    }

    /**
     * Determines if old data needs to be cleared based on changes in the current item.
     *
     * @param object $item The current item being processed.
     * @param object $itemOldData The previous version of the item for comparison.
     * @return int Returns the count of Varnish cache keys that need to be cleared.
     */
    public function needClearOldData($item, $itemOldData)
    {
        // Initialize an array to hold the Varnish cache keys for old data.
        $this->varnishKeysOldData = [];

        // Check if categories have changed between the current and old item.
        if ($item->categories != $itemOldData->categories) {
            $this->varnishKeysOldData = array_merge($this->varnishKeysOldData, $this->varnishKeysChangedCategory);
        }

        // Check if the author has changed between the current and old item.
        if ($item->fk_author != $itemOldData->fk_author) {
            $this->varnishKeysOldData = array_merge($this->varnishKeysOldData, $this->varnishKeysChangedAuthor);
        }

        // Check if the tags have changed between the current and old item.
        if (count(array_diff($item->tags, $itemOldData->tags)) || count(array_diff($itemOldData->tags, $item->tags))) {
            $this->varnishKeysOldData = array_merge($this->varnishKeysOldData, $this->varnishKeysChangedTags);
        }

        return (count($this->varnishKeysOldData));
    }

    /**
     * Returns varnish keys if respective module is activated
     *
     * @return Array Array of varnish keys.
     */
    public function getModuleKeys()
    {
        $finalModuleKeys = [];

        if (empty($this->varnishModuleKeys)) {
            return $finalModuleKeys;
        }

        $security = getService('core.security');

        foreach ($this->varnishModuleKeys as $moduleName => $moduleKeys) {
            if ($security->hasExtension($moduleName)) {
                $finalModuleKeys = array_merge($finalModuleKeys, $moduleKeys);
            }
        }

        return $finalModuleKeys;
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
        if ($content->fk_author) {
            $xtags[] = sprintf('author-%d', $content->fk_author);
        }

        return implode(',', $xtags);
    }

    /**
     * Queues all the varnish bans based on the content.
     *
     * @param array   $keys The array of keys to delete.
     * @param Content $item The content to delete cache for.
     */
    protected function removeVarnishCache($keys, $item, $action = null)
    {
        if (!empty($item->path)
            && $item->content_type_name != 'video'
            && $action != 'Api\Service\V1\PhotoService::createItem'
        ) {
            $this->queue->push(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-url ~ %s', $item->path)
            ]));
        }

        $banRegExpr = '';
        foreach ($keys as $key) {
            $banRegExpr .= '|(' . $key . ')';
        }

        if (!empty($banRegExpr)) {
            $this->queue->push(new ServiceTask('core.varnish', 'ban', [
                sprintf(
                    'obj.http.x-tags ~ ^instance-%s.*%s',
                    $this->instance->internal_name,
                    '(' . substr($banRegExpr, 1) . ')'
                )
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

                // Don't add the key if the content doesn't have the property and is a single replacement
                if (empty($item->{$match}) && count($matches[1]) === 1) {
                    $key = null;
                    continue;
                }

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

        return array_filter($keys);
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
        return sprintf('(%s)', implode('|', $item->tags));
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
