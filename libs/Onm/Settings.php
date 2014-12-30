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
        $settingsManager  = getService('setting_repository');

        return $settingsManager->get($settingName, $default);
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
        $settingsManager  = getService('setting_repository');

        return $settingsManager->set($settingName, $settingValue);
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
        $settingsManager  = getService('setting_repository');

        return $settingsManager->invalidate($settingName, $instanceName);
    }
}
