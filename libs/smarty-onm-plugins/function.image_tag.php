<?php
/*
 * -------------------------------------------------------------
 * File:     	function.image_tag.php
 * Prints an img html tag gitven src url file.
 */
function smarty_function_image_tag($params, &$smarty)
{
    if (array_key_exists('id', $params) && !empty($params['id'])) {
        $photo = getService('entity_repository')->find('Photo', $params['id']);
        $params['src'] = $photo->path_img;
    }

    if (empty($params['src'])) {
        return '';
    }

    $src = $params['src'];

    if (preg_match('@http(s)?://@', $src)) {
        $baseUrl = '';
    } elseif (array_key_exists('common', $params) && $params['common'] == "1") {
        $baseUrl = SS."assets".SS."images".SS;
    } elseif (array_key_exists('bundle', $params)) {
        $baseUrl = SS."bundles".SS.$params['bundle'].SS;
    } elseif (array_key_exists('base_url', $params)) {
        $baseUrl = $params['base_url'].DS;
    } else {
        $baseUrl = INSTANCE_MEDIA.'images';
    }

    $resource = $baseUrl.$src;
    $resource = preg_replace('@(?<!:)//@', '/', $resource);

    $lazyload = ($params['data-src'] == 'lazyload');

    unset($params['src']);
    unset($params['base_url']);
    unset($params['common']);
    unset($params['bundle']);
    unset($params['data-src']);

    if ($lazyload) {
        $params['class'] = "lazy ".(array_key_exists('class', $params)? $params['class']: '');
    }

    $properties = '';
    foreach ($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }

    if ($lazyload) {
        $output = "<img src=\"/assets/images/lazy-bg.png\" data-src=\"{$resource}\" {$properties}>";
    } else {
        $output = "<img src=\"{$resource}\" {$properties}>";
    }

    return $output;
}
