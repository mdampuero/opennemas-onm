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

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
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

        if ($this->get('core.security')->hasExtension('OPINION_MANAGER')) {
            array_push($this->pages, array('title'=>_("Opinion"),'link'=>"opinion/"));
        }
        if ($this->get('core.security')->hasExtension('BLOG_MANAGER')) {
            array_push($this->pages, array('title'=>_("Bloggers"),'link'=>"blog/"));
        }
        if ($this->get('core.security')->hasExtension('ALBUM_MANAGER')) {
            array_push($this->pages, array('title'=>_("Album"),'link'=>"album/"));
        }
        if ($this->get('core.security')->hasExtension('VIDEO_MANAGER')) {
            array_push($this->pages, array('title'=>_("Video"),'link'=>"video/"));
        }
        if ($this->get('core.security')->hasExtension('POLL_MANAGER')) {
            array_push($this->pages, array('title'=>_("Poll"),'link'=>"poll/"));
        }
        if ($this->get('core.security')->hasExtension('LETTER_MANAGER')) {
            array_push($this->pages, array('title'=>_("Letters to the Editor"),'link'=>"cartas-al-director/"));
        }
        if ($this->get('core.security')->hasExtension('KIOSKO_MANAGER')) {
            array_push($this->pages, array('title'=>_("News Stand"),'link'=>"portadas-papel/"));
        }
        if ($this->get('core.security')->hasExtension('FORM_MANAGER')) {
            array_push($this->pages, array('title'=>_("Form"),'link'=>"participa/"));
        }
        if ($this->get('core.security')->hasExtension('NEWSLETTER_MANAGER')) {
            array_push($this->pages, array('title'=>_("Newsletter"),'link'=>"newsletter/"));
        }
        if ($this->get('core.security')->hasExtension('LIBRARY_MANAGER')) {
            array_push($this->pages, array('title'=>_("Archive"),'link'=>"archive/content/"));
        }

        $this->menuPositions = array_merge(
            [ '' => _('Without position') ],
            $this->container->get('core.manager.menu')->getMenus()
        );

        $this->view->assign('menu_positions', $this->menuPositions);

    }

    /**
     * Lists all the available menus
     *
     * @return void
     *
     * @Security("hasExtension('MENU_MANAGER')
     *     and hasPermission('MENU_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('menues/list.tpl');
    }

    /**
     * Shows the menu information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('MENU_MANAGER')
     *     and hasPermission('MENU_UPDATE')")
     */
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

        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('Content');

        $oql = 'content_type_name = "static_page" and in_litter = 0'
           . ' order by created desc';

        $staticPages = $em->getRepository('Content')->findBy($oql);
        $staticPages = $converter->responsify($staticPages);

        // Get categories from menu
        $menu = new \Menu($id);

        // Fetch synchronized elements if exists
        $syncSites = [];
        if ($syncParams = s::get('sync_params')) {
            $syncSites = $syncParams;
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
                'elements'        => $syncSites,
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
     * @Security("hasExtension('MENU_MANAGER')
     *     and hasPermission('MENU_CREATE')")
     */
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

            $em        = $this->get('orm.manager');
            $converter = $em->getConverter('Content');

            $oql = 'content_type_name = "static_page" and in_litter = 0'
               . ' order by created desc';

            $staticPages = $em->getRepository('Content')->findBy($oql);
            $staticPages = $converter->responsify($staticPages);

            // Fetch synchronized elements if exists
            $syncSites = [];
            if ($syncParams = s::get('sync_params')) {
                $syncSites = $syncParams;
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
                    'pages'           => $this->pages,
                    'menu_positions'  => $this->menuPositions,
                    'elements'        => $syncSites,
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
     * @Security("hasExtension('MENU_MANAGER')
     *     and hasPermission('MENU_UPDATE')")
     */
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
