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
    $mtime  = THEMES_DEPLOYED_AT;
    $server = '';
    $type   = "type=\"text/javascript\"";
    $escape = false;

    //Comprobar si es un link externo
    if (!array_key_exists('external', $params)) {
        $server = DS . 'assets' . DS . 'js' . DS;

        if (!array_key_exists('common', $params)) {
            $basepath = $params['basepath'] ? : DS . 'js';
            $server   = DS . $smarty->getTheme()->path . DS . $basepath;
        }
    }

    if (isset($params['type'])) {
        $type = "type=\"{$params['type']}\"";
    }

    if (isset($params['escape'])) {
        $escape = true;
    }

    unset($params['common']);
    unset($params['src']);
    unset($params['type']);
    unset($params['escape']);
    unset($params['basepath']);

    $properties = '';
    foreach ($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }

    $resource = empty($server) ? $src : $server . DS . $src;

    if ($params['external'] != 1) {
        $resource = preg_replace('/(\/+)/', '/', $resource);
        $resource = str_replace('.js', '.' . $mtime . '.js', $resource);
    }

    $output = "<script {$type} src=\"{$resource}\" {$properties} ></script>";

    if ($escape) {
        $output = str_replace('</', '<\/', $output);
    }

    return $output;
}
