<?php
/**
 * Defines the Onm\Settings class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_Settings
 */
namespace Onm;

/**
 * Wrapper class to get/set settings in db with support of cache.
 *
 * Conventions:
 *    All the setting names should have the next format:
 *      module_value
 *          where module stands on the module name the setting
 *          where  value stands on the key name for the setting
 *    Example:
 *      europapress_server
 *          europapress is the name of the module
 *          server is the name of the setting
 *
 * @package    Onm_Settings
 */
class Settings
{
    /**
     * Fetches a setting from its name.
     *
     * Example:
     *  use Onm\Settings as s;
     *  s::get('opinion');
     *
     * @param string $settingName the name of the setting.
     * @param array  $default     the default value to return if not available
     *
     * @return string the value of the setting
     * @return array  if was provided an array of names this function returns an array of name/values
     * @return false  if the key doesn't exists or is not setted
     */
    public static function get($settingName, $default = null)
    {
        // the setting name must be setted
        if (!isset($settingName) || empty($settingName)) {
            return false;
        };

        $cache = getService('cache');

        if (!is_array($settingName)) {
            // Try to fetch the setting from cache first
            $settingValue = $cache->fetch(CACHE_PREFIX . $settingName);

            // If was not fetched from cache now is turn of DB
            if (!$settingValue) {
                $sql = "SELECT value FROM `settings` WHERE name = ?";
                $rs = $GLOBALS['application']->conn->GetOne($sql, array($settingName));

                if ($rs === false) {
                    return false;
                }

                if ($rs === null && !is_null($default)) {
                    $settingValue = $default;
                } else {
                    $settingValue = unserialize($rs);
                }

                $cache->save(CACHE_PREFIX . $settingName, $settingValue);
            }
        } else {
            // Try to fetch each setting from cache first
            $cacheSettingName = array();
            foreach ($settingName as $key) {
                $cacheSettingName []= CACHE_PREFIX . $key;
            }

            $cacheSettingValue = $cache->fetch($cacheSettingName);

            $settingValue = array();

            if (!empty($cacheSettingValue)) {
                foreach ($cacheSettingValue as $key => $value) {

                    $keyName = str_replace(CACHE_PREFIX, "", $key);

                    $settingValue[$keyName] = $value;
                }
            }

            // If all the keys were not fetched from cache now is turn of DB
            if (!is_null($settingValue)) {
                $settings = implode("', '", $settingName);
                $sql         = "SELECT name, value FROM `settings` WHERE name IN ('{$settings}') ";
                $rs          = $GLOBALS['application']->conn->Execute($sql);

                if (!$rs) {
                    return false;
                }

                $settingValue = array();
                foreach ($rs as $option) {
                    $settingValue[$option['name']] = unserialize($option['value']);
                }

                $cacheSettingName = array();
                foreach ($settingValue as $key => $option) {
                    $cacheSettingName [CACHE_PREFIX . $key] = $option;
                }
                $cache->save($settingName, $cacheSettingName);

            }

        }

        return $settingValue;
    }

    /**
     * Stores a setting in DB and updates cache entry for it.
     *
     * Example:
     *  use Onm\Settings as s;
     *  s::set('opinion', 'test');
     *
     * @param string $settingName  the name of the setting.
     * @param string $settingValue the value of the setting.
     *
     * @return boolean true if the setting was stored.
     */
    public static function set($settingName, $settingValue)
    {
        $cache = getService('cache');
        // the setting name must be setted
        if (!isset($settingName) || empty($settingName)) {
            return false;
        }

        $settingValueSerialized = serialize($settingValue);

        $sql = "INSERT INTO settings (name,value)
                VALUES ('{$settingName}','{$settingValueSerialized}')
                ON DUPLICATE KEY UPDATE value='{$settingValueSerialized}'";

        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            return false;
        }
        $cache->save(CACHE_PREFIX . $settingName, $settingValue);


        return true;
    }

    /**
     * Invalidates the cache for a setting from its name.
     *
     * Example:
     *  use Onm\Settings as s;
     *  s::invalidate('opinion');
     *
     * @param string $settingName  the name of the setting.
     * @param string $instanceName the name of the instance.
     *
     * @return boolean true if the setting cache was invalidated.
     */
    public static function invalidate($settingName, $instanceName = null)
    {
        $cache = getService('cache');

        if (is_null($instanceName)) {
            $instanceName = CACHE_PREFIX;
        }

        // the setting name must be setted
        if (!isset($settingName) || empty($settingName)) {
            return false;
        }

        $cache->delete($instanceName . $settingName);

        return true;
    }
}
