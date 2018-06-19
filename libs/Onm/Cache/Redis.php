<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Cache;

use Redis as RedisBase;

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
     */
    public function __construct($options)
    {
        if (array_key_exists('server', $options)
            && array_key_exists('port', $options)
        ) {
            $redis = new RedisBase();
            $redis->pconnect($options['server'], $options['port']);
            if (array_key_exists('auth', $options)) {
                $redis->auth($options['auth']);
            }
            if (array_key_exists('database', $options)) {
                $redis->select($options['database']);
            }
            $this->setRedis($redis);
        }

        return $this;
    }

    /**
     * Sets the memcache instance to use.
     *
     * @param Redis $redis
     */
    public function setRedis(RedisBase $redis)
    {
        // $redis->setOption(Redis::OPT_SERIALIZER, $this->getSerializerValue());
        $this->redis = $redis;
    }

    /**
     * Gets the memcache instance used by the cache.
     *
     * @return Redis
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
        // TODO: implement
        $keys = [];

        return $keys;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        if (is_array($id)) {
            $data = $this->getRedis()->mGet($id);

            $newData = [];

            for ($i = 0; $i < count($id); $i++) {
                if ($data[$i] === false) {
                    continue;
                }
                $dataUnserialized = @unserialize($data[$i]);
                if ($dataUnserialized !== false || $data[$i] === 'b:0;') {
                    $newData[$id[$i]] = $dataUnserialized;
                } else {
                    $newData[$id[$i]] = $data[$i];
                }
            }

            return $newData;
        } else {
            $data             = $this->getRedis()->get($id);
            $dataUnserialized = @unserialize($data);

            if ($dataUnserialized !== false || $data === 'b:0;') {
                return $dataUnserialized;
            } else {
                return $data;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return (bool) $this->getRedis()->exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data = null, $lifeTime = 0)
    {
        if (is_array($id)) {
            $saved = $this->getRedis()->mSet($id);

            // Set the expire time for this key if valid lifeTime
            if ($lifeTime > 0) {
                foreach (array_keys($id) as $key) {
                    $this->redis->expire($key, $lifeTime);
                }
            }
        } else {
            $saved = $this->getRedis()->set($id, $data);

            // Set the expire time for this key if valid lifeTime
            if ($lifeTime > 0) {
                $this->redis->expire($id, $lifeTime);
            }
        }



        return $saved;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return $this->getRedis()->delete($id);
    }

    /**
     * Returns the serializer constant to use. If Redis is compiled with
     * igbinary support, that is used. Otherwise the default PHP serializer is
     * used.
     *
     * @return integer One of the Redis::SERIALIZER_* constants
     */
    protected function getSerializerValue()
    {
        return defined('Redis::SERIALIZER_IGBINARY') ? RedisBase::SERIALIZER_IGBINARY : RedisBase::SERIALIZER_PHP;
    }
}
