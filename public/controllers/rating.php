<?php
/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

$action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING);
if($action != 'rating') {
    Application::ajax_out(_("Action not recognized!"));
}


$ip = $_SERVER['REMOTE_ADDR'];
$ip_from = filter_input(INPUT_GET,'i',FILTER_SANITIZE_STRING);
$vote_value = filter_input(INPUT_GET,'v',FILTER_VALIDATE_INT);
$page = filter_input(INPUT_GET,'p',FILTER_SANITIZE_STRING);
$article_id = filter_input(INPUT_GET,'a',FILTER_SANITIZE_STRING);

if($ip != $ip_from) {
    Application::ajax_out(_("Problem with IP verification!"));
}

//Comprobamos que exista el artículo que se quiere votar
$content = new Content($article_id);

if(is_null($content->id)) {
    Application::ajax_out(_("Content not available"));
}

$rating = new Rating($content->id);
$update = $rating->update($vote_value,$ip);

if($update) {
    $html_out = $rating->render($page,'result',1);
} else {
    $html_out = _("You have voted this new previously.")    ;
}

Application::ajax_out($html_out);
