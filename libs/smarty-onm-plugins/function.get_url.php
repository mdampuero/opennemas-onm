<?php
/*
 * -------------------------------------------------------------
 * File:        function.get_url.php
 * Returns the url for a given content
 * -------------------------------------------------------------
 */
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

function smarty_function_get_url($params)
{
    if (!array_key_exists('item', $params) && !is_object($params['item'])) {
       return '';
    }

    $content = $params['item'];
    $absolute = array_key_exists('absolute', $params) && $params['absolute'];

    return getService('core.helper.url_generator')
        ->generate($params['item'], ['absolute' => $absolute]);
}
