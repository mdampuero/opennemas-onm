<?php
/*
 * -------------------------------------------------------------
 * File:     	function.script_tag.php
 * Comprueba el tipo y escribe el nombre o la imag
 */

function smarty_function_script_tag($params, &$smarty)
{

    $output = "";

    if (empty($params['src'])) {
        trigger_error("[plugin] script_tag parameter 'src' cannot be empty", E_USER_NOTICE);
        return;
    }

    $src = $params['src'];
    $mtime = '1234';
    $server = '';
    //Comprobar si es un link externo
    if (!array_key_exists('external', $params)) {
        $basepath = $params["basepath"] ?: SS."js";
        if (array_key_exists('common', $params) && $params['common']=="1") {
            $file = SITE_PATH.SS."assets".SS."js".SS.$href;
            $serverUrl = SS."assets".SS;
        } else {
            $file = SITE_PATH.SS."themes".SS.$smarty->theme.SS.$basepath.$href;
            $serverUrl = SS."themes".SS.$smarty->theme.SS;
        }

        if (file_exists($file)) {
            $mtime = filemtime($file);
            $server = $serverUrl.$basepath;
        }
    }

    //Comprobar si tiene type definido
    if (isset($params['type'])) {
        $type = "type=\"{$params['type']}\"";
    } else {
        $type = "type=\"text/javascript\"";
    }

    //Comprobar si tiene type definido
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
    //Comprobar si es external
    if ($server == '') {
        $resource = $src;
    } else {
        $resource = $server.SS.$src;
    }

    if ($params['external'] != 1 || $server != '') {
        $resource = str_replace(SS.SS, SS, $resource);
        $resource = str_replace('.js', '.'.$mtime.'.js', $resource);
    }

    // $resource = preg_replace('/(\/+)/','/',$resource);
    // $resource = preg_replace('@(?<!:)//@', '/', $resource);

    $output = "<script {$type} src=\"{$resource}\" {$properties} ></script>";

    if ($escape) {
        $output = str_replace('</', '<\/', $output);
    }

    return $output;
}
