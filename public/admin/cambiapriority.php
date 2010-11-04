<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

define('PATH_FILES', realpath(dirname(__FILE__).'/../media/files/'));
require_once(SITE_LIBS_PATH.'class.dir.php');

$orden=$_GET['orden']; 
$ok=0;

if(isset($orden)){
    $tok = strtok($orden,",");
    $pos=1;
    while (($tok !== false) AND ($tok !=" ")) {
        $category = new ContentCategory($tok);
        $category->set_priority($pos);
        $tok = strtok(",");
        $pos+=1;
    }
    $ok=1;

}

if( $ok==1){
    echo '<script>
    alert("Guardado correctamente");
    </script>';
}