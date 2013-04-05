<?php
/*
 * -------------------------------------------------------------
 * File:     	function.script_tag.php
 * Comprueba el tipo y escribe el nombre o la imag
 */

function smarty_function_css_tag($params, &$smarty)
{

    $output = "";

    if (empty($params['href'])) {
        trigger_error("[plugin] css_tag parameter 'href' cannot be empty", E_USER_NOTICE);
        return;
    }

    $href = $params['href'];

    $server = '';
    $file = '';
    if (array_key_exists('common', $params)) {
        $file = SITE_PATH.SS."assets".SS."css".SS.$href;
        $server = SS."assets".SS."css".SS;
    } else {
        $basepath = $params["basepath"] ?: SS."css";
        $file = SITE_PATH.SS."themes".SS.$smarty->theme.SS.$basepath.$href;
        $server = SS."themes".SS.$smarty->theme.SS.$basepath;
    }

    $mtime = '1234';
    if (file_exists($file)) {
        $mtime = filemtime($file);
    }

    //Comprobar si tiene type definido
    if (isset($params['type'])) {
        $type = "type=\"{$params['type']}\"";
    } else {
        $type = "type=\"text/css\"";
    }

    //Comprobar si tiene rel definido
    if (isset($params['rel'])) {
        $rel = "rel=\"{$params['rel']}\"";
    } else {
        $rel = "rel=\"stylesheet\"";
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

    $resource = preg_replace('/(\/+)/', '/', $server.SS.$href);
    $resource = preg_replace('@(?<!:)//@', '/', $resource);

    $resource = str_replace('.css', '.'.$mtime.'.css', $resource);

    $output = "<link {$rel} {$type} href=\"{$resource}\" {$properties}>";

    return $output;
}
