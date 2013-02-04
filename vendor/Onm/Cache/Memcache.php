<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Cache;

use \Memcache as BaseMemcache;

/**
 * Memcache cache driver.
 *
 * @since 0.8
 * @author  Fran Dieguez <fran@openhost.es>
 */
class Memcache extends AbstractCache
{
    /**
     * @var Memcache
     */
    private $_memcache;

    /**
     * Initializes the database layer
     *
     * @return void
     **/
    public function __construct($options)
    {
        if (
            array_key_exists('server', $options)
            && array_key_exists('port', $options)
        ) {
            $memcache = new \Memcache();
            $memcache->connect($options['server'], $options['port']);
            $this->setMemcache($memcache);
        }

        return $this;
    }

    /**
     * Sets the memcache instance to use.
     *
     * @param Memcache $memcache
     */
    public function setMemcache(BaseMemcache $memcache)
    {
        $this->_memcache = $memcache;
    }

    /**
     * Gets the memcache instance used by the cache.
     *
     * @return Memcache
     */
    public function getMemcache()
    {
        return $this->_memcache;
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        $keys = array();
        $allSlabs = $this->_memcache->getExtendedStats('slabs');

        foreach ($allSlabs as $server => $slabs) {
            if (is_array($slabs)) {
                foreach (array_keys($slabs) as $slabId) {
                    $dump = @$this->_memcache->getExtendedStats(
                        'cachedump',
                        (int) $slabId
                    );

                    if ($dump) {
                        foreach ($dump as $entries) {
                            if ($entries) {
                                $keys = array_merge(
                                    $keys,
                                    array_keys($entries)
                                );
                            }
                        }
                    }
                }
            }
        }

        return $keys;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return $this->_memcache->get($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return (bool) $this->_memcache->get($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        return $this->_memcache->set($id, $data, 0, (int) $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return $this->_memcache->delete($id);
    }
}
