<?php

namespace Api\Helper\Cache;

use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;
use Opennemas\Cache\Core\Cache;
use Opennemas\Task\Component\Queue\Queue;
use Opennemas\Task\Component\Task\ServiceTask;
use Symfony\Component\DependencyInjection\Container;

class FrontpageCacheHelper extends CacheHelper
{
    /**
     * {@inheritdoc}
     */
    protected $keys = [
        'content_type_name-widget-{{content_type_name}}' .
        '.*category-widget-({{categories}}|all)' .
        '.*tag-widget-({{tags}}|all)' .
        '.*author-widget-({{fk_author}}|all)' .
        '.*content-listing-frontpage-{{frontpage_id}}'
    ];


    /**
     * {@inheritdoc}
     */
    public function __construct(?Instance $instance, Queue $queue, Cache $cache, Container $container)
    {
        $this->instance  = $instance;
        $this->queue     = $queue;
        $this->cache     = $cache;
        $this->container = $container;
    }

    /**
     * Removes the caches for the content listing in the frontpage.
     *
     * @param array $added    The contents added to the frontpage.
     * @param array $removed  The contents removed from the frontpage.
     * @param int   $category The category of the frontpage to be saved.
     */
    public function deleteItems($added, $removed, $category)
    {
        if (empty($added) && empty($removed)) {
            return;
        }

        // Remove the redis cache keys of the widgets.
        $this->cache->remove(array_merge($this->cache->getSetMembers('Widget_Keys'), [ 'Widget_Keys' ]));

        $added = array_map(function ($item) use ($category) {
            return $item . '.*content-listing-frontpage-' . $category;
        }, $added);

        // Remove varnish caches for the widgets with the content recently added
        $this->removeVarnishCache($added, $category);

        $repository = $this->container->get('entity_repository');

        $removed = $repository->findMulti($this->formatItems($removed));

        // Remove varnish cache for the widgets that can have the item removed from the frontpage.
        $this->keys = array_map(function ($key) use ($category) {
            return preg_replace('@{{frontpage_id}}@', $category, $key);
        }, $this->keys);

        foreach ($removed as $item) {
            $this->removeVarnishCache($this->replaceWildcards($item, $this->keys), $category);
        }
    }

    /**
     * Queues all the varnish bans based on the content.
     *
     * @param array   $keys The array of keys to delete.
     * @param Content $item The content to delete cache for.
     * @param string  $ban  The string with the ban.
     */
    protected function removeVarnishCache($keys, $category)
    {
        foreach ($keys as $key) {
            $this->queue->push(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ instance-%s.*%s', $this->instance->internal_name, $key, $category)
            ]));
        }
    }

    /**
     * Formats the array to the type expected for the entity manager.
     *
     * @param array $data An array of strings with a format content_type_name-id.
     *
     * @return array An array with the format expected for the entity manager.
     */
    protected function formatItems($data)
    {
        return array_map(function ($item) {
            return explode('-', $item);
        }, $data);
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
        return implode('|', $item->tags);
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
