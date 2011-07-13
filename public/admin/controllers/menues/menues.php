<?php
  
/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

// Check if the user can frontpage menues admin
Acl::checkOrForward('MENUES_ADMIN');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', _('Section Manager'));

 /**
 * Setup Database access
*/
//$ccm = new ContentCategoryManager();
//$allcategorys = $ccm->find('internal_category != 0 AND fk_content_category =0', 'ORDER BY inmenu DESC, posmenu');

$ccm = ContentCategoryManager::get_instance();
 

//Frontpages ¿? add polls, kiosko, static_pages
$pages = array('frontpage'=>1, 'opinion'=>1, 'album'=>7, 'video'=>9, 'mobile'=>1, 'poll'=>11);


$action = filter_input(INPUT_POST,'action',FILTER_SANITIZE_STRING );
if (empty($action)) {
    $action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING,
                           array('options' => array('default'=>'list')));
}

switch($action) {

    case 'list':
        Acl::checkOrForward('MENU_LIST');
        $name = 'album';
        $tpl->assign('pages', $pages);
      

    break;
    case 'read':
        Acl::checkOrForward('MENU_READ');
        
        $name =   filter_input(INPUT_GET,'name',FILTER_SANITIZE_STRING );
 
        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0, $pages[$name]);
        $mn = new Menu();
        $menu = $mn->getMenu($name);
  
        $tpl->assign('categories', $parentCategories);
        $tpl->assign('menu', $menu);
        $tpl->assign('name', $name);
        $tpl->assign('pages', $pages);

    break;

    case 'save':

         Acl::checkOrForward('MENU_UPDATE');

         $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
         $_POST['categories'] = json_decode($_POST['positions'], true);   
      
         $mn = new Menu();
         $menu = $mn->setMenu($_POST);

        

         Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
 
    break;

    default:
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
    break;

}

$tpl->display('menues/menues.tpl');