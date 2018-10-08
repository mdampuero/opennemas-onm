<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty json_encode function plugin
 *
 * Type:     function<br>
 * Name:     json_encode<br>
 * Purpose:  json_encode
 * @author   Tomás Vilariño <vifito at gmail dot com>
 * @param string
 * @return string
 */
function smarty_function_json_encode($params, &$smarty)
{
    if (!isset($params['value']) && !isset($params['default'])) {
        $smarty->_trigger_fatal_error('[plugin] json_encode needs a "value" param');

        return '';
    }

    if (!isset($params['value']) && isset($params['default'])) {
        $output = $params['default'];
    } else {
        $output = json_encode($params['value']);
    }

    if (isset($params['assign'])) {
        $smarty->assign($params['assign'], $output);

        return '';
    }

    return $output;
}
