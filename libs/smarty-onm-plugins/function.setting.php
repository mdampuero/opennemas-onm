<?php
/**
 * Smarty plugin for getting system settings
 *
 * Usage:
 *   {setting name=key [field=subkey]}
 *
*/
function smarty_function_setting($params, &$smarty)
{
    $output = '';
    if (array_key_exists('name',$params)) {
        $key = $params['name'];
        if (array_key_exists('field',$params)) {
            $key_value = \Onm\Settings::get($key);
            if (is_array($key_value)) {
                foreach ($key_value as $name => $value) {
                    if ($name == $params['field']) {
                        $output = $value;
                    }
                }
            }
        } else {
            $output = \Onm\Settings::get($key);
        }
    } elseif (count($keys) >0) {
        $keys = array_keys($params);
        $key = $keys[0];
    } else {
        return '';
    }

    return $output;

}
