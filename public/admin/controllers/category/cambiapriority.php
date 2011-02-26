<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

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
    $output = Content::refreshFrontpageForAllCategories();

}

if( !$ok==1){
    header('HTTP/1.1 500 Internal Server Error');
} else {
    echo sprintf(_("Changes in category order saved correctly:\n %s"), $output);
}