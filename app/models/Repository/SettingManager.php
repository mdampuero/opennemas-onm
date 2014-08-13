<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Repository;

use Repository\BaseManager;
use Onm\Database\DbalWrapper;
use Onm\Cache\CacheInterface;

/**
 * Handles common actions in Menus.
 */
class SettingManager extends BaseManager
{
    /*
     * Initializes the InstanceManager.
     *
     * @param DbalWrapper    $dbConn The custom DBAL wrapper.
     * @param CacheInterface $cache  The cache instance.
     */
    public function __construct(DbalWrapper $conn, CacheInterface $cache, $prefix)
    {
        $this->conn        = $conn;
        $this->cache       = $cache;
        $this->cachePrefix = $prefix;
    }

    /**
     * Sets a new database name and cache prefix to use in the service.
     *
     * @return boolean
     */
    public function setConfig($config)
    {
        if (array_key_exists('database', $config)) {
            $this->conn->selectDatabase($config['database']);
        }

        if (array_key_exists('cache_prefix', $config)) {
            $this->cachePrefix = $config['cache_prefix'];
            $this->cache->setNamespace($config['cache_prefix']);
        }

        return true;
    }

    /**
     * Fetches a setting from its name.
     *
     * @param string $settingName The name of the setting.
     * @param array  $default     The default value to return if not available.
     *
     * @return string The value of the setting.
     * @return array  If was provided an array of names this function returns an array of name/values.
     * @return false  If the key doesn't exists or is not setted.
     */
    public function get($settingName, $default = null)
    {
        // the setting name must be setted
        if (!isset($settingName) || empty($settingName)) {
            return false;
        }

        if (!is_array($settingName)) {
            // Try to fetch the setting from cache first
            $settingValue = $this->cache->fetch($settingName);

            // If was not fetched from cache now is turn of DB
            if (!$settingValue) {
                $rs = $this->conn->fetchArray(
                    "SELECT value FROM `settings` WHERE name = ?",
                    array($settingName)
                );

                if ($rs === false) {
                    return false;
                }

                if ($rs === null && empty($rs) && !is_null($default)) {
                    $settingValue = $default;
                } else {
                    $settingValue = unserialize($rs[0]);
                }

                $this->cache->save($settingName, $settingValue);
            }
        } else {
            // Try to fetch each setting from cache first
            $settingValue = $this->cache->fetch($settingName);

            // If all the keys were not fetched from cache now is turn of DB
            if (is_null($settingValue) || empty($settingValue)) {
                $settings = implode("', '", $settingName);
                $sql      = "SELECT name, value FROM `settings` WHERE name IN ('{$settings}') ";
                $rs       = $this->conn->executeQuery($sql);

                if (!$rs) {
                    return false;
                }

                $settingValue = array();
                foreach ($rs as $option) {
                    $settingValue[$option['name']] = unserialize($option['value']);
                }

                $this->cache->save($settingValue, '');
            }
        }

        return $settingValue;
    }

    /**
     * Stores a setting in DB and updates cache entry for it.
     *
     * @param string $settingName  The name of the setting.
     * @param string $settingValue The value of the setting.
     *
     * @return boolean true If the setting was stored.
     */
    public function set($settingName, $settingValue)
    {
        // the setting name must be setted
        if (!isset($settingName) || empty($settingName)) {
            return false;
        }

        $settingValueSerialized = serialize($settingValue);

        $sql = "INSERT INTO settings (name,value) "
                ."VALUES ('{$settingName}','{$settingValueSerialized}')"
                ."ON DUPLICATE KEY UPDATE value='{$settingValueSerialized}'";

        $rs = $this->conn->executeQuery($sql);

        if (!$rs) {
            return false;
        }
        $this->cache->save($settingName, $settingValue);

        return true;
    }

    /**
     * Invalidates the cache for a setting from its name.
     *
     * @param string $settingName The name of the setting.
     * @param string $cachePrefix The name of the instance.
     *
     * @return boolean true If the setting cache was invalidated.
     */
    public function invalidate($settingName, $cachePrefix = null)
    {
        if (is_null($cachePrefix)) {
            $cachePrefix = $this->cachePrefix;
        }

        // the setting name must be setted
        if (!isset($settingName) || empty($settingName)) {
            return false;
        }

        $this->cache->delete($settingName, $cachePrefix);

        return true;
    }
}
