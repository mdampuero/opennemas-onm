<?php
/**
 * Smarty plugin for getting system settings
 *
 * Usage:
 *   {setting settings_name}
 *
*/
function smarty_function_setting($params, &$smarty)
{
    $keys = array_keys($params);
    if (count($keys) >0) {
        return \Onm\Settings::get($keys[0]);
    }
}
