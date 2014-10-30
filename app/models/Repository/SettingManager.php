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
    protected $toAutoload = [
        'site_color',
        'site_description',
        'site_footer',
        'site_keywords',
        'site_language',
        'site_logo',
        'site_title',
        'time_zone'
    ];

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

        if (count($this->autoloaded) < count($this->toAutoload)) {
            $this->autoloadSettings();
        }

        $searched = $name;
        if (!is_array($name)) {
            $searched = array($name);
        }

        $results = array();

        $missed = array_diff($searched, $this->toAutoload);
        $fromAutoload = array_diff($searched, $missed);

        // Load settings from autoload
        $results = array_intersect_key(
            $this->autoloaded,
            array_flip($fromAutoload)
        );

        // Load settings from cache
        $results = array_merge($results, $this->cache->fetch($missed));

        // Check the settings to fetch from database
        $missed = array_diff($searched, array_keys($results));

        // Load the settings from database
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

        if (in_array($name, $this->toAutoload)) {
            $this->autoloaded[$name] = $value;
        }

        $serialized = serialize($value);
        $sql = "INSERT INTO settings (name,value) "
                ."VALUES ('$name', '$serialized') "
                ."ON DUPLICATE KEY UPDATE value = '$serialized'";

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

    /**
     * Load all settings in toAutoload array.
     */
    public function autoloadSettings()
    {
        // Build autoload
        if (empty($this->autoloaded)) {
            // First search from cache
            $this->autoloaded = $this->cache->fetch($this->toAutoload);

            // Check for missed properties
            $missed = array_diff(
                $this->toAutoload,
                array_keys($this->autoloaded)
            );

            // Second search in database
            $rs = $this->conn->fetchAll(
                "SELECT * FROM `settings` WHERE name IN ('"
                . implode("', '", $missed) . "')"
            );

            $names = array();
            foreach ($rs as $setting) {
                $value = unserialize($setting['value']);
                $names[] = $setting['name'];

                $this->autoloaded[$setting['name']] = $value;
                $this->cache->save($setting['name'], $value);
            }
        }

        return $this;
    }
}
