<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$fk_author=$_REQUEST['fk_author'];
$aut = new Author($fk_author);
$photos = $aut->get_author_photos($fk_author);
echo "<ul id='thelist'  class='gallery_list'> ";

if($photos) {
    foreach ($photos as $as) {
        echo "<li><img src='".MEDIA_IMG_PATH_WEB.$as->path_img."' id='".$as->pk_img."'  border='1' /></li>";
    }
}

echo "</ul>";

