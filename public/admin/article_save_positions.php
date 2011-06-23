<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new TemplateCacheManager(TEMPLATE_USER_PATH);

require_once(SITE_LIBS_PATH.'class.dir.php');

if(isset($_REQUEST['category'])) {
    $ccm = ContentCategoryManager::get_instance();
    $categoryID = ($_REQUEST['category'] == 'home')? 0 : $_REQUEST['category'];
    if($categoryID == 0){
        $category_name = 'home';
    }else{
        $category_name = $ccm->get_name($categoryID);
    }
    $tpl->delete($category_name . '|RSS');
    $tpl->delete($category_name . '|0');
}

require_once('application_events.php');

String_Utils::disabled_magic_quotes();

$isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');

$places = json_decode($_REQUEST['id'], true);

///**
// * Log this action
//*/
//$app->workflow->log( 'Cambiapos - ' . $_SESSION['username'] . ' ' . Application::getRealIP() .
//                     ' - QueryString: ' . $_REQUEST['id'] , PEAR_LOG_INFO );

$_frontpage = array();
$_positions = array();
$_suggested_home = array();
$_other_contents = array();

$contentTypeTranslationCache = array();
$content_positions = array();

$i=0;

foreach($places as $id => $params) {

    // This element isn't an article so store it in new content_position db table
    if($params['content_type'] != '1'
       && preg_match('@^placeholder@',$params['placeholder']) )
    {
        $content_positions[] = array(
                                    'id' => $id,
                                    'category' => $categoryID,
                                    'placeholder' => $params['placeholder'],
                                    'position' => $params['position'],
                                    'content_type' => $params['content_type'],
                                   );
        $i++;

    // This element is an article so use the old way of positioning
    } else {
        if( empty($params['placeholder']) || $params['placeholder'] == 'art' || $params['placeholder'] == 'div_no_home'){
            $_frontpage[$i] = array(0, $id);
            $_positions[$i] = array('100', '0', $id);
            $i++;
        } else {
             $_frontpage[$i] = array(1, $id);
             $_positions[$i] = array($params['position'], $params['placeholder'], $id);
             $i++;
        }
    }

}

// Save contents, the new way
$savedProperly = ContentManager::saveContentPositionsForHomePage($categoryID, $content_positions);

// Save contents, the old way
$article = new Article();
if( $_REQUEST['category']!='home' ){
    $article->set_frontpage($_frontpage, $_SESSION['userid']);
    $ok = $article->set_position($_positions, $_SESSION['userid']);
} else {
    $ok = $article->refresh_home($_suggested_home, $_positions,  $_SESSION['userid']);
}

$msg= "Change and Save positions -- Category: " . $category_name;
Application::write_log($msg);

// If this request is Ajax return properly formated result.
if( $isAjax ) {
    if( $ok == 1  && $savedProperly) {
        echo('<div class="success">Posiciones guardadas correctamente.</div>');
    } else {
        echo('<div class="success">Hubo errores al guardar las posiciones. Inténtelo de nuevos.</div>');
    }
}
