<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm;
/**
 * Wrapper class to get/set settings in db with support of APC caching.
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
 * @package    Onm
 * @subpackage Settings
 * @author     Fran Dieguez <fran@openhost.es>
 * @version    Git: $Id: Settings.php Mér Xul 13 01:06:01 2011 frandieguez $
 */
class Settings {

    /**
     * Fetches a setting from its name.
     *
     * Example:
     *  use Onm\Settings as s;
     *  s::get('opinion');
     *
     * @param string $settingName the name of the setting.
     * @param array $settingName  array of settings name
     *
     * @return string the value of the setting
     * @return array  if was provided an array of names this function returns an array of name/values
     * @return false  if the key doesn't exists or is not setted
     */
    static public function get( $settingName )
    {
        // the setting name must be setted
        if (!isset($settingName) || empty($settingName)) { return false; };

        if (!is_array($settingName)) {

            // Try to fetch the setting from APC first
            $fetchedFromAPC = false;
            if (extension_loaded('apc')) {
                $settingValue = apc_fetch(APC_PREFIX . "_". $settingName, $fetchedFromAPC);
            }

            // If was not fetched from APC now is turn of DB
            if (!$fetchedFromAPC) {
                $sql = "SELECT value FROM `settings` WHERE name = \"{$settingName}\"";
                $rs = $GLOBALS['application']->conn->GetOne( $sql );


                if (!$rs) {
                    $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                    $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

                    return false;
                }

                $settingValue = unserialize($rs);

                if (extension_loaded('apc')) {
                    apc_store(APC_PREFIX . "_".$settingName, $settingValue);
                }

            }

        } else {

            // Try to fetch each setting from APC first
            $fetchedFromAPC = false;
            if (extension_loaded('apc')) {
                $apcSettingName = array();
                foreach ($settingName as $key) {
                    $apcSettingName []= APC_PREFIX . "_". $key;
                }

                $apcSettingValue = apc_fetch($apcSettingName, $fetchedFromAPC);

                $settingValue = array();
                foreach ($apcSettingValue as $key => $value ) {
                    $key = preg_replace("@".APC_PREFIX . "_@","", $key);
                    $settingValue[$key] = $value;
                }

            }

            // If all the keys were not fetched from APC now is turn of DB
            if (
                !is_null($settingValue)
                && (count($settingValue) != count($settingName))
            ) {

                $settingName = implode("', '", $settingName);
                $sql = ( "SELECT name, value FROM `settings` WHERE name IN ('{$settingName}') ");
                $rs = $GLOBALS['application']->conn->Execute( $sql );

                if (!$rs) {
                    $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                    $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

                    return false;
                }

                $settingValue = array();
                foreach ($rs as $option) {
                    $settingValue[$option['name']] = unserialize($option['value']);
                }

                if (extension_loaded('apc')) {
                    $apcSettingName = array();
                    foreach ($settingValue as $key => $option) {
                        $apcSettingName [APC_PREFIX . "_". $key] = $option;
                    }
                    apc_store($apcSettingName);
                }

            }

        }

        return $settingValue;
    }

    /**
     * Stores a setting in DB and updates apc cache entry for it.
     *
     * Example:
     *  use Onm\Settings as s;
     *  s::set('opinion', 'test');
     *
     * @param string $settingName the name of the setting.
     * @param string $settingValue the value of the setting.
     *
     * @return boolean true if the setting was stored.
     */
    static public function set($settingName, $settingValue)
    {
        // the setting name must be setted
        if (!isset($settingName) || empty($settingName)) { return false; };

        $settingValueSerialized = serialize($settingValue);

        $sql = "INSERT INTO settings (name,value)
                            VALUES ('{$settingName}','{$settingValueSerialized}')
                            ON DUPLICATE KEY UPDATE value='{$settingValueSerialized}'";

        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return false;
        }
        if (extension_loaded('apc')) {
            apc_store(APC_PREFIX . "_".$settingName, $settingValue);
        }

        return true;
    }


}
