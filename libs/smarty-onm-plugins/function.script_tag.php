<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.script_tag.php
 * Type:     function
 * Name:     script_tag
 * Purpose:  Returns the URL for a script.
 * -------------------------------------------------------------
 */
function smarty_function_script_tag($params, &$smarty)
{
    if (empty($params['src'])) {
        trigger_error("[plugin] script_tag parameter 'src' cannot be empty", E_USER_NOTICE);
        return;
    }

    $output = '';
    $src    = $params['src'];
    $mtime  = DEPLOYED_AT;
    $server = '';
    $escape = false;

    // Comprobar si es un link externo
    if (!array_key_exists('external', $params)) {
        $server = DS . 'assets' . DS . 'js' . DS;

        if (!array_key_exists('common', $params)) {
            $basepath = (array_key_exists('basepath', $params)
                && $params['basepath']) ? : DS . 'js';
            $server   = DS . $smarty->getTheme()->path . DS . $basepath;
            $mtime    = THEMES_DEPLOYED_AT;
        }
    }

    if (isset($params['escape'])) {
        $escape = true;
    }

    // Clean internal properties
    $keys = [ 'basepath', 'common', 'escape', 'src', 'type', ];
    foreach ($keys as $key) {
        unset($params[$key]);
    }

    $properties = '';
    foreach ($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }

    $resource = empty($server) ? $src : $server . DS . $src;

    if (!array_key_exists('external', $params) || $params['external'] != 1) {
        $resource = preg_replace('/(\/+)/', '/', $resource);
        $resource = str_replace('.js', '.' . $mtime . '.js', $resource);
    }

    $output = "<script src=\"{$resource}\" {$properties}></script>";

    if ($escape) {
        $output = str_replace('</', '<\/', $output);
    }

    return $output;
}
