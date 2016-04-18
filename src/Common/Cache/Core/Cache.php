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
     * The cache namespace
     *
     * @var string
     */
    protected $namespace = 'default';

    /**
     * Deletes the data from cache for the given id.
     *
     * @param mixed $id The cache id (or array of cache ids).
     */
    public function delete($id)
    {
        $this->addToBuffer('delete', $id);

        $id = $this->getNamespacedId($id);

        if (is_array($id)) {
            $this->removeMulti($id);
            return;
        }

        $this->remove($id);
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
        $this->addToBuffer('get', $id);

        $id = $this->getNamespacedId($id);

        if (is_array($id)) {
            return $this->fetchMulti($id);
        }

        return $this->fetch($id);
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
     */
    protected function getNamespacedId($id)
    {
        if (is_array($id)) {
            foreach ($id as &$i) {
                $i = $this->getNamespacedId($i);
            }

            return $id;
        }

        return $this->namespace . '_'. $id;
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

        return preg_replace('/^' . $this->namespace . '_/', '', $id);
    }

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
     * Removes data from cache given an id.
     *
     * @param mixed $id A cache id.
     */
    abstract protected function remove($id);

    /**
     * Removes data from cache given an array of ids.
     *
     * @param mixed $id An array of cache ids.
     */
    abstract protected function removeMulti($ids);

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
