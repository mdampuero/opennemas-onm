<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Repository;

use Repository\BaseManager;

/**
 * Handles common actions in Menus
 *
 * @package Repository
 **/
class SettingManager extends BaseManager
{
    /**
     * Fetches a setting from its name.
     *
     * @param string $settingName the name of the setting.
     * @param array  $default     the default value to return if not available
     *
     * @return string the value of the setting
     * @return array  if was provided an array of names this function returns an array of name/values
     * @return false  if the key doesn't exists or is not setted
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
                $rs = $this->dbConn->GetOne(
                    "SELECT value FROM `settings` WHERE name = ?",
                    array($settingName)
                );

                if ($rs === false) {
                    return false;
                }

                if ($rs === null && !is_null($default)) {
                    $settingValue = $default;
                } else {
                    $settingValue = unserialize($rs);
                }

                $this->cache->save($settingName, $settingValue);
            }
        } else {
            // Get settings in string format separeted by comma
            $settings = implode("', '", $settingName);

            // Try to fetch each setting from cache first
            $settingValue = $this->cache->fetch($settings);

            // If all the keys were not fetched from cache now is turn of DB
            if (is_null($settingValue) || empty($settingValue)) {
                $sql      = "SELECT name, value FROM `settings` WHERE name IN ('{$settings}') ";
                $rs       = $this->dbConn->Execute($sql);

                if (!$rs) {
                    return false;
                }

                $settingValue = array();
                foreach ($rs as $option) {
                    $settingValue[$option['name']] = unserialize($option['value']);
                }

                $this->cache->save($settings, $settingValue);
            }
        }

        return $settingValue;
    }

    /**
     * Stores a setting in DB and updates cache entry for it.
     *
     * @param string $settingName  the name of the setting.
     * @param string $settingValue the value of the setting.
     *
     * @return boolean true if the setting was stored.
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

        $rs = $this->dbConn->Execute($sql);

        if (!$rs) {
            return false;
        }
        $this->cache->save($settingName, $settingValue);

        return true;
    }

    /**
     * Invalidates the cache for a setting from its name.
     *
     * @param string $settingName  the name of the setting.
     * @param string $instanceName the name of the instance.
     *
     * @return boolean true if the setting cache was invalidated.
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
