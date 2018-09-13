<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Cache\Core;

use Framework\Component\Data\DataBuffer;

abstract class Cache extends DataBuffer
{
    /**
     * The already used data.
     *
     * @var array
     */
    public $mru = [];

    /**
     * Checks if there is data in cache for the given id.
     *
     * @param string $id The cache id.
     *
     * @return boolean True if there is an entry in the cache for the id.
     *                 Otherwise, returns false.
     */
    public function exists($id)
    {
        $this->addToBuffer('exists', [ 'ids' => $id ]);

        $cacheId = $this->getNamespacedId($id);

        if (array_key_exists($id, $this->mru)) {
            return true;
        }

        return $this->contains($cacheId);
    }

    /**
     * Removes the data from cache for the given id.
     *
     * @param mixed $id The cache id (or array of cache ids).
     */
    public function remove($id)
    {
        $this->addToBuffer('remove', [ 'ids' => $id ]);

        $cacheId = $this->getNamespacedId($id);

        if (is_array($id)) {
            $this->mru = array_diff_key($this->mru, array_flip($id));

            $this->deleteMulti($cacheId);
            return;
        }

        unset($this->mru[$id]);

        $this->delete($cacheId);
    }

    /**
     * Removes all entries that match a pattern.
     *
     * @param strign $pattern The pattern to match.
     */
    public function removeByPattern($pattern)
    {
        $this->addToBuffer('removeByPattern', [ 'pattern' => $pattern ]);

        $this->deleteByPattern($pattern);
    }

    /**
     * Gets the data from cache for the given id.
     *
     * @param mixed $id The cache id (or array of cache ids).
     *
     * @return mixed The data in cache.
     */
    public function get($id)
    {
        if (is_array($id)) {
            // Get values from MRU data
            $values = array_intersect_key($this->mru, array_flip($id));

            if (!empty($values)) {
                $this->addToBuffer('get', [
                    'ids'    => array_keys($values),
                    'values' => array_values($values),
                    'mru'    => true
                ]);
            }

            // Missed ids in MRU data
            $id = array_values(array_diff($id, array_keys($values)));

            if (!empty($id)) {
                $cacheId = $this->getNamespacedId($id);
                $values  = array_merge($values, $this->fetchMulti($cacheId));

                // Save values in MRU data
                if (!empty($values)) {
                    $this->addToBuffer('get', [
                        'ids'    => array_keys($values),
                        'values' => array_values($values)
                    ]);

                    $this->mru = array_merge($this->mru, $values);
                }
            }

            return $values;
        }

        if (array_key_exists($id, $this->mru)) {
            $this->addToBuffer('get', [
                'ids'    => [ $id ],
                'values' => [ $this->mru[$id] ],
                'mru'    => true
            ]);

            return $this->mru[$id];
        }

        $cacheId = $this->getNamespacedId($id);
        $value   = $this->fetch($cacheId);

        if (!empty($value)) {
            $this->addToBuffer('get', [
                'ids'    => [ $id ],
                'values' => [ $value ]
            ]);

            $this->mru[$id] = $value;
        }

        return $value;
    }

    /**
     * Returns the current cache namespace.
     *
     * @return string The cache namespace.
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Sets data into the cache.
     *
     * @param string   $id   The cache id.
     * @param mixed    $data The data.
     * @param intetger $ttl  The time to live (in seconds).
     */
    public function set($id, $data = null, $ttl = null)
    {
        $this->addToBuffer('set', [ 'id' => $id, 'data' => $data ]);

        if (is_array($id)) {
            $ids  = $this->getNamespacedId(array_keys($id));
            $data = array_combine($ids, array_values($id));

            $this->saveMulti($data);
            return;
        }

        $id = $this->getNamespacedId($id);

        $this->save($id, $data, $ttl);
    }

    /**
     * Changes the cache namespace.
     *
     * @return string The cache namespace.
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Adds the namespace as prefix to the id.
     *
     * @param  mixed $id The cache id.
     *
     * @return string
     */
    protected function getNamespacedId($id)
    {
        if (is_array($id)) {
            foreach ($id as &$i) {
                $i = $this->getNamespacedId($i);
            }

            return $id;
        }

        $namespace = $this->getPrefix() . $this->namespace . '_';

        return $namespace . str_replace($namespace, '', $id);
    }

    /**
     * Returns the cache prefix.
     *
     * @return string The cache prefix.
     */
    protected function getPrefix()
    {
        if (!empty($this->prefix)) {
            return $this->prefix . '_';
        }

        return '';
    }

    /**
     * Removes the namespace from the namespaced id.
     *
     * @param mixed $id The namespaced cache id.
     */
    protected function getUnNamespacedId($id)
    {
        if (is_array($id)) {
            foreach ($id as &$i) {
                $i = $this->getUnNamespacedId($i);
            }

            return $id;
        }

        $namespace = $this->getPrefix() . $this->namespace . '_';

        return preg_replace('/^' . $namespace . '/', '', $id);
    }

    /**
     * Checks if there is data in cache for the given id.
     *
     * @param string $id The cache id.
     *
     * @return mixed The data from cache.
     */
    abstract protected function contains($id);

    /**
     * Deletes data from cache given an id.
     *
     * @param mixed $id A cache id.
     */
    abstract protected function delete($id);

    /**
     * Deletes data from cache given a pattern.
     *
     * @param string $pattern The cache id pattern.
     */
    abstract protected function deleteByPattern($id);

    /**
     * Deletes data from cache given an array of ids.
     *
     * @param mixed $id An array of cache ids.
     */
    abstract protected function deleteMulti($ids);

    /**
     * Returns data from cache given an id.
     *
     * @param string $id The cache id.
     *
     * @return mixed The data from cache.
     */
    abstract protected function fetch($id);

    /**
     * Returns data from cache given an array of ids.
     *
     * @param string $id The cache id.
     *
     * @return array The data from cache.
     */
    abstract protected function fetchMulti($id);

    /**
     * Saves data into the cache.
     *
     * @param string   $id   The cache id.
     * @param mixed    $data The data.
     * @param intetger $ttl  The time to live (in seconds).
     */
    abstract protected function save($id, $data, $ttl);

    /**
     * Saves data into the cache given an array with keys and data.
     *
     * @param mixed    $data The array with keys and data.
     */
    abstract protected function saveMulti($data);
}
