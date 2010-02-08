<?php

//error_reporting(E_ALL);
require_once('config.inc.php');
require_once('core/application.class.php');

Application::import_libs('*');
$app = Application::load();

// redirect to /mobile/ if it's mobile device request
$app->mobileRouter();

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');
require_once('core/user.class.php');

require_once('core/kiosko.class.php');

$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('kiosko');

if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = 245;}
if (!isset($_REQUEST['action'])) {$_REQUEST['action'] = 'list';}
if (!isset($_REQUEST['month'])) {$_REQUEST['month'] = date('n');}
if (!isset($_REQUEST['year'])) {$_REQUEST['year'] = date('Y');}

$tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);
$tpl->assign('MEDIA_FILE_PATH_WEB', MEDIA_DIR.'/files/');

$tpl->assign('MONTH', $_REQUEST['month']);
$tpl->assign('YEAR', $_REQUEST['year']);


/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
$category_name = $_GET['category_name'] = 'kiosko';
$ccm = new ContentCategoryManager();
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'list': { //Opinion de un autor

                $ccm = new ContentCategoryManager();
                $allcategorys = $ccm->find('internal_category=4  AND fk_content_category=0', 'ORDER BY posmenu');

                foreach ($allcategorys as $category) {
                    $cm = new ContentManager();

                    $kiosko[] = array ('category' => $category->title,
                                       'portadas' => $cm->find_by_category('Kiosko', $category->pk_content_category,
                                                  ' `contents`.`available`=1 AND `contents`.`fk_content_type`=14 AND MONTH(`kioskos`.date)='.$_REQUEST['month'].' AND'.
                                                  ' YEAR(`kioskos`.date)='.$_REQUEST['year'].'', 'ORDER BY `kioskos`.date DESC '));
                }

                $ki = new Kiosko();
                $months_kiosko = $ki->get_months_by_years();

                $tpl->assign('kiosko', $kiosko);
                $tpl->assign('months_kiosko', $months_kiosko);

                // advertisement opinion NOCACHE
                require_once('index_advertisement.php');

        } break;


        default: {
            Application::forward301('index.php');
        } break;
    }

} else {
    Application::forward301('index.php');
}


// Show in Frontpage
$tpl->display('kiosko_index.tpl', $cache_id);
