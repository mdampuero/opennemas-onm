<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.css_tag.php
 * Type:     function
 * Name:     css_tag
 * Purpose:  Returns the URL for a stylesheet.
 * -------------------------------------------------------------
 */
function smarty_function_css_tag($params, &$smarty)
{
    if (empty($params['href'])) {
        trigger_error("[plugin] css_tag parameter 'href' cannot be empty", E_USER_NOTICE);
        return;
    }

    $output = '';
    $href   = $params['href'];
    $server = DS . 'assets' . DS . 'css' . DS;
    $mtime  = DEPLOYED_AT;
    $type   = 'type="text/css"';
    $rel    = 'rel="stylesheet"';

    if (!array_key_exists('common', $params)) {
        $basepath = $params['basepath'] ? : DS . 'css';
        $server   = DS . $smarty->getTheme()->path . DS . $basepath;
    }

    if (isset($params['type'])) {
        $type = "type=\"{$params['type']}\"";
    }

    if (isset($params['rel'])) {
        $rel = "rel=\"{$params['rel']}\"";
    }

    unset($params['rel']);
    unset($params['href']);
    unset($params['type']);
    unset($params['basepath']);
    unset($params['common']);

    $properties = '';
    foreach ($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }

    $resource = $server . DS . $href;

    if ($params['external'] != 1) {
        $resource = preg_replace('/(\/+)/', '/', $resource);
        $resource = str_replace('.css', '.'.$mtime.'.css', $resource);
    }

    $output = "<link {$rel} {$type} href=\"{$resource}\" {$properties}>";

    return $output;
}
