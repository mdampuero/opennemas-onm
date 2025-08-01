<?php
/**
 * Smarty plugin for getting system settings
 *
 * Usage:
 *   {setting name=key [field=subkey]}
 */
function smarty_function_setting($params, &$smarty)
{
    if (!array_key_exists('name', $params)) {
        return '';
    }

    $default = null;
    $request = $smarty->getContainer()->get('request_stack')
        ->getCurrentRequest();

    if ($params['name'] === 'refresh_interval'
        && !empty($request)
        && preg_match('@^/admin@', $request->getRequestUri())
    ) {
        return -1;
    }

    if (array_key_exists('default', $params)) {
        $default = $params['default'];
    }

    $value = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get($params['name'], $default);

    if (array_key_exists('field', $params)) {
        if (is_array($value) && array_key_exists($params['field'], $value)) {
            return $value[$params['field']];
        }

        return '';
    }

    return $value;
}
