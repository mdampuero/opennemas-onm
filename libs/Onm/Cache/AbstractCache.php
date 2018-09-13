<?php
/**
 * Defines the Onm\Cache\AbstractCache abstract class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Onm_Cache
 */

namespace Onm\Cache;

/**
 * Base class for cache driver implementations.
 *
 * @package Onm_Cache
 */
abstract class AbstractCache implements CacheInterface
{
    /**
     * The namespace to prefix all cache ids with
     * @var string
     */
    private $namespace = '';

    /**
     * List of most recent used items.
     *
     * @var array
     */
    public $mru = [];

    /**
     * The function call buffer.
     *
     * @var array
     */
    protected $buffer = [];

    /**
     * Set the namespace to prefix all cache ids with
     *
     * @param  string $namespace
     * @return void
     */
    public function setNamespace($namespace)
    {
        $this->namespace = (string) $namespace;
    }

    /**
     * Returns the current function call buffer.
     *
     * @return array The function call buffer.
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * Returns the current namespace
     *
     * @return string the namespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $id cache id The id of the cache entry to fetch.
     *
     * @return string The cached data or FALSE, if no cache entry
     *                exists for the given id.
     */
    public function fetch($id)
    {
        $id = $this->getNamespacedId($id);

        if (is_array($id)) {
            $values = array_intersect_key($this->mru, array_flip($id));
            $id     = array_values(array_diff($id, array_keys($values)));

            if (!empty($values)) {
                $this->buffer[] = [
                    'method' => 'fetchMulti',
                    'params' => [
                        'ids' => array_keys($values),
                        'values' => array_values($values)
                    ],
                    'mru'    => true,
                    'time'   => microtime(true)
                ];
            }

            if (!empty($id)) {
                $rawValues = $this->doFetch($id);
                $values    = array_merge($values, $rawValues);

                $this->buffer[] = [
                    'method' => 'fetchMulti',
                    'params' => [
                        'ids' => $id,
                        'values' => array_values($values)
                    ],
                    'time'   => microtime(true)
                ];
            }

            $this->mru = array_merge($this->mru, $values);

            return array_combine(
                $this->getUnNamespacedId(array_keys($values)),
                array_values($values)
            );
        }

        if (array_key_exists($id, $this->mru)) {
            $this->buffer[] = [
                'method' => 'fetch',
                'params' => [ 'ids' => $id, 'values' => $this->mru[$id] ],
                'mru'    => true,
                'time'   => microtime(true)
            ];

            return $this->mru[$id];
        }

        $this->mru[$id] = $this->doFetch($id);
        $this->buffer[] = [
            'method' => 'fetch',
            'params' => [ 'ids' => $id, 'values' => $this->mru[$id] ],
            'time'   => microtime(true)
        ];

        return $this->mru[$id];
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for
     *                 the given cache id, FALSE otherwise.
     */
    public function contains($id)
    {
        return $this->doContains($this->getNamespacedId($id));
    }

    /**
     * {@inheritdoc}
     *
     * @param string|array $id       The cache id.
     * @param string|array $data     The cache entry/data.
     * @param int    $lifeTime The lifetime. If != false, sets a specific
     *                         lifetime for this cache entry (null => infinite
     *                         lifeTime).
     * @return boolean TRUE if the entry was successfully stored in the
     *                         cache, FALSE otherwise.
     */
    public function save($id, $data = null, $lifeTime = 0)
    {
        if (is_array($id)) {
            $values = [];
            foreach ($id as $key => $value) {
                $cacheKey             = $this->getNamespacedId($key);
                $this->mru[$cacheKey] = $value;
                $values[$cacheKey]    = $value;
            }

            $this->buffer[] = [
                'method' => 'saveMulti',
                'params' => [ 'ids' => $id, 'values' => $data ],
                'time'   => microtime(true)
            ];

            return $this->doSave($values, $data, $lifeTime);
        }

        $this->mru[$this->getNamespacedId($id)] = $data;

        $this->buffer[] = [
            'method' => 'save',
            'params' => [ 'ids' => $id, 'values' => $data ],
            'time'   => microtime(true)
        ];
        return $this->doSave($this->getNamespacedId($id), $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted,
     *                 FALSE otherwise.
     */
    public function delete($id, $namespace = '')
    {
        if (empty($namespace)) {
            $id = $this->getNamespacedId($id);
        } else {
            $id = $namespace . '_' . $id;
        }

        if (strpos($id, '*') !== false) {
            return $this->deleteByRegex('/' . str_replace('*', '.*', $id) . '/');
        }

        return $this->doDelete($id);
    }

    /**
     * Deletes all cache entries.
     *
     * @return array $deleted  Array of the deleted cache ids
     */
    public function deleteAll()
    {
        $ids = $this->getIds();

        foreach ($ids as $id) {
            $this->delete($id);
        }

        return $ids;
    }

    /**
     * Deletes cache entries where the id matches a PHP regular expressions
     *
     * @param  string $regex
     * @return array  $deleted  Array of the deleted cache ids
     */
    public function deleteByRegex($regex)
    {
        $deleted = [];

        $ids = $this->getIds();

        foreach ($ids as $id) {
            if (preg_match($regex, $id)) {
                $this->delete($id);
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    /**
     * Deletes cache entries where the id has the passed prefix
     *
     * @param  string $prefix
     * @return array  $deleted  Array of the deleted cache ids
     */
    public function deleteByPrefix($prefix)
    {
        $deleted = [];

        $prefix = $this->getNamespacedId($prefix);
        $ids    = $this->getIds();

        foreach ($ids as $id) {
            if (strpos($id, $prefix) === 0) {
                $this->delete($id);
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    /**
     * Deletes cache entries where the id has the passed suffix
     *
     * @param  string $suffix
     * @return array  $deleted  Array of the deleted cache ids
     */
    public function deleteBySuffix($suffix)
    {
        $deleted = [];

        $ids = $this->getIds();

        foreach ($ids as $id) {
            if (substr($id, -1 * strlen($suffix)) === $suffix) {
                $this->delete($id);
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    /**
     * Prefix the passed id with the configured namespace value
     *
     * @param  string $id The id to namespace
     * @return string $id The namespaced id
     */
    private function getNamespacedId($id)
    {
        if (is_array($id)) {
            return array_map(function ($a) {
                return $this->namespace . '_' . $a;
            }, $id);
        }

        return $this->namespace . '_' . $id;
    }

    /**
     * Removes the namespace from the id.
     *
     * @param mixed $id A namespaced id or an array of namespaced ids.
     *
     * @return mixed The id or ids without namespace.
     */
    private function getUnNamespacedId($id)
    {
        if (is_array($id)) {
            return array_map(function ($a) {
                return str_replace($this->namespace . '_', '', $a);
            }, $id);
        }

        return str_replace($this->namespace . '_', '', $id);
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param  string $id cache id The id of the cache entry to fetch.
     * @return string The cached data or FALSE, if no cache entry
     *                exists for the given id.
     */
    abstract protected function doFetch($id);

    /**
     * Test if an entry exists in the cache.
     *
     * @param  string  $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for
     *                 the given cache id, FALSE otherwise.
     */
    abstract protected function doContains($id);

    /**
     * Puts data into the cache.
     *
     * @param string $id       The cache id.
     * @param mixed  $data     The cache entry/data.
     * @param int    $lifeTime The lifetime. If != false, sets a specific
     *                         lifetime for this cache entry (null => infinite
     *                         lifeTime).
     * @return boolean TRUE if the entry was successfully stored in the
     *                         cache, FALSE otherwise.
     */
    abstract protected function doSave($id, $data, $lifeTime = null);

    /**
     * Deletes a cache entry.
     *
     * @param  string  $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted,
     *                 FALSE otherwise.
     */
    abstract protected function doDelete($id);

    /**
     * Get an array of all the cache ids stored
     *
     * @return array $ids
     */
    abstract public function getIds();
}
