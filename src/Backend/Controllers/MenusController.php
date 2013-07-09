<?php
/**
 * Handles the actions for the menus
 *
 * @package Backend_Controllers
 **/
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
        \Onm\Module\ModuleManager::checkActivatedOrForward('MENU_MANAGER');

        $this->checkAclOrForward('MENU_ADMIN');

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

        $this->menuPositions = array('' => _('Without position'));

        $this->menuPositions = array_merge(
            $this->menuPositions,
            $this->container->getParameter('instance')->theme->getMenus()
        );
        $this->view->assign('menu_positions', $this->menuPositions);

    }

    /**
     * Lists all the available menus
     *
     * @param Request $request the resquest object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $menues = \Menu::find();

        return $this->render('menues/list.tpl', array('menues' => $menues,));
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
        $this->checkAclOrForward('MENU_UPDATE');

        $id = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

        $ccm = \ContentCategoryManager::get_instance();
        $cm = new \ContentManager();

        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0);
        foreach ($subcat as $subcategory) {
            $parentCategories = array_merge($parentCategories, $subcategory);
        }

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

        // Get categories from menu
        $menu = new \Menu($id);
        $menu->loadItems();

        // Overload sync category color if exists
        if ($syncParams = s::get('sync_params')) {
            $colorSites = s::get('sync_colors', array());
            $allSites = $colors = array();
            foreach ($syncParams as $siteUrl => $categories) {
                $allSites[] = array ($siteUrl => $categories);

                if (array_key_exists($siteUrl, $colorSites)) {
                    $colors[$siteUrl] = $colorSites[$siteUrl];
                }
            }

            $this->view->assign('elements', $allSites);
            $this->view->assign('colors', $colors);

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

        return $this->render(
            'menues/new.tpl',
            array(
                'categories'      => $parentCategories,
                'albumCategories' => $albumCategories,
                'videoCategories' => $videoCategories,
                'pollCategories'  => $pollCategories,
                'staticPages'     => $staticPages,
                'pages'           => $this->pages,
                'menu'            => $menu,
                'menu_positions'  => $this->menuPositions,
            )
        );
    }

    /**
     * Handles the creating of new menus
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('MENU_CREATE');

        if ('POST' == $request->getMethod()) {
            $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
            $data = array(
                'name'      => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
                'params'    => serialize(
                    array(
                        'description' => $request->request->filter('description', null, FILTER_SANITIZE_STRING)
                    )
                ),
                'site'      => SITE,
                'pk_father' => $request->request->filter('pk_father', 'user', FILTER_SANITIZE_STRING),
                'items'     => json_decode($request->request->get('items')),
                'position'  => $request->request->filter('position', '', FILTER_SANITIZE_STRING),
            );

            $menu = new \Menu();
            if ($menu->create($data)) {
                m::add(sprintf(_("Menu '%s' created successfully."), $data['name']), m::SUCCESS);
            } else {
                m::add(sprintf(_("Unable to create the menu")), m::SUCCESS);
            }

            if ($continue) {
                return $this->redirect(
                    $this->generateUrl(
                        'admin_menu_show',
                        array('id' => $menu->pk_menu)
                    )
                );
            } else {
                return $this->redirect($this->generateUrl('admin_menus'));
            }

        } else {

            $cm  = new \ContentManager();
            $ccm = \ContentCategoryManager::get_instance();

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
            $menues = \Menu::find();

            if ($syncParams = s::get('sync_params')) {
                // Fetch all elements from settings
                $colorSites = s::get('sync_colors');
                $allSites = array();
                foreach ($syncParams as $siteUrl => $categories) {
                    $allSites[] = array ($siteUrl => $categories);
                    if (array_key_exists($siteUrl, $colorSites)) {
                        $colors[$siteUrl] = $colorSites[$siteUrl];
                    }
                }

                $this->view->assign('elements', $allSites);
                $this->view->assign('colors', $colors);
            }

            return $this->render(
                'menues/new.tpl',
                array(
                    'categories'      => $parentCategories,
                    'subcat'          => $subcat,
                    'albumCategories' => $albumCategories,
                    'videoCategories' => $videoCategories,
                    'pollCategories'  => $pollCategories,
                    'staticPages'     => $staticPages,
                    'menues'          => $menues,
                    'pages'           => $this->pages,
                    'menu_positions'  => $this->menuPositions,
                )
            );
        }
    }

    /**
     * Updates the menu information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('MENU_UPDATE');

        $id = $this->request->query->getDigits('id');
        $continue = $this->request->request->filter('continue', false, FILTER_SANITIZE_STRING);

        $menu = new \Menu($id);

        if ($menu->pk_menu == null) {
            m::add(sprintf(_('Unable to find a menu with the id "%s"'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_menus'));
        } else {
            // Check empty data
            if (count($request->request) < 1) {
                m::add(_("Menu data sent not valid."), m::ERROR);

                return $this->redirect($this->generateUrl('admin_menu_show', array('id' => $id)));
            }

            $data = array(
                'name'      => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
                'params'    => serialize(
                    array(
                        'description' => $request->request->filter('description', null, FILTER_SANITIZE_STRING)
                    )
                ),
                'site'      => SITE,
                'pk_father' => $request->request->filter('pk_father', 'user', FILTER_SANITIZE_STRING),
                'items'     => json_decode($request->request->get('items')),
                'position'  => $request->request->filter('position', '', FILTER_SANITIZE_STRING),
            );

            if ($menu->update($data)) {
                m::add(_("Menu updated successfully."), m::SUCCESS);
            } else {
                m::add(_("There was an error while updating the menu."), m::ERROR);
            }

            if ($continue) {
                return $this->redirect(
                    $this->generateUrl(
                        'admin_menu_show',
                        array('id' => $menu->pk_menu)
                    )
                );
            } else {
                return $this->redirect($this->generateUrl('admin_menus'));
            }
        }
    }

    /**
     * Deletes a menu given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('MENU_DELETE');
        $id = $request->query->getDigits('id');

        if (empty($id)) {
            m::add(_('You must give an id for delete the menu.'), m::ERROR);
        } else {
            $menu = new \Menu($id);
            $menu->delete($_SESSION['userid']);
            \MenuItems::emptyMenu($id);

            m::add(sprintf(_("Menu '%s' deleted successfully."), $menu->name), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_menus',
                array(
                    'category' => $menu->category
                )
            )
        );

    }

    /**
     * Deletes multiple menus at once give them ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('MENU_DELETE');

        $page          = $request->query->getDigits('page', 1);
        $selectedItems = $request->query->get('selected_fld');

        if (is_array($selectedItems)
            && count($selectedItems) > 0
        ) {
            foreach ($selectedItems as $id) {
                $menu = new \Menu($id);

                if ($menu->type == 'user') {
                    $menu->delete($_SESSION['userid']);
                    m::add(sprintf(_('Menu "%s" deleted successfully.'), $menu->name), m::SUCCESS);
                } else {
                    m::add(sprintf(_('Unable to delete the menu "%s" as is system internal.'), $menu->name), m::ERROR);

                }
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_menus',
                array(
                    'page'    => $page,
                )
            )
        );
    }
}
