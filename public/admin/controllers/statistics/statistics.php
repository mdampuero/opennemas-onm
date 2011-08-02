<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

require_once(SITE_LIBS_PATH.'ofc1/open-flash-chart.php');
require_once(SITE_LIBS_PATH.'ofc1/open_flash_chart_object.php');


// Assign a content types for don't reinvent the wheel into template
$tpl->assign('content_types', array(1 => 'Noticia' , 7 => 'Galeria', 9 => 'Video', 4 => 'Opinion', 3 => 'Fichero'));


if (!isset($_SESSION['desde'])) {$_SESSION['desde'] = 'index';}
if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = '0';}
if (!isset($_REQUEST['page'])) {$_REQUEST['page'] = '0';}

$tpl->assign('category', $_REQUEST['category']);

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $datos_cat);
$allcategorys = $parentCategories;

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {

        case 'index':
            $tpl->display('statistics/statistics.tpl');
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
                $title = "<h2>".sprintf(_("More seen in %s"), $tiempo)."</h2>";
                $items = Dashboard::getMostViewed('Article',$_REQUEST['category'],$_REQUEST['days']);
                String_Utils :: disabled_magic_quotes($items);
                $html_output = Dashboard::viewedTable($items, $title);

            } elseif ($_REQUEST['type']=='comented') {
                $title = "<h2>".sprintf(_("Most commented %s"), $tiempo)."</h2>";
                $items = Dashboard::getMostComented('Article',$_REQUEST['category'],$_REQUEST['days']);
                String_Utils :: disabled_magic_quotes($items);
                $html_output = Dashboard::comentedTable($items, $title);

            } elseif ($_REQUEST['type']=='voted') {
                $title = "<h2>".sprintf(_("Most voted %s"), $tiempo)."</h2>";
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
