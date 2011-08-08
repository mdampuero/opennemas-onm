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
    if (array_key_exists('name',$params)) {
        $key = $params['name'];
    } elseif (count($keys) >0) {
        $keys = array_keys($params);
        $key = $keys[0];
    } else {
        return '';
    }

    return \Onm\Settings::get($key);

}
