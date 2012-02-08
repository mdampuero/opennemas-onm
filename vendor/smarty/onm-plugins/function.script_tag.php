<?php
/*
 * -------------------------------------------------------------
 * File:     	function.script_tag.php
 * Comprueba el tipo y escribe el nombre o la imag
 */

function smarty_function_script_tag($params, &$smarty) {

    $output = "";

    if (empty($params['src'])) {
        trigger_error("[plugin] script_tag parameter 'src' cannot be empty",E_USER_NOTICE);
        return;
    }

    $src = $params['src'];

    //Comprobar si es un link externo
    if (array_key_exists('external', $params)) {
        $server = '';
    } else {
        //Si no es externno, calculamos el mtime del fichero
        $mtime = '?';
        $server = '';
        $basepath = $params["basepath"] ?: "/js";
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


    unset($params['external']);
    unset($params['src']);
    unset($params['type']);
    unset($params['escape']);
    unset($params['basepath']);
    $properties = '';
    foreach($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }
    //Comprobar si es external
    if ($server == '') {
        $resource = $src;
    } else {
        $resource = $server.DS.$src;
    }

    $resource = preg_replace('@(?<!:)//@', '/', $resource);

    $output = "<script {$type} src=\"{$resource}{$mtime}\" {$properties} ></script>";

    if ($escape) {
        $output = str_replace('/>', '\/>', $output);
    }

    return $output;
}
