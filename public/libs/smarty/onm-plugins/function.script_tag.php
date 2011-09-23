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
        $server = TEMPLATE_ADMIN_URL.'js';
        if ($smarty->theme == 'default') {
            $file = TEMPLATE_ADMIN_PATH.'/js'.$src;
            if (file_exists($file)) {
                $mtime .= filemtime($file);
            }
        } else {
            $file = TEMPLATE_USER_PATH.'/js'.$src;
            if (file_exists($file)) {
                $mtime .= filemtime($file);
            }
        }
    }
    
    //Comprobar si tiene type definido
    if (isset($params['type'])) {
        $type = "type=\"{$params['type']}\"";
    } else {
        $type = "type=\"text/javascript\"";
    }
    
    
    unset($params['external']);
    unset($params['src']);
    unset($params['type']);
    $properties = '';
    foreach($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }
    
    $output = "<script {$type} src=\"{$server}{$src}{$mtime}\" {$properties}></script>";
    
    return $output;
}
