<?php
/**
 * Renders the datetime given a format
 *
 * @param array $params The list of parameters passed to the block.
 * @param \Smarty $smarty The instance of smarty.
 *
 * @return null|string
 */
function smarty_function_datetime($params, &$smarty)
{
    $output = '';

    if (isset($params['date']) && is_object($params['date'])) {
        $format = (array_key_exists('format', $params)) ? $params['format'] : 'Y-m-d H:i:s';

        $time = $params['date'];

        $output = $time->format($format);
    }

    return $output;
}
