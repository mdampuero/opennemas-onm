<?php
/*
 * -------------------------------------------------------------
 * File:        function.url.php
 * returns the url given a set of params
 */
function smarty_function_url($params, &$smarty) {

    $url = '';

    global $sc;
    $generator = $sc->get('url_generator');
    if (is_object($generator)) {
        if (array_key_exists('name', $params)) {
            $name = $params['name'];
            $absolute = $params['absolute'];
            unset($params['name'], $params['absolute']);
            try {
                $url = $generator->generate($name, $params, $absolute);
            } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
                $url = '#not-found-'.$params['name'];
            } catch (\Exception $e) {
                $url = '#not-found';
            }
        }
    } else {
        $url = '#url-generator-not-available';
    }

    return $url;
}
