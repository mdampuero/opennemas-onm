<?php
/*
 * -------------------------------------------------------------
 * File:     	function.image_tag.php
 * Prints an img html tag gitven src url file.
 */
function smarty_function_image_tag($params, &$smarty)
{
    $output = "";

    if (empty($params['src'])) {
        trigger_error("[plugin] image_tag parameter 'src' cannot be empty", E_USER_NOTICE);
        return;
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

    unset($params['src']);
    unset($params['base_url']);
    unset($params['common']);
    unset($params['bundle']);
    $properties = '';
    foreach ($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }

    $output = "<img src=\"{$resource}\" {$properties}>";

    return $output;
}
