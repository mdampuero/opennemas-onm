<?php
/**
 * Applies the localize filter to the field parameter
 *
 * @param array $params  Parameters of smarty function
 * @param Smarty $smarty Object reference to Smarty class
 *
 * @return string Return a HTML code of the message board
 */
function smarty_function_localize_filter($params, &$smarty)
{
    if (!is_array($params)
        || !array_key_exists('field', $params)
        || empty($params['field'])
        || !array_key_exists('params', $params)
        || empty($params['params'])
    ) {
        return null;
    }

    if (is_string($params['field'])) {
        return $params['field'];
    }

    $field = (object) ['field' => $params['field']];
    $value = getService('data.manager.filter')->set($field)
        ->filter('localize', [
            'keys'   => ['field'],
            'locale' => $params['params']['default']
        ])->get();

    return $value->field;
}
