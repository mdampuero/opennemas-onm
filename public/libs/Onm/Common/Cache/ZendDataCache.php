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
 * Zend Data Cache cache driver.
 *
 * @since 0.8
 * @author  Fran Dieguez <fran@openhost.es>
 */
class ZendDataCache extends AbstractCache
{
    public function __construct()
    {
        $this->setNamespace('onm::'); // zend data cache format for namespaces ends in ::
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        throw new \BadMethodCallException("getIds() is not supported by ZendDataCache");
    }

    /**
     * {@inheritdoc}
     */
    protected function _doFetch($id)
    {
        return zend_shm_cache_fetch($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doContains($id)
    {
        return (zend_shm_cache_fetch($id) !== FALSE);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doSave($id, $data, $lifeTime = 0)
    {
        return zend_shm_cache_store($id, $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doDelete($id)
    {
        return zend_shm_cache_delete($id);
    }
}
