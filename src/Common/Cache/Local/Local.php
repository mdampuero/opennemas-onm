<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Cache\Local;

use Common\Cache\Core\Cache;

class Local extends Cache
{
    /**
     * The array of data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * {@inheritdoc}
     */
    protected function fetch($id)
    {
        if (!array_key_exists($id, $this->data)) {
            return null;
        }

        return $this->data[$id];
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchMulti($ids)
    {
        $values = array_intersect_key($this->data, array_flip($ids));
        $ids    = $this->getUnNamespacedId(array_keys($values));

        return array_combine($ids, $values);
    }

    /**
     * {@inheritdoc}
     */
    protected function remove($id)
    {
        unset($this->data[$id]);
    }

    /**
     * {@inheritdoc}
     */
    protected function removeMulti($ids)
    {
        foreach ($ids as $id) {
            $this->remove($id);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function save($id, $data, $ttl)
    {
        $this->data[$id] = $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function saveMulti($data)
    {
        $this->data = array_merge($this->data, $data);
    }
}
