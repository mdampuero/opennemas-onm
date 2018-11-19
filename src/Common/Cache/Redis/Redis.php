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

use Common\Cache\Core\Cache;
use Redis as RedisBase;

/**
 * The Redis class provides methods to use a Redis based cache.
 */
class Redis extends Cache
{
    /**
     * The Redis connection.
     *
     * @var RedisBase
     */
    protected $redis;

    /**
     * Initializes the Redis client.
     */
    public function __construct($data)
    {
        if (!array_key_exists('name', $data)
            || !array_key_exists('server', $data)
            || !array_key_exists('port', $data)
        ) {
            throw new \InvalidArgumentException();
        }

        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function contains($id)
    {
        return $this->getRedis()->exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function delete($id)
    {
        return $this->getRedis()->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function deleteByPattern($pattern)
    {
        return $this->getRedis()->eval(
            'for i, name in ipairs(redis.call(\'KEYS\', ARGV[1])) do redis.call(\'DEL\', name); end',
            [ $pattern ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function deleteMulti($id)
    {
        return $this->getRedis()->delete($id);
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
            $this->redis->pconnect($this->server, $this->port);

            if (!empty($this->auth)) {
                $this->redis->auth($this->auth);
            }
        }

        return $this->redis;
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
