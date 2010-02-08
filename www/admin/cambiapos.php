<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

define('PATH_FILES', realpath(dirname(__FILE__).'/../media/files/'));
require_once "libs/class.dir.php";

require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/article.class.php');
require_once('core/attachment.class.php');
require_once('core/related_content.class.php');
require_once('core/attach_content.class.php');

/* Modo treadstone {{{ */
require_once('./core/template_cache_manager.class.php');
$tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);

if(isset($_REQUEST['category'])) {
    $ccm = ContentCategoryManager::get_instance();
    $category_name = $ccm->get_name($_REQUEST['category']);  
    $tplManager->delete($category_name . '|RSS');  
    $tplManager->delete($category_name . '|0');
}
/* }}} */

Application::import_libs('*');
$app = Application::load();

$app->workflow->log( 'Cambiapos - ' . $_SESSION['username'] . ' ' . Application::getRealIP() .
                     ' - QueryString: ' . $_SERVER['QUERY_STRING'], PEAR_LOG_INFO );

require_once('application_events.php');

$pares = $_GET['pares'];
$hole1 = $_GET['hole1'];
$hole2 = $_GET['hole2'];
$hole3 = $_GET['hole3'];
$hole4 = $_GET['hole4'];
$des = $_GET['des'];
$nopubli = $_GET['nopubli'];
$ok = 0;

$isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');

$_frontpage = array();
$_positions = array();
$_placeholder = array();
$_suggested_home = array();

if(isset($des)){
    $tok = strtok($des,",");
    $pos=1;
                       
    while (($tok !== false) AND ($tok !=" ")) {			   		   				   			   		 
        $_frontpage[] = array(1, $tok);
        $_positions[] = array($pos, 'placeholder_0_0', $tok);
        $_placeholder[] = array('placeholder_0_0');
        $tok = strtok(",");
        $pos+=1;
    } 
    $ok=1; 
}

if(isset($pares)){
    $tok = strtok($pares,",");
    $pos=2;
    while (($tok !== false) AND ($tok !=" ")) {			   		   				   			   		 
        $_frontpage[] = array(1, $tok);
        $_positions[] = array($pos, 'placeholder_0_1', $tok);
        $_placeholder[] = array('placeholder_0_1');
        $tok = strtok(",");
        $pos+=2;
    }
   
    if(!$isAjax) {
        echo $pos;
    }
    $ok=1;
}

if(isset($hole1)){
    $tok = strtok($hole1,",");
    $pos=3; 
    while (($tok !== false) AND ($tok !=" ")) {			   		   				   			   		          
            $_frontpage[] = array(1, $tok);
            $_positions[] = array($pos,'placeholder_1_0', $tok);
            $_placeholder[] = array('placeholder_1_0');
            $tok = strtok(","); 
            $pos+=2;
    }
    $ok=1;
  }

if(isset($hole2)){
    $tok = strtok($hole2,",");
    while (($tok !== false) AND ($tok !=" ")) {
            $_frontpage[] = array(1, $tok);
            $_positions[] = array($pos, 'placeholder_1_1', $tok);
            $_placeholder[] = array('placeholder_1_1');
            $tok = strtok(",");
            $pos+=2;
    }
    $ok=1;
  }
if(isset($hole3)){
    $tok = strtok($hole3,",");
    while (($tok !== false) AND ($tok !=" ")) {
            $_frontpage[] = array(1, $tok);
            $_positions[] = array($pos, 'placeholder_1_2', $tok);
            $_placeholder[] = array('placeholder_1_2');
            $tok = strtok(",");
            $pos+=2;
    }
    $ok=1;
  }
  if(isset($hole4)){
    $tok = strtok($hole4,",");
    while (($tok !== false) AND ($tok !=" ")) {
            $_frontpage[] = array(1, $tok);
            $_positions[] = array($pos, 'placeholder_1_3', $tok);
            $_placeholder[] = array('placeholder_1_3');
            $tok = strtok(",");
            $pos+=2;
    }
    $ok=1;
  }
  

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

$article = new Article();
 if( $ok == 1 ) {
    if( $_GET['category']!='home' ){
        $article->set_frontpage($_frontpage, $_SESSION['userid']);
        $article->set_position($_positions, $_SESSION['userid']);
    } else {     
      //  $article->set_home_position($_positions, $_SESSION['userid']);
        //Sugeridas        
      //  $article->set_inhome($_suggested_home, $_SESSION['userid']);
        $article->refresh_home($_suggested_home, $_positions,  $_SESSION['userid']);
    }
 }
// Mostrar mensaxes si a petición ver por Ajax
if( $isAjax ) {
    if( $ok == 1 ) {        
        echo('Posiciones guardadas correctamente.');
    } else {
        echo('Hubo errores al guardar las posiciones. Inténtelo de nuevo.');
    }
}