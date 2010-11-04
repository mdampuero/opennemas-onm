<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

define('PATH_FILES', realpath(dirname(__FILE__).'/../media/files/'));
require_once(SITE_LIBS_PATH.'class.dir.php');


/* Modo treadstone {{{ */
/**
 * Setup view
*/
$tpl = new TemplateCacheManager(TEMPLATE_USER_PATH);

if(isset($_REQUEST['category'])) {
    $ccm = ContentCategoryManager::get_instance();
    $category_name = $ccm->get_name($_REQUEST['category']);  
    $tpl->delete($category_name . '|RSS');  
    $tpl->delete($category_name . '|0');
}
/* }}} */

Application::import_libs('*');
$app = Application::load();

$app->workflow->log( 'Cambiapos - ' . $_SESSION['username'] . ' ' . Application::getRealIP() .
                     ' - QueryString: ' . $_SERVER['QUERY_STRING'], PEAR_LOG_INFO );

require_once('application_events.php');


require(SITE_CORE_PATH.'string_utils.class.php');

// Merda de magic_quotes
String_Utils::disabled_magic_quotes();

$isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');

$places = json_decode($_REQUEST['id'], true);

$_frontpage = array();
$_positions = array();
$_suggested_home = array();

$pos=1;
$i=0;
foreach($places as $id => $placeholder) {
 
    if( empty($placeholder) || $placeholder == 'art' || $placeholder == 'div_no_home'){
         $_frontpage[$i] = array(0, $id);
         $_positions[$i] = array('100', '0', $id);
         $i++;
    }else{

          $_frontpage[$i] = array(1, $id);
          $_positions[$i] = array($pos, $placeholder, $id);
          $pos++;
          $i++;
    }
}

 
/*
if( $_GET['category']!='home' ){
    
    if(isset($nopubli)){
        $tok = strtok($nopubli,",");	    
        $pos=44;
        while (($tok !== false) AND ($tok !=" ")) {			   		   				   			   		            
            $_frontpage[] = array(0, $tok);
            $_positions[] = array($pos, 'placeholder_0_1', $tok); //un placeholder por defecto
            $tok = strtok(",");
            $pos+=1;
        }
        
        $ok=1;
    }
} else {
     if(isset($nopubli)){
        $tok = strtok($nopubli,",");
        while (($tok !== false) AND ($tok !=" ")) {
            $_suggested_home[] = array(2, $tok);
            $tok = strtok(",");
        }

        $ok=1;
    }
}
*/
$article = new Article();

    if( $_REQUEST['category']!='home' ){
        $article->set_frontpage($_frontpage, $_SESSION['userid']);
        $ok = $article->set_position($_positions, $_SESSION['userid']);
    } else {
         
         $ok = $article->refresh_home($_suggested_home, $_positions,  $_SESSION['userid']);
    }


// Mostrar mensaxes si a petición ver por Ajax
if( $isAjax ) {
    if( $ok == 1 ) {        
        echo('Posiciones guardadas correctamente.');
    } else {
        echo('Hubo errores al guardar las posiciones. Inténtelo de nuevos.');
    }
}