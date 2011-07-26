<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');
 
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
    echo sprintf(_("Changes in menu category order saved correctly:\n %s"), $output);
}