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
    private $data = array();

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        return array_keys($this->data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doFetch($id)
    {
        if (isset($this->data[$id])) {
            return $this->data[$id];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doContains($id)
    {
        return isset($this->data[$id]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doSave($id, $data, $lifeTime = 0)
    {
        $this->data[$id] = $data;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doDelete($id)
    {
        unset($this->data[$id]);

        return true;
    }
}
