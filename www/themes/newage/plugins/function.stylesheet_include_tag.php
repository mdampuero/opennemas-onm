<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {stylesheet_include_tag} function plugin
 *
 * Type:     function<br>
 * Name:     stylesheet_include_tag<br>
 * Date:     Jan 28, 2003<br>
 * Purpose:  format HTML tags for the stylesheet with support for assets server<br>
 * Input:<br>
 *         - file = file (and path) of image (required)
 *         - basedir = base directory for absolute paths, default
 *                     is environment variable DOCUMENT_ROOT
 *         - path_prefix = prefix for path output (optional, default empty)
 *         - path_prefix = prefix for path output (optional, default empty)
 *
 * Examples: {html_image file="/images/masthead.gif"}
 * Output:   <img src="/images/masthead.gif" width=400 height=23>
 * @link http://smarty.php.net/manual/en/language.function.html.image.php {html_image}
 *      (Smarty online manual)
 * @author   Fran Diéguez <fran at openhost dot es>
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_stylesheet_include_tag($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    require_once $smarty->_get_plugin_filepath('function','stylesheet_include_tag');

    if(empty($params['file'])){
        $smarty->trigger_error("stylesheet_include_tag: missing 'file' parameter", E_USER_NOTICE);
        return;
    } else { 
      $files = explode(',',$params['file']);
      if(count($files)>1){
	$val_return = ''; 
        foreach($files as $_stylefile) {
	  $params_copy = $params;
	  $params_copy['file'] = $_stylefile;
	  $val_return .= smarty_function_stylesheet_include_tag($params_copy,$smarty)."\n";
        }
        return $val_return;
      }
    }

    $type = 'text/css';
    $file = '';
    $rel = 'stylesheet';
    $extra = '';
    $prefix = '';
    $suffix = '';
    $cacheburstsignature = '1234';
    $cacheburst = true;
    $path_prefix = ''; 
    $media = 'all';
    $server_vars = ($smarty->request_use_auto_globals) ? $_SERVER : $GLOBALS['HTTP_SERVER_VARS'];
    $basedir = isset($server_vars['DOCUMENT_ROOT']) ? $server_vars['DOCUMENT_ROOT'] : '';
    $baseurl = '';
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'file':
            case 'path_prefix':
            case 'basedir':
            case 'media':
            case 'cacheburst':
            case 'baseurl':
                $$_key = $_val;
                break;
            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    $smarty->trigger_error("stylesheet_include_tag: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }   

    if($cacheburst !== false) {
        $cacheburstsignature = '?' . @filemtime($_SERVER['DOCUMENT_ROOT'] . $params['baseurl'] . $file);
        if($cacheburstsignature == '?'){$cacheburstsignature .= '1234';};
    } 

    if(!empty($baseurl)) {
        $file = $baseurl . $file;
    }

    if (substr($file,0,1) == '/') {
        $_link_path = $basedir . $file;
    } else {
        $_link_path = $file;
    }
    
    if(defined('ASSET_HOST')){
       $preg_test = ASSET_HOST;
       if(preg_match('/%02d/', $preg_test)){
          if(!defined('NUM_ASSET_HOSTS')) {
		define(NUM_ASSET_HOSTS,4);
          }
          $asset_server = sprintf(ASSET_HOST, rand(1, NUM_ASSET_HOSTS));

       } else {
          $asset_server = ASSET_HOST;
       }
       $protocol = (!empty($_SERVER['HTTPS']))? 'https://': 'http://';
       $asset_server = $protocol . $asset_server;
    } else{ $asset_server = ''; }

    
    return ('<link rel="'.$rel.'" type="'.$type.'" media="'.$media.'" href="'.$asset_server.$path_prefix.$file.$cacheburstsignature.'" '.$extra.' />');

}
/* vim: set expandtab: */

