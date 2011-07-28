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
        $menues = Menu::listMenues();

        $tpl->assign('menues', $menues);


        $tpl->display('menues/list.tpl');


    break;

    case 'new':
        Acl::checkOrForward('MENU_CREATE');

        $name = filter_input(INPUT_GET,'name',FILTER_SANITIZE_STRING );

        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0);
        $albumCategories = array();
        $videoCategories = array();
        foreach($ccm->categories as $category) {
            if($category->internal_category == $pages['album']) {
                $albumCategories[] = $category;
            } else if($category->internal_category == $pages['video']) {
                $videoCategories[] = $category;
            }
        }
        $cm = new ContentManager();
        $staticPages = $cm->find('Static_Page', '1=1', 'ORDER BY created DESC ');
        $menues = Menu::listMenues();

        $tpl->assign(array( 'categories'=> $parentCategories,
                            'subcat'=> $subcat,
                            'albumCategories'=>$albumCategories,
                            'videoCategories'=>$videoCategories,
                            'staticPages'=> $staticPages,
                            'menues'=> $menues,
                            'pages'=> $pages ));

        $tpl->display('menues/read.tpl');

    break;

    case 'read':

        Acl::checkOrForward('MENU_READ');

        $name = filter_input(INPUT_GET,'name',FILTER_SANITIZE_STRING );

        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0);
        $albumCategories = array();
        $videoCategories = array();
        foreach($ccm->categories as $category) {
            if($category->internal_category == $pages['album']) {
                $albumCategories[] = $category;
            } else if($category->internal_category == $pages['video']) {
                $videoCategories[] = $category;
            }
        }
        $cm = new ContentManager();
        $staticPages = $cm->find('Static_Page', '1=1', 'ORDER BY created DESC ');
        $menues = Menu::listMenues();

        $tpl->assign(array( 'categories'=> $parentCategories,
                            'subcat'=> $subcat,
                            'albumCategories'=>$albumCategories,
                            'videoCategories'=>$videoCategories,
                            'staticPages'=> $staticPages,
                            'menues'=> $menues,
                            'pages'=> $pages ));

        $menu = Menu::getMenu($name);

        $tpl->assign('menu', $menu);

        $tpl->display('menues/read.tpl');

    break;

    case 'validate':
            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);

            $_POST['params'] = serialize(array('description'=>$_POST['description']));
            $_POST['site'] = SITE;


            if(empty($id)) {
                Acl::checkOrForward('MENU_CREATE');

                $mn = new Menu();                
                $mn->create($_POST);

            } else {
                Acl::checkOrForward('MENU_UPDATE');

                $mn = new Menu($id);
                $menu = $mn->update($_POST);

            }

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=read&name=' . $_POST['name']);
        break;


    case 'create':

         Acl::checkOrForward('MENU_CREATE');

         $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);

         $_POST['params'] = serialize(array('description'=>$_POST['description']));
         $_POST['site'] = SITE;

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
