<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the menus
 *
 * @package Backend_Controllers
 **/
class MenusController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->pages = array(
            'frontpage' => 1,
            'opinion'   => 4,
            'album'     => 7,
            'video'     => 9,
            'mobile'    => 3,
            'poll'      => 11,
            'letter'    => 17,
            'kiosko'    => 14,
            'boletin'   => 13,
        );
    }

    /**
     * Description of the action
     *
     * @param  Request $request the resquest object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $this->checkAclOrForward('MENU_ADMIN');

        $menues    = \Menu::listMenues();
        $subMenues = array();
        $list      = array();
        $subList   = array();

        foreach ($menues as $menu) {
            if (empty($menu->pk_father)) {
                $list[] = $menu;
            } else {
               $subMenues[] = $menu;
            }
        }

        $withoutFather = array();
        foreach ($subMenues as $submenu){
            //TODO: mejorar, buscamos su menu padre para pintarlo ya que solo sabemos el item
            $without = true;
            foreach ($list as $menu) {
                foreach ($menu->items as $item){
                    if ($item->pk_item == $submenu->pk_father) {
                        $subList[$item->pk_menu][] = $submenu;
                        $without = false;
                    }
                }

            }
            if ($submenu->pk_father != 0 && $without) {
                $withoutFather[] = $submenu;
            }
        }

        return $this->render('menues/list.tpl', array(
            'menues'        => $list,
            'subMenues'     => $subList,
            'withoutFather' => $withoutFather,
            'pages'         => $this->pages,
        ));
    }

    /**
     * Shows the menu information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('MENU_AVAILABLE');

        $name = $request->query->filter('id', null,FILTER_SANITIZE_STRING);

        $ccm = \ContentCategoryManager::get_instance();
        $cm = new \ContentManager();

        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0);
        $albumCategories = $videoCategories = $pollCategories = array();
        foreach ($ccm->categories as $category) {
            if ($category->internal_category == $this->pages['album']) {
                $albumCategories[] = $category;
            } elseif ($category->internal_category == $this->pages['video']) {
                $videoCategories[] = $category;
            } elseif ($category->internal_category == $this->pages['poll']) {
                $pollCategories[] = $category;
            }
        }
        $staticPages = $cm->find('StaticPage', '1=1', 'ORDER BY created DESC ');
        $menues = \Menu::listMenues();

        // Get Sync categories from settings
        if ($syncParams = s::get('sync_params')) {
            $colorSites = s::get('sync_colors');
            $allSites = array();
            foreach ($syncParams as $siteUrl => $categories) {
                $allSites[] = array ($siteUrl => $categories);

                if (array_key_exists($siteUrl, $colorSites)) {
                    $colors[$siteUrl] = $colorSites[$siteUrl];
                }
            }

            $tpl->assign('elements', $allSites);
            $tpl->assign('colors', $colors);
        }

        // Get categories from menu
        $menu = \Menu::getMenu($name);

        // Overload sync category color if exists
        if ($syncParams) {
            foreach ($menu->items as &$item) {
                foreach ($syncParams as $siteUrl => $categories) {
                    foreach ($categories as $category) {
                        if ($item->type == 'syncCategory' && $item->link == $category) {
                            $item->color = $colors[$siteUrl];
                        }
                    }
                }
            }
        }

        return $this->render('menues/edit.tpl', array(
            'categories'      => $parentCategories,
            'subcat'          => $subcat,
            'albumCategories' => $albumCategories,
            'videoCategories' => $videoCategories,
            'pollCategories'  => $pollCategories,
            'staticPages'     => $staticPages,
            'menues'          => $menues,
            'pages'           => $this->pages,
            'menu'            => $menu
        ));
    }

} // END class MenusController