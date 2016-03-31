<?php

function smarty_function_get_parameter($params, &$smarty)
{
    $value = '';
    if (is_array($params) &&
        array_key_exists('name', $params) &&
        !empty($params['name'])
    ) {
        if (getService('service_container')->hasParameter($params['name'])) {
            $value = getService('service_container')->getParameter($params['name']);
        }
    }

    return $value;
}
