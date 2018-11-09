<?php
/**
 * Decodes a json string
 *
 * @param string
 * @return string
 */
function smarty_function_json_decode($params, &$smarty)
{
    if (!isset($params['value'])) {
        $smarty->_trigger_fatal_error('[plugin] json_decode needs a "value" param');

        return '';
    }

    $json  = $params['value'];
    $assoc = (isset($params['assoc'])) ? $params['assoc'] : false;

    $output = json_decode($json, $assoc);

    return $output;
}
