<?php
//error_reporting(E_ALL);
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/article.class.php');
require_once('core/related_content.class.php');
require_once('core/attachment.class.php');
require_once('core/attach_content.class.php');
require_once('core/comment.class.php');
require_once('core/photo.class.php');
require_once('core/video.class.php');
require_once('core/opinion.class.php');
require_once('core/album.class.php');
require_once('core/search.class.php');
require_once('core/rating.class.php');
require_once('core/author.class.php');
require_once('core/privileges_check.class.php');

require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');

require_once('core/ofc.class.php');
require_once('core/dashboard.class.php');

require_once('libs/ofc1/open-flash-chart.php');
require_once('libs/ofc1/open_flash_chart_object.php');

require_once('utils_content.php');

// Assign a content types for don't reinvent the wheel into template
$tpl->assign('content_types', array(1 => 'Noticia' , 7 => 'Galeria', 9 => 'Video', 4 => 'Opinion', 3 => 'Fichero'));


if (!isset($_SESSION['desde'])) {$_SESSION['desde'] = 'index';}
if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = '0';}

$tpl->assign('category', $_REQUEST['category']);

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $datos_cat);
$allcategorys = $parentCategories;

$tpl->assign('titulo_barra', 'DashBoard');

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {

        case 'index':

        break;

        case 'get':

            $tiempo = "";
            if ($_REQUEST['days']<=3) {
                $tiempo = ($_REQUEST['days']*24)." Horas</h2>";
            } elseif ($_REQUEST['days']==7) {
                $tiempo = "1 semana";
            } elseif ($_REQUEST['days']==14) {
                $tiempo = "2 semana";
            } elseif ($_REQUEST['days']==30) {
                $tiempo = "1 mes";
            }

            if ($_REQUEST['type']=='viewed') {
                $title = "<h2>Más vistas ".$tiempo."</h2>";
                $items = Dashboard::getMostViewed('Article',$_REQUEST['category'],$_REQUEST['days']);
                String_Utils :: disabled_magic_quotes($items);
                $html_output = Dashboard::viewedTable($items, $title);

            } elseif ($_REQUEST['type']=='comented') {
                $title = "<h2>Más comentadas ".$tiempo."</h2>";
                $items = Dashboard::getMostComented('Article',$_REQUEST['category'],$_REQUEST['days']);
                String_Utils :: disabled_magic_quotes($items);
                $html_output = Dashboard::comentedTable($items, $title);
                
            } elseif ($_REQUEST['type']=='voted') {
                $title = "<h2>Más votadas ".$tiempo."</h2>";
                $items = Dashboard::getMostVoted('Article',$_REQUEST['category'],$_REQUEST['days']);
                String_Utils :: disabled_magic_quotes($items);
                $html_output = Dashboard::votedTable($items, $title);
            }

            Application::ajax_out("$html_output");
        break;

        default:
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=index&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;
    } //switch
} else {
    Application::forward($_SERVER['SCRIPT_NAME'].'?action=index&category='.$_REQUEST['category']);
}

$tpl->removeScript('wz_tooltip.js', 'body');

$tpl->display('dashboard.tpl');

