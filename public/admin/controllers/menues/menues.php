<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

// Check if the user can frontpage menues admin
//Acl::checkOrForward('MENUES_ADMIN');

use Onm\Settings as s,
    Onm\Message as m;
/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

$ccm = ContentCategoryManager::get_instance();

//Modules => change & check if ismodule activated
$pages = array('frontpage'=>1, 'opinion'=>4, 'album'=>7, 'video'=>9, 'mobile'=>3,
    'poll'=>11, 'letter'=>17, 'kiosko'=> 14,'boletin'=>13 //,'especiales'=10,'agenda'=16
    );

$action = filter_input(INPUT_POST,'action',FILTER_SANITIZE_STRING );
if (empty($action)) {
    $action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING,
                           array('options' => array('default'=>'list')));
}

switch($action) {

    case 'list':

        Acl::checkOrForward('MENU_ADMIN');

        $tpl->assign('pages', $pages);
        $menues = Menu::listMenues();
        $subMenues = array();
        $list = array();
        $subList = array();

        foreach($menues as $menu) {
            if(empty($menu->pk_father)) {
                $list[] = $menu;
            }else{
               $subMenues[] = $menu;
            }
        }

        $withoutFather = array();
        foreach($subMenues as $submenu){
            //TODO: mejorar, buscamos su menu padre para pintarlo ya que solo sabemos el item
            $without = true;
            foreach($list as $menu) {
                foreach($menu->items as $item){
                    if($item->pk_item == $submenu->pk_father) {
                        $subList[$item->pk_menu][] = $submenu;
                        $without = false;
                    }
                }

            }
            if(($submenu->pk_father !=0) && $without) {
                $withoutFather[] = $submenu;
            }
        }

        $tpl->assign( array('menues'=>$list,
            'subMenues'=>$subList,
            'withoutFather'=>$withoutFather) );


        $tpl->display('menues/list.tpl');


    break;

    case 'new':
        Acl::checkOrForward('MENU_CREATE');

        $name = filter_input(INPUT_GET,'name',FILTER_SANITIZE_STRING );

        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0);
        $albumCategories = array();
        $videoCategories = array();
        $pollCategories = array();
        foreach($ccm->categories as $category) {
            if($category->internal_category == $pages['album']) {
                $albumCategories[] = $category;
            } else if($category->internal_category == $pages['video']) {
                $videoCategories[] = $category;
            } else if($category->internal_category == $pages['poll']) {
                $pollCategories[] = $category;
            }
        }
        $cm = new ContentManager();
        $staticPages = $cm->find('StaticPage', '1=1', 'ORDER BY created DESC ');
        $menues = Menu::listMenues();

        if ($syncParams = s::get('sync_params')) {
            // Fetch all elements from settings
            $allSites = array();
            foreach ($syncParams as $siteUrl => $categories) {
                $allSites[] = array ($siteUrl => $categories);
            }

            $tpl->assign('elements', $allSites);

        }


        $tpl->assign(array( 'categories'=> $parentCategories,
                            'subcat'=> $subcat,
                            'albumCategories'=>$albumCategories,
                            'videoCategories'=>$videoCategories,
                            'pollCategories'=>$pollCategories,
                            'staticPages'=> $staticPages,
                            'menues'=> $menues,
                            'pages'=> $pages ));

        $tpl->display('menues/edit.tpl');

    break;

    case 'read':

        Acl::checkOrForward('MENU_AVAILABLE');

        $name = filter_input(INPUT_GET,'name',FILTER_SANITIZE_STRING );

        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0);
        $albumCategories = array();
        $videoCategories = array();
        $pollCategories = array();
        foreach($ccm->categories as $category) {
            if($category->internal_category == $pages['album']) {
                $albumCategories[] = $category;
            } else if($category->internal_category == $pages['video']) {
                $videoCategories[] = $category;
            } else if($category->internal_category == $pages['poll']) {
                $pollCategories[] = $category;
            }
        }
        $cm = new ContentManager();
        $staticPages = $cm->find('StaticPage', '1=1', 'ORDER BY created DESC ');
        $menues = Menu::listMenues();

        if ($syncParams = s::get('sync_params')) {
            // Fetch all elements from settings
            $allSites = array();
            foreach ($syncParams as $siteUrl => $categories) {
                $allSites[] = array ($siteUrl => $categories);
            }

            $tpl->assign('elements', $allSites);

        }

        $tpl->assign(array( 'categories'=> $parentCategories,
                            'subcat'=> $subcat,
                            'albumCategories'=>$albumCategories,
                            'videoCategories'=>$videoCategories,
                            'pollCategories'=> $pollCategories,
                            'staticPages'=> $staticPages,
                            'menues'=> $menues,
                            'pages'=> $pages ));

        $menu = Menu::getMenu($name);

        $tpl->assign('menu', $menu);

        $tpl->display('menues/edit.tpl');

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

         $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);

         Acl::checkOrForward('MENU_UPDATE');

         $_POST['params'] = serialize(array('description'=>$_POST['description']));
         //TODO:get site_name;
         $_POST['site'] = SITE;

         $mn = new Menu($id);
         $menu = $mn->update($_POST);

         Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

    break;

    case 'delete':

         $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);

         Acl::checkOrForward('MENU_DELETE');

         $mn = new Menu($id);
         $menu = $mn->delete($id);
         MenuItems::emptyMenu($id);

         Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

    break;

    case 'batchDelete':
        Acl::checkOrForward('MENU_DELETE');
        if (isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0) {
            $fields = $_REQUEST['selected_fld'];
            if (is_array($fields)) {
                foreach ($fields as $id ) {
                    $mn = new Menu($id);
                    if($mn->type == 'user') {
                        $mn = $mn->delete($id);
                        MenuItems::emptyMenu($id);
                    } else {
                        m::add( "You can't delete menu %{$mn->name}% " );
                    }
               }

            }
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

    break;

    default:
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
    break;

}
