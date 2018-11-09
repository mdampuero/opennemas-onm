<?php
/**
 * Encodes a variable into a json string
 *
 * @param string
 *
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
