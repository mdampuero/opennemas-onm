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
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Backend\Annotation\CheckModuleAccess;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

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
        $this->pages = array(array('title'=>_("Frontpage"),'link'=>"/"));

        if (\Onm\Module\ModuleManager::isActivated('OPINION_MANAGER')) {
            array_push($this->pages, array('title'=>_("Opinion"),'link'=>"opinion/"));
        }
        if (\Onm\Module\ModuleManager::isActivated('BLOG_MANAGER')) {
            array_push($this->pages, array('title'=>_("Bloggers"),'link'=>"blog/"));
        }
        if (\Onm\Module\ModuleManager::isActivated('ALBUM_MANAGER')) {
            array_push($this->pages, array('title'=>_("Album"),'link'=>"album/"));
        }
        if (\Onm\Module\ModuleManager::isActivated('VIDEO_MANAGER')) {
            array_push($this->pages, array('title'=>_("Video"),'link'=>"video/"));
        }
        if (\Onm\Module\ModuleManager::isActivated('POLL_MANAGER')) {
            array_push($this->pages, array('title'=>_("Poll"),'link'=>"poll/"));
        }
        if (\Onm\Module\ModuleManager::isActivated('LETTER_MANAGER')) {
            array_push($this->pages, array('title'=>_("Letters to the Editor"),'link'=>"cartas-al-director/"));
        }
        if (\Onm\Module\ModuleManager::isActivated('KIOSKO_MANAGER')) {
            array_push($this->pages, array('title'=>_("News Stand"),'link'=>"portadas-papel/"));
        }
        if (\Onm\Module\ModuleManager::isActivated('FORM_MANAGER')) {
            array_push($this->pages, array('title'=>_("Form"),'link'=>"participa/"));
        }
        if (\Onm\Module\ModuleManager::isActivated('NEWSLETTER_MANAGER')) {
            array_push($this->pages, array('title'=>_("Newsletter"),'link'=>"newsletter/"));
        }
        if (\Onm\Module\ModuleManager::isActivated('LIBRARY_MANAGER')) {
            array_push($this->pages, array('title'=>_("Archive"),'link'=>"archive/content/"));
        }

        $this->menuPositions = array('' => _('Without position'));

        $this->menuPositions = array_merge(
            $this->menuPositions,
            $this->container->get('instance_manager')->current_instance->theme->getMenus()
        );
        $this->view->assign('menu_positions', $this->menuPositions);

    }

    /**
     * Lists all the available menus
     *
     * @return void
     *
     * @Security("has_role('MENU_ADMIN')")
     *
     * @CheckModuleAccess(module="MENU_MANAGER")
     **/
    public function listAction()
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
     *
     * @Security("has_role('MENU_UPDATE')")
     *
     * @CheckModuleAccess(module="MENU_MANAGER")
     **/
    public function showAction(Request $request)
    {
        $id = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

        $ccm = \ContentCategoryManager::get_instance();
        $cm = new \ContentManager();

        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0);
        // Unused var  $categoryData
        unset($categoryData);

        foreach ($subcat as $subcategory) {
            $parentCategories = array_merge($parentCategories, $subcategory);
        }

        $albumCategories = $videoCategories = $pollCategories = array();
        foreach ($ccm->categories as $category) {
            if ($category->internal_category == \ContentManager::getContentTypeIdFromName('album')) {
                $albumCategories[] = $category;
            } elseif ($category->internal_category == \ContentManager::getContentTypeIdFromName('video')) {
                $videoCategories[] = $category;
            } elseif ($category->internal_category == \ContentManager::getContentTypeIdFromName('poll')) {
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

        $menu->items = array_values($menu->items);

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
     *
     * @Security("has_role('MENU_CREATE')")
     *
     * @CheckModuleAccess(module="MENU_MANAGER")
     **/
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $data = array(
                'name'      => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
                'params'    => serialize(
                    array(
                        'description' => $request->request->filter('description', null, FILTER_SANITIZE_STRING)
                    )
                ),
                'items'     => json_decode($request->request->get('items')),
                'position'  => $request->request->filter('position', '', FILTER_SANITIZE_STRING),
            );

            $menu = new \Menu();
            if ($menu->create($data)) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    sprintf(_("Menu '%s' created successfully."), $data['name'])
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("Unable to create the menu")
                );
            }

            return $this->redirect(
                $this->generateUrl(
                    'admin_menu_show',
                    array('id' => $menu->pk_menu)
                )
            );
        } else {
            $cm  = new \ContentManager();
            $ccm = \ContentCategoryManager::get_instance();

            list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0);
            // Unused var  $categoryData
            unset($categoryData);

            foreach ($subcat as $subcategory) {
                $parentCategories = array_merge($parentCategories, $subcategory);
            }
            $albumCategories = $videoCategories = $pollCategories = array();
            foreach ($ccm->categories as $category) {
                if ($category->internal_category == \ContentManager::getContentTypeIdFromName('album')) {
                    $albumCategories[] = $category;
                } elseif ($category->internal_category == \ContentManager::getContentTypeIdFromName('video')) {
                    $videoCategories[] = $category;
                } elseif ($category->internal_category == \ContentManager::getContentTypeIdFromName('poll')) {
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
     *
     * @Security("has_role('MENU_UPDATE')")
     *
     * @CheckModuleAccess(module="MENU_MANAGER")
     **/
    public function updateAction(Request $request)
    {
        $id = $this->request->query->getDigits('id');
        $menu = new \Menu($id);

        if ($menu->pk_menu == null) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find a menu with the id "%s"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_menus'));
        } else {
            // Check empty data
            if (count($request->request) < 1) {
                $this->get('session')->getFlashBag()->add('error', _("Menu data sent not valid."));

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
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _("Menu updated successfully.")
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("There was an error while updating the menu.")
                );
            }

            return $this->redirect(
                $this->generateUrl(
                    'admin_menu_show',
                    array('id' => $menu->pk_menu)
                )
            );
        }
    }
}
