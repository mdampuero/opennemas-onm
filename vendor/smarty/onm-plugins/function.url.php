<?php
/*
 * -------------------------------------------------------------
 * File:        function.url.php
 * returns the url given a set of params
 */
function smarty_function_url($params, &$smarty) {

    $url = '';

    global $generator;
    if (array_key_exists('name', $params)) {
        $name = $params['name'];
        unset($params['name']);
        try {
            $url = $generator->generate($name, $params);
        } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
            $url = 'not-found '.$params['name'];
        }
    }

    return $url;
}