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

        $tpl->assign('pages', $pages);


        $tpl->display('menues/list.tpl');


    break;

    case 'new':
        Acl::checkOrForward('MENU_CREATE');

        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0);
        $tpl->assign('categories', $parentCategories);

        $tpl->assign('pages', $pages);

        $tpl->display('menues/read.tpl');

    break;

    case 'read':

        Acl::checkOrForward('MENU_READ');

        $name = filter_input(INPUT_GET,'name',FILTER_SANITIZE_STRING );



        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0, $pages[$name]);

        $tpl->assign('categories', $parentCategories);
        $tpl->assign('pages', $pages);

        $menu = Menu::getMenu($name);

        $tpl->assign('menu', $menu);

        $tpl->display('menues/read.tpl');

    break;

    case 'create':

         Acl::checkOrForward('MENU_CREATE');

         $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);

         $_POST['params'] = serialize(array('description'=>$data['description']));

         $_POST['positions'] = json_decode($_POST['items'], true);

         $mn = new Menu();
         $menu = $mn->create($_POST);

         Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

    break;

    case 'update':

         Acl::checkOrForward('MENU_UPDATE');

         $_POST['params'] = serialize(array('description'=>$_POST['description']));

         //TODO:get site_name;
         $_POST['site'] = SITE;

         $mn = new Menu();
         $menu = $mn->update($_POST);

         Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

    break;

    default:
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
    break;

}
