<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new TemplateCacheManager(TEMPLATE_USER_PATH);

$category = filter_input ( INPUT_POST, 'category' , FILTER_DEFAULT );

if(isset($category) && !empty($category)) {
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

require_once('../../application_events.php');

String_Utils::disabled_magic_quotes();

$isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');


/**
 * Get the JSON-encoded places from request
*/
$placesJSON = filter_input ( INPUT_POST, 'id' , FILTER_DEFAULT );
$places = json_decode($placesJSON, true);

if(!is_null($places)) {
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
    if( $_POST['category']!='home' ){
        $article->set_frontpage($_frontpage, $_SESSION['userid']);
        $ok = $article->set_position($_positions, $_SESSION['userid']);
    } else {
        $ok = $article->refresh_home($_suggested_home, $_positions,  $_SESSION['userid']);
    }
}

$msg= "Change and save positions -- Category: {$category_name} --\n por request: ". $placesJSON . " \n content_positions a json: ". json_encode($content_positions,true) . "-- \n ";
Application::write_log($msg);

// If this request is Ajax return properly formated result.
if( $isAjax ) {
    if( $ok == 1 && $savedProperly) {
        echo "<div class='success'>"._('Positions saved successfully.')."</div>";
    } elseif(is_null($places)) {
        echo "<div class='error'>"._('There was an error with the data sent from the client.')."</div>";
    } else {
        echo "<div class='error'>". _('There was an error while saving the content positions. Please, try it again.') ."</div>";
    }
}
