<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Cache\Redis;

use Redis as RedisBase;
use Common\Cache\Core\Cache;

class Redis extends Cache
{
    /**
     * The Redis configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * The Redis connection.
     *
     * @var Redis
     */
    protected $redis;

    /**
     * Initializes the Redis client.
     */
    public function __construct($options)
    {
        if (!array_key_exists('server', $options)
            && !array_key_exists('port', $options)
        ) {
            throw new \InvalidArgumentException();
        }

        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    protected function fetch($id)
    {
        return \unserialize($this->getRedis()->get($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchMulti($ids)
    {
        $values = $this->getRedis()->mGet($ids);
        $ids    = $this->getUnNamespacedId($ids);

        $values = array_map(function ($a) {
            return \unserialize($a);
        }, $values);

        return array_filter(array_combine($ids, $values), function ($a) {
            return !empty($a);
        });
    }

    /**
     * Gets a new Redis connection.
     *
     * @return BaseRedis The redis client.
     */
    protected function getRedis()
    {
        if (!is_object($this->redis)) {
            $this->redis = new RedisBase();
            $this->redis->pconnect(
                $this->options['server'],
                $this->options['port']
            );

            if (array_key_exists('auth', $this->options)) {
                $this->redis->auth($this->options['auth']);
            }
        }

        return $this->redis;
    }

    /**
     * {@inheritdoc}
     */
    protected function remove($id)
    {
        $this->getRedis()->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function removeMulti($id)
    {
        $this->getRedis()->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function save($id, $data, $ttl)
    {
        $this->getRedis()->set($id, serialize($data));

        if (!empty($ttl)) {
            $this->getRedis()->expire($id, $ttl);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function saveMulti($data)
    {
        $data = array_map(function ($a) {
            return serialize($a);
        }, $data);

        $this->getRedis()->mSet($data);
    }
}
