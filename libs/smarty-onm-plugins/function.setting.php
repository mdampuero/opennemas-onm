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
    $request = $smarty->getContainer()->get('request_stack')
        ->getCurrentRequest();

    if (!array_key_exists('name', $params)
        || ($params['name'] === 'refresh_interval'
            && !empty($request)
            && preg_match('@/admin@', $request->getRequestUri()))
    ) {
        return '';
    }

    $ds    = $smarty->getContainer()->get('orm.manager')->getDataSet('Settings');
    $value = $ds->get($params['name']);

    if (array_key_exists('field', $params)) {
        if (is_array($value) && array_key_exists($params['field'], $value)) {
            return $value[$params['field']];
        }

        return '';
    }

    return $value;
}
