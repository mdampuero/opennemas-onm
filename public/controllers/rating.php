<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
// Start up and setup the app
require_once '../bootstrap.php';

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if ($action != 'rating') {
    Application::ajaxOut(_("Action not recognized!"));
}

$ip        = $_SERVER['REMOTE_ADDR'];
$ipFrom    = filter_input(INPUT_GET, 'i', FILTER_SANITIZE_STRING);
$voteValue = filter_input(INPUT_GET, 'v', FILTER_VALIDATE_INT);
$page      = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
$articleId = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_STRING);

if ($ip != $ipFrom) {
    Application::ajaxOut(_("Problem with IP verification!"));
}

//Comprobamos que exista el artículo que se quiere votar
$content = new Content($articleId);

if (is_null($content->id)) {
    Application::ajaxOut(_("Content not available"));
}

$rating = new Rating($content->id);
$update = $rating->update($voteValue, $ip);

if ($update) {
    $output = $rating->render($page, 'result', 1);
} else {
    $output = _("You have voted this new previously.");
}

Application::ajaxOut($output);
