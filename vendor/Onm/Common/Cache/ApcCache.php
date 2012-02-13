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
 * APC cache driver.
 *
 * @since 0.8
 * @author  Fran Dieguez <fran@openhost.es>
 */
class APCCache extends AbstractCache
{

   /*
    * Initilizes the APCCache
    *
    * @param $options
    */
    public function __construct($options= array())
    {
        $this->initialize($options);
    }

    /**
    * Initializes this APCCache instance.
    */
    public function initialize($options = array())
    {
        if (!function_exists('apc_store') || !ini_get('apc.enabled')) {
            throw new \Exception('You must have APC installed and enabled to use APCCache class.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        $ci = apc_cache_info('user');
        $keys = array();

        foreach ($ci['cache_list'] as $entry) {
            $keys[] = $entry['info'];
        }

        return $keys;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doFetch($id)
    {
        return apc_fetch($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doContains($id)
    {
        $found = false;

        apc_fetch($id, $found);

        return $found;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doSave($id, $data, $lifeTime = 0)
    {
        return (bool) apc_store($id, $data, (int) $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doDelete($id)
    {
        return apc_delete($id);
    }
}
