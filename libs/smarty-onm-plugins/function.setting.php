<?php
/**
 * Smarty plugin for getting system settings
 *
 * Usage:
 *   {setting name=key [field=subkey]}
 *
*/
function smarty_function_setting($params)
{
    if (!array_key_exists('name', $params)) {
        return '';
    }

    $output = '';
    $key    = $params['name'];
    $sr     = getService('setting_repository');

    if (!array_key_exists('field', $params)) {
        return $sr->get($key);
    }

    $keyValue = $sr->get($key);
    if (is_array($keyValue)) {
        foreach ($keyValue as $name => $value) {
            if ($name == $params['field']) {
                $output = $value;
            }
        }
    }

    return $output;
}
