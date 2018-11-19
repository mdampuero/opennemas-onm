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
    protected $autoloaded = [];

    /**
     * Array of names of settings to auto-load.
     *
     * @var array
     */
    protected $toAutoload = [
        'comscore',
        'favico',
        'google_analytics',
        'locale',
        'ojd',
        'piwik',
        'site_color',
        'site_description',
        'site_footer',
        'site_keywords',
        'site_logo',
        'site_name',
        'site_title',
    ];

    /**
     * Array of names of settings to auto-load for manager.
     *
     * @var array
     */
    protected $toAutoloadManager = [
        'locale',
    ];

    /**
     * Sets a new database name and cache prefix to use in the service.
     *
     * @return boolean
     */
    public function setConfig($config)
    {
        if (array_key_exists('database', $config)) {
            $this->dbConn->selectDatabase($config['database']);
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
        if (empty($name)) {
            return false;
        }

        $toAutoload = $this->toAutoload;

        if ($this->cache->getNamespace() === 'manager') {
            $toAutoload = $this->toAutoloadManager;
        }

        if (count($this->autoloaded) < count($toAutoload)) {
            $this->autoloadSettings();
        }

        $results = [];
        $keys    = $name;

        // Normalize keys and default values
        if (!is_array($name)) {
            $keys    = [ $name ];
            $default = [ $default ];
        } elseif (!is_array($default)) {
            $default = array_fill(0, count($keys), $default);
        }

        // Build results with default values
        $default = array_combine($keys, $default);

        // Load settings from autoload
        $fromAutoload = array_intersect_key($this->autoloaded, $default);
        $results      = array_merge($results, $fromAutoload);

        // Missed keys from autoload
        $missed = array_diff($keys, array_keys($results));

        if (!empty($missed)) {
            $results = array_merge($results, $this->cache->fetch($missed));
        }

        $missed = array_diff($keys, array_keys($results));

        // Fetch missed settings from database and add them to cache
        $missed = array_diff($keys, array_keys($results));
        if (!empty($missed)) {
            $sql = "SELECT name, value FROM `settings` WHERE name IN ('"
                . implode("', '", $missed) . "')";

            $rs = $this->dbConn->fetchAll($sql);
            foreach ($rs as $setting) {
                $value                     = @unserialize($setting['value']);
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

        $results = array_merge($default, $results);

        if (is_array($name)) {
            return array_merge($default, $results);
        }

        return array_pop($results);
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
            . "VALUES ('$name', '$serialized') "
            . "ON DUPLICATE KEY UPDATE value = '$serialized'";

        $this->dbConn->executeUpdate($sql);
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
        $rs = $this->dbConn->fetchAll(
            "SELECT * FROM `settings` WHERE name IN ('"
            . implode("', '", $missed) . "')"
        );

        $names = array();
        foreach ($rs as $setting) {
            $value   = @unserialize($setting['value']);
            $names[] = $setting['name'];

            $this->autoloaded[$setting['name']] = $value;
            $this->cache->save($setting['name'], $value);
        }

        $notInDatabase = array_diff($missed, array_keys($this->autoloaded));

        return $this;
    }
}
