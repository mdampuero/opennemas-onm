<?php
/*
 * -------------------------------------------------------------
 * File:     	function.script_tag.php
 * Comprueba el tipo y escribe el nombre o la imag
 */

function smarty_function_css_tag($params, &$smarty) {

    $output = "";

    if (empty($params['href'])) {
        trigger_error("[plugin] css_tag parameter 'href' cannot be empty",E_USER_NOTICE);
        return;
    }

    $href = $params['href'];


    $mtime = '?';
    $server = '';
    $basepath = $params["basepath"] ?: "/css";
    if ($smarty->theme == 'default') {
        $file = TEMPLATE_ADMIN_PATH.$basepath.$href;
        if (file_exists($file)) {
            $mtime .= filemtime($file);
            $server = TEMPLATE_ADMIN_URL.$basepath;
        }
    } else {
        $file = TEMPLATE_USER_PATH.$basepath.$href;
        if (file_exists($file)) {
            $mtime .= filemtime($file);
            $server = TEMPLATE_USER_URL.$basepath;
        }
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
    $properties = '';
    foreach($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }

    $resource = $server.DS.$href;
    $resource = preg_replace('@(?<!:)//@', '/', $resource);

    $output = "<link {$rel} {$type} href=\"{$resource}{$mtime}\" {$properties}>";

    return $output;
}
