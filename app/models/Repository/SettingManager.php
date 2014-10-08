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
    /**
     * Array of auto-loaded settings.
     *
     * @var array
     */
    protected $autoloaded = array();

    /**
     * Array of names of settings to auto-load.
     *
     * @var array
     */
    protected $toAutoload = array('site_color', 'site_description',
        'site_footer', 'site_keywords', 'site_language', 'site_logo',
        'site_title', 'time_zone');

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
     * @param string $name    The name of the setting.
     * @param array  $default The default value to return if not available.
     *
     * @return string The value of the setting if $name is a string.
     * @return array  An array of values if $name is an array of strings.
     * @return false  If the key doesn't exists or is not set.
     */
    public function get($name, $default = null)
    {
        if (!isset($name) || empty($name)) {
            return false;
        }

        $settingValue = $default;

        // Build autoload
        if (empty($this->autoloaded)) {
            $rs = $this->conn->fetchAll(
                "SELECT * FROM `settings` WHERE name IN ('"
                . implode("', '", $this->toAutoload) . "')"
            );

            $names = array();
            foreach ($rs as $setting) {
                $value = unserialize($setting['value']);
                $names[] = $setting['name'];

                $this->autoloaded[$setting['name']] = $value;
                $this->cache->save($setting['name'], $value);
            }
        }

        $searched = $name;
        if (!is_array($name)) {
            $searched = array($name);
        }

        $results = array();
        $missed  = array();
        foreach ($searched as $setting) {
            if (in_array($setting, array_keys($this->autoloaded))) {
                // Get from autoload
                $results[] = $this->autoloaded[$setting];
            } else {
                // Get from cache
                $value = $this->cache->fetch($setting);

                if (!empty($value)) {
                    $results[] = $value;
                } else {
                    $missed[] = $setting;
                }
            }
        }

        // Get missed settings from database
        if (!empty($missed)) {
            $sql = "SELECT name, value FROM `settings` WHERE name IN ('"
                . implode("', '", $missed) . "')";

            $rs = $this->conn->fetchAll($sql);

            if (!$rs) {
                return false;
            }

            foreach ($rs as $setting) {
                $value = unserialize($setting['value']);
                $results[$setting['name']] = $value;

                $this->cache->save($setting['name'], $value);
            }
        }

        if (!is_array($name)) {
            return array_pop($results);
        }

        return $results;
    }

    /**
     * Stores a setting in DB and updates cache entry for it.
     *
     * @param string $name  The name of the setting.
     * @param string $value The value of the setting.
     *
     * @return boolean True if the setting was stored. Otherwise, returns false.
     */
    public function set($name, $value)
    {
        if (!isset($name) || empty($name)) {
            return false;
        }

        $autoload = 0;
        if (in_array($name, $this->toAutoload)) {
            $autoload = 1;
            $this->autoloaded[$name] = $value;
        }

        $serialized = serialize($value);
        $sql = "INSERT INTO settings (name,value,autoload) "
                ."VALUES ('$name', '$serialized', '$autoload') "
                ."ON DUPLICATE KEY UPDATE value = '$serialized', "
                . "autoload = '$autoload'";

        $rs = $this->conn->executeQuery($sql);

        if (!$rs) {
            return false;
        }
        $this->cache->save($name, $value);

        return true;
    }

    /**
     * Invalidates the cache for a setting from its name.
     *
     * @param string $settingName The name of the setting.
     * @param string $cachePrefix The name of the instance.
     *
     * @return boolean True if the setting cache was invalidated. Otherwise,
     *                 returns false
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
