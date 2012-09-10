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
 * Array cache driver.
 *
 * @since 0.8
 * @author  Fran Dieguez <fran@openhost.es>
 */
class ArrayCache extends AbstractCache
{
    /**
     * @var array $data
     */
    private $_data = array();

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        return array_keys($this->_data);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        if (isset($this->_data[$id])) {
            return $this->_data[$id];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return isset($this->_data[$id]);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $this->_data[$id] = $data;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        unset($this->_data[$id]);

        return true;
    }
}

