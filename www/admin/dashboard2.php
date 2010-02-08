<?php
error_reporting(E_ALL);
require_once('config.inc.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Dashboard del sistema');
require_once('core/article.class.php');
require_once('core/content.class.php');
require_once('core/content_manager.class.php');
require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');

if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = 1;}
$tpl->assign('category', $_REQUEST['category']);

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'list':
            $cm = new ContentManager();
            //$ccm = new ContentCategoryManager();
            $viewed = $cm->find_by_category('Article',$_GET['category'], 'content_status=1 AND frontpage=1','ORDER BY views DESC, archive DESC LIMIT 0,1');
            $tpl->assign('viewed', $viewed);

            //$categorys = $ccm->find('fk_content_category = 0', 'ORDER BY posmenu');
            // FIXME: Set pagination
            //$tpl->assign('categorys', $categorys);
        break;

        case 'save':
            $c = new Configuration();
            $c->set_items( $_REQUEST );
            $c->save();
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        break;
        default:
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        break;
    }
} else {
    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
}

$tpl->display('dashboard.tpl');

