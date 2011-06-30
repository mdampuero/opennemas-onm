<?php
require('./config.inc.php');
require_once('./core/application.class.php');
require_once('./core/content.class.php');
require_once('./core/rating.class.php');

Application::import_libs('*');
$app = Application::load();



$action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING);
if($action != 'rating') {
    Application::ajax_out("Error Action!");
}


$ip = $_SERVER['REMOTE_ADDR'];
$ip_from = filter_input(INPUT_GET,'i',FILTER_SANITIZE_STRING);
$vote_value = filter_input(INPUT_GET,'v',FILTER_VALIDATE_INT);
$page = filter_input(INPUT_GET,'p',FILTER_SANITIZE_STRING);
$article_id = filter_input(INPUT_GET,'a',FILTER_SANITIZE_STRING);

if($ip != $ip_from) {
    Application::ajax_out("Error IP!");
}

//Comprobamos que exista el artículo que se quiere votar
$content = new Content($article_id);
 
if(is_null($content->id)) {
    Application::ajax_out("Error content!");
}

$rating = new Rating($content->id);
$update = $rating->update($vote_value,$ip);

if($update) {
    $html_out = $rating->render($page,'result',1);
} else {
    $html_out = "Ya ha votado anteriormente esta noticia.";
}

Application::ajax_out($html_out);

