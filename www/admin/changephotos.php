<?php
//error_reporting(E_ALL);
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();


$tpl = new TemplateAdmin(TEMPLATE_ADMIN);


require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/opinion.class.php');
require_once('core/content_category.class.php');
require_once('core/user.class.php');
require_once('core/author.class.php');


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

