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
        'comscore',
        'favico',
        'google_analytics',
        'ojd',
        'piwik',
        'site_color',
        'site_description',
        'site_footer',
        'site_keywords',
        'site_language',
        'site_logo',
        'site_name',
        'site_title',
        'time_zone'
    ];

    /**
     * Array of names of settings to auto-load for manager.
     *
     * @var array
     */
    protected $toAutoloadManager = [
        'site_language',
        'time_zone'
    ];

    /*
     * Initializes the InstanceManager.
     *
     * @param Connection     $dbConn The custom DBAL wrapper.
     * @param CacheInterface $cache  The cache instance.
     */
    public function __construct($conn, CacheInterface $cache, $prefix)
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

        $this->autoloaded = [];

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

        $toAutoload = $this->toAutoload;

        if ($this->cache->getNamespace() === 'manager') {
            $toAutoload = $this->toAutoloadManager;
        }

        if (count($this->autoloaded) < count($toAutoload)) {
            $this->autoloadSettings();
        }

        $results = array();

        $searched = $name;
        if (!is_array($name)) {
            // $name like setting
            $searched = array($name);

            if ($default != null) {
                $default = array($default);
            }
        } elseif (array_keys($name) !== range(0, count($name) - 1)) {
            // $name like [ setting1 => default1, setting2 => default2 ]
            $searched = array_keys($name);
            $results = $name;
        }

        $missed = array_diff($searched, $this->toAutoload);
        $fromAutoload = array_diff($searched, $missed);

        // Add auto-loaded settings to final results
        $results = array_merge(
            $results,
            array_intersect_key(
                $this->autoloaded,
                array_flip($fromAutoload)
            )
        );

        // Add missed settings to final results from cache
        if (!empty($missed)) {
            $results = array_merge($results, $this->cache->fetch($missed));
        }

        // Fetch missed settings from database and add them to cache
        $missed = array_diff($searched, array_keys($results));
        if (!empty($missed)) {
            $sql = "SELECT name, value FROM `settings` WHERE name IN ('"
                . implode("', '", $missed) . "')";

            $rs = $this->conn->fetchAll($sql);
            foreach ($rs as $setting) {
                $value = unserialize($setting['value']);
                $results[$setting['name']] = $value;

                $this->cache->save($setting['name'], $value);
            }

            // Save lost settings (not in database) in cache as lost
            $notInDatabase = array_diff($missed, array_keys($results));
            foreach ($notInDatabase as $item) {
                $this->cache->save($item, $this->lostValue);
            }
        }

        // Remove lost settings from results
        $results = array_filter($results, function ($value) {
            return $value !== $this->lostValue;
        });

        if (is_array($default)) {
            $results = array_merge($default, $results);
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
        $toAutoload = $this->toAutoload;

        if ($this->cache->getNamespace() === 'manager') {
            $toAutoload = $this->toAutoloadManager;
        }

        // Build autoload
        if (!empty($this->autoloaded)) {
            return $this;
        }

        // First search from cache
        $this->autoloaded = $this->cache->fetch($toAutoload);

        // Check for missed properties
        $missed = array_diff($toAutoload, array_keys($this->autoloaded));

        if (empty($missed)) {
            return $this;
        }

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

        $notInDatabase = array_diff($missed, array_keys($this->autoloaded));

        return $this;
    }
}
