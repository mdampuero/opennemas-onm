<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Cache;

/**
 * Redis cache driver.
 *
 * @since 0.8
 * @author  Fran Dieguez <fran@openhost.es>
 */
class Redis extends AbstractCache
{
    /**
     * @var Redis
     */
    private $redis;

    /**
     * Initializes the backend layer connection
     *
     * @return void
     **/
    public function __construct($options)
    {
        // Check if Predis library is installed
        if (!class_exists('\Predis\Client')) {
            throw new \Exception('Predis library not installed');
        }

        if (
            // is_string($options)
            true
        ) {
            // $redis = new \Predis\Client($options);
            $redis = new \Predis\Client();

            $this->setRedis($redis);
        }

        return $this;
    }

    /**
     * Sets the memcache instance to use.
     *
     * @param Memcache $memcache
     */
    public function setRedis(\Predis\Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Gets the memcache instance used by the cache.
     *
     * @return Memcache
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {


        return $keys;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        $data = $this->redis->get($id);

        $dataUnserialized = @unserialize($data);
        if ($data !== false || $str === 'b:0;') {
            return $data;
        } else {
            return $dataUnserialized;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return (bool) $this->redis->exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = -1)
    {
        if (!is_string($data)) {
            $data = serialize($data);
        }

        $saved = $this->redis->set($id, $data);

        // Set the expire time for this key if valid lifeTime
        if ($lifeTime > -1) {
            $this->redis->expire($id, $lifeTime);
        }
        return $saved;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return $this->redis->delete($id);
    }
}

