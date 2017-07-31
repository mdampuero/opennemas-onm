<?php
/*
 * -------------------------------------------------------------
 * File:        function.get_url.php
 * Returns the url for a given content
 * -------------------------------------------------------------
 */
function smarty_function_get_url($params)
{
    if (!array_key_exists('item', $params)
        || !is_object($params['item'])
        || empty($params['item']->id)
    ) {
       return '';
    }

    $content = $params['item'];
    $absolute = array_key_exists('absolute', $params) && $params['absolute'];

    return getService('core.helper.url_generator')
        ->generate($params['item'], ['absolute' => $absolute]);
}
