<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the menus
 *
 * @package Backend_Controllers
 */
class MenusController extends Controller
{
    /**
     * Lists all the available menus
     *
     * @return void
     *
     * @Security("hasExtension('MENU_MANAGER')
     *     and hasPermission('MENU_ADMIN')")
     */
    public function listAction(Request $request)
    {
        return $this->render('menues/list.tpl', [
            'menu_positions' => $this->getMenuPositions(),
            'language_data'  => $this->getLocaleData($request),
            'multilanguage' => in_array(
                'es.openhost.module.multilanguage',
                $this->get('core.instance')->activated_modules
            )
        ]);
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

        $menu = new \Menu($id);

        if (is_null($menu->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the menu with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_menus'));
        }

        $menu->items = $menu->unlocalize($menu->getRawItems());

        $categories = $this->getCategoriesByType();

        return $this->render('menues/new.tpl', [
            'categories'       => $categories['categories'],
            'categories_album' => $categories['categories_album'],
            'categories_poll'  => $categories['categories_poll'],
            'categories_video' => $categories['categories_video'],
            'language_data'    => $this->getLocaleData('frontend', $request),
            'menu'             => $menu,
            'menu_positions'   => $this->getMenuPositions(),
            'pages'            => $this->getModulePages(),
            'static_pages'     => $this->getStaticPages(),
            'subcat'           => $categories['subcategories'],
            'sync_sites'       => $this->getSyncSites(),
            'multilanguage'    => in_array(
                'es.openhost.module.multilanguage',
                $this->get('core.instance')->activated_modules
            )
        ]);
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
            $data = [
                'name'      => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
                'params'    => serialize([
                    'description' => $request->request->filter('description', null, FILTER_SANITIZE_STRING)
                ]),
                'items'     => json_decode($request->request->get('items')),
                'position'  => $request->request->filter('position', '', FILTER_SANITIZE_STRING),
            ];

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

            return $this->redirect($this->generateUrl('admin_menu_show', [
                'id'     => $menu->pk_menu,
                'locale' => $request->get('locale')
            ]));
        }

        $params = $this->getCategoriesByType();

        return $this->render('menues/new.tpl', [
            'menu'             => new \Menu(),
            'categories'       => $params['categories'],
            'categories_album' => $params['categories_album'],
            'categories_album' => $params['categories_video'],
            'categories_poll'  => $params['categories_poll'],
            'language_data'    => $this->getLocaleData($request),
            'menu_positions'   => $this->getMenuPositions(),
            'pages'            => $this->getModulePages(),
            'staticPages'      => $this->getStaticPages(),
            'subcat'           => $params['subcategories'],
            'sync_sites'       => $this->getSyncSites(),
        ]);
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
        $id   = $this->request->query->getDigits('id');
        $menu = new \Menu($id);

        if ($menu->pk_menu == null) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find a menu with the id "%s"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_menus'));
        }

        // Check empty data
        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add('error', _("Menu data sent not valid."));

            return $this->redirect($this->generateUrl('admin_menu_show', ['id' => $id]));
        }

        $data = [
            'name'      => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
            'params'    => serialize([
                'description' => $request->request->filter('description', null, FILTER_SANITIZE_STRING)
            ]),
            'site'      => SITE,
            'pk_father' => $request->request->filter('pk_father', 'user', FILTER_SANITIZE_STRING),
            'items'     => json_decode($request->request->get('items')),
            'position'  => $request->request->filter('position', '', FILTER_SANITIZE_STRING),
        ];

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

        return $this->redirect($this->generateUrl('admin_menu_show', [
            'id' => $menu->pk_menu,
            'locale' => $request->get('lang')
        ]));
    }

    /**
     * Returns the category listings by content type
     *
     * @return array the list of category listings
     **/
    private function getCategoriesByType()
    {
        $ccm = \ContentCategoryManager::get_instance();

        list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0);
        // Unused var  $categoryData
        unset($categoryData);

        foreach ($subcat as $subcategory) {
            $parentCategories = array_merge($parentCategories, $subcategory);
        }

        $albumCategories = $videoCategories = $pollCategories = [];
        foreach ($ccm->categories as $category) {
            if ($category->internal_category == \ContentManager::getContentTypeIdFromName('album')) {
                $albumCategories[] = $category;
            } elseif ($category->internal_category == \ContentManager::getContentTypeIdFromName('video')) {
                $videoCategories[] = $category;
            } elseif ($category->internal_category == \ContentManager::getContentTypeIdFromName('poll')) {
                $pollCategories[] = $category;
            }
        }

        return [
            'categories'       => $parentCategories,
            'subcategories'    => $subcat,
            'categories_album' => $albumCategories,
            'categories_video' => $videoCategories,
            'categories_poll'  => $pollCategories,
        ];
    }

    /**
     * Returns a list of static pages and their slugs
     *
     * @return array the list of static pages
     **/
    private function getStaticPages()
    {
        $oql = 'content_type_name = "static_page" and in_litter = "0"'
           . ' order by created desc';

        $staticPages = $this->get('orm.manager')
            ->getRepository('Content')
            ->findBy($oql);

        $statics = [];
        foreach ($staticPages as $staticPage) {
            $statics[] = [
                'title'      => $staticPage->title,
                'slug'       => $staticPage->slug,
                'pk_content' => $staticPage->pk_content
            ];
        }

        return $statics;
    }

    /**
     * Returns the list of synchronized sites.
     *
     * @return array The list of synchronized sites.
     */
    private function getSyncSites()
    {
        // Fetch synchronized elements if exists
        $syncSites = $this->get('setting_repository')->get('sync_params');

        if (empty($syncSites)) {
            return [];
        }

        return $syncSites;
    }

    /**
     * Returns a list of activated module pages
     *
     * @return array the list of module pages
     **/
    private function getModulePages()
    {
        $pages = [['title' => _("Frontpage"),'link' => "/"]];

        if ($this->get('core.security')->hasExtension('OPINION_MANAGER')) {
            array_push($pages, ['title' => _("Opinion"),'link' => "opinion/"]);
        }

        if ($this->get('core.security')->hasExtension('BLOG_MANAGER')) {
            array_push($pages, ['title' => _("Bloggers"),'link' => "blog/"]);
        }

        if ($this->get('core.security')->hasExtension('ALBUM_MANAGER')) {
            array_push($pages, ['title' => _("Album"),'link' => "album/"]);
        }

        if ($this->get('core.security')->hasExtension('VIDEO_MANAGER')) {
            array_push($pages, ['title' => _("Video"),'link' => "video/"]);
        }

        if ($this->get('core.security')->hasExtension('POLL_MANAGER')) {
            array_push($pages, ['title' => _("Poll"),'link' => "poll/"]);
        }

        if ($this->get('core.security')->hasExtension('LETTER_MANAGER')) {
            array_push($pages, ['title' => _("Letters to the Editor"),'link' => "cartas-al-director/"]);
        }

        if ($this->get('core.security')->hasExtension('KIOSKO_MANAGER')) {
            array_push($pages, ['title' => _("News Stand"),'link' => "portadas-papel/"]);
        }

        if ($this->get('core.security')->hasExtension('FORM_MANAGER')) {
            array_push($pages, ['title' => _("Form"),'link' => "participa/"]);
        }

        if ($this->get('core.security')->hasExtension('NEWSLETTER_MANAGER')) {
            array_push($pages, ['title' => _("Newsletter"),'link' => "newsletter/"]);
        }

        if ($this->get('core.security')->hasExtension('LIBRARY_MANAGER')) {
            array_push($pages, ['title' => _("Archive"),'link' => "archive/content/"]);
        }

        return $pages;
    }

    /**
     * Returns the list of menu positions
     *
     * @return array the list of menu positions
     **/
    private function getMenuPositions()
    {
        return array_merge(
            [ '' => _('Without position') ],
            $this->container->get('core.manager.menu')->getMenus()
        );
    }
}
