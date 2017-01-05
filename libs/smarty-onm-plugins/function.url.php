<?php
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/*
 * -------------------------------------------------------------
 * File:        function.url.php
 * returns the url given a set of params
 */
function smarty_function_url($params, &$smarty)
{
    $url = '';
    if (array_key_exists('name', $params)) {
        $name = $params['name'];
        if (array_key_exists('absolute', $params) && $params['absolute']) {
            $absolute = UrlGeneratorInterface::ABSOLUTE_URL;
        } else {
            $absolute = UrlGeneratorInterface::ABSOLUTE_PATH;
        }
        unset($params['name'], $params['absolute']);
        try {
            $url = getService('router')->generate($name, $params, $absolute);
        } catch (RouteNotFoundException $e) {
            $url = '#not-found-'.$params['name'];
        } catch (\Exception $e) {
            $url = '#not-found';
        }
    }

    return $url;
}
