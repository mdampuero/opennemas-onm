<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

define('PATH_FILES', realpath(dirname(__FILE__).'/../media/files/'));
require_once(SITE_LIBS_PATH.'class.dir.php');

//variable GET

$orden=$_GET['orden'];
$pares=$_GET['pares'];
$impares=$_GET['impares'];

$ok = 0;

if(isset($orden)){
    $tok = strtok($orden,",");	    
    $pos=1;
    while (($tok !== false) AND ($tok !=" ")) {			   		   				   			   		 
        $article = new Article($tok);
        $article->set_frontpage('1');
        $article->set_position($pos); 
        $tok = strtok(",");
        $pos+=1;
    }
    $ok=1;
}

if(isset($pares)){
    $tok = strtok($pares,",");
    $pos=2;			    
    while (($tok !== false) AND ($tok !=" ")) {			   		   				   			   		 
        $article = new Article($tok);	
        $article->set_frontpage('1');     		    
        $article->set_position($pos);		   
        $tok = strtok(",");
        $pos+=2;
    }
    $ok=1;
}

if(isset($impares)){
    $tok = strtok($impares,",");
    $pos=3;			    
    while (($tok !== false) AND ($tok !=" ")) {			   		   				   			   		 
        $article = new Article($tok);					
        $article->set_frontpage('1');	   
        $article->set_position($pos);  	
        $tok = strtok(",");
        $pos+=2;
    }
    $ok=1;
}

if( $ok==1){
    echo '<script> alert("Guardado correctamente");</script>';
}
?>