<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onm\Common\Cache;

/**
 * Base class for cache driver implementations.
 *
 * @since 0.8
 * @author  Fran Dieguez <fran@openhost.es>
 */
abstract class AbstractCache implements Cache
{
    /** @var string The namespace to prefix all cache ids with */
    private $_namespace = '';

    /**
     * Set the namespace to prefix all cache ids with.
     *
     * @param string $namespace
     * @return void
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = (string) $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        return $this->_doFetch($this->_getNamespacedId($id));
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return $this->_doContains($this->_getNamespacedId($id));
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return $this->_doSave($this->_getNamespacedId($id), $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $id = $this->_getNamespacedId($id);

        if (strpos($id, '*') !== false) {
            return $this->deleteByRegex('/' . str_replace('*', '.*', $id) . '/');
        }

        return $this->_doDelete($id);
    }

    /**
     * Delete all cache entries.
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
     * Delete cache entries where the id matches a PHP regular expressions
     *
     * @param string $regex
     * @return array $deleted  Array of the deleted cache ids
     */
    public function deleteByRegex($regex)
    {
        $deleted = array();

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
     * Delete cache entries where the id has the passed prefix
     *
     * @param string $prefix
     * @return array $deleted  Array of the deleted cache ids
     */
    public function deleteByPrefix($prefix)
    {
        $deleted = array();

        $prefix = $this->_getNamespacedId($prefix);
        $ids = $this->getIds();

        foreach ($ids as $id) {
            if (strpos($id, $prefix) === 0) {
                $this->delete($id);
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    /**
     * Delete cache entries where the id has the passed suffix
     *
     * @param string $suffix
     * @return array $deleted  Array of the deleted cache ids
     */
    public function deleteBySuffix($suffix)
    {
        $deleted = array();

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
     * @param string $id  The id to namespace
     * @return string $id The namespaced id
     */
    private function _getNamespacedId($id)
    {
        return $this->_namespace . $id;
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id cache id The id of the cache entry to fetch.
     * @return string The cached data or FALSE, if no cache entry exists for the given id.
     */
    abstract protected function _doFetch($id);

    /**
     * Test if an entry exists in the cache.
     *
     * @param string $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    abstract protected function _doContains($id);

    /**
     * Puts data into the cache.
     *
     * @param string $id The cache id.
     * @param string $data The cache entry/data.
     * @param int $lifeTime The lifetime. If != false, sets a specific lifetime for this cache entry (null => infinite lifeTime).
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    abstract protected function _doSave($id, $data, $lifeTime = false);

    /**
     * Deletes a cache entry.
     *
     * @param string $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    abstract protected function _doDelete($id);

    /**
     * Get an array of all the cache ids stored
     *
     * @return array $ids
     */
    abstract public function getIds();
}
