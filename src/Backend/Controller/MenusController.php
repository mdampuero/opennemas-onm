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
    // /**
    //  * Lists all the available menus
    //  *
    //  * @param Request $request the request object
    //  *
    //  * @return Response
    //  *
    //  * @Security("hasExtension('MENU_MANAGER')
    //  *     and hasPermission('MENU_ADMIN')")
    //  */
    // public function listAction(Request $request)
    // {
    //     return $this->render('menus/list.tpl', [
    //         'menu_positions' => $this->getMenuPositions(),
    //         'language_data'  => $this->getLocaleData($request),
    //         'multilanguage' => in_array(
    //             'es.openhost.module.multilanguage',
    //             $this->get('core.instance')->activated_modules
    //         )
    //     ]);
    // }

    // /**
    //  * Shows the menu information
    //  *
    //  * @param Request $request the request object
    //  *
    //  * @return Response the response object
    //  *
    //  * @Security("hasExtension('MENU_MANAGER')
    //  *     and hasPermission('MENU_UPDATE')")
    //  */
    // public function showAction(Request $request)
    // {
    //     $id = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

    //     $menu = new \Menu($id);

    //     if (is_null($menu->id)) {
    //         $this->get('session')->getFlashBag()->add(
    //             'error',
    //             sprintf(_('Unable to find the menu with the id "%d"'), $id)
    //         );

    //         return $this->redirect($this->generateUrl('admin_menus'));
    //     }

    //     $menu->items = $menu->unlocalize($menu->getRawItems());

    //     return $this->render('menus/new.tpl', [
    //         'categories'       => $this->getCategories(),
    //         'language_data'    => $this->getLocaleData('frontend', $request),
    //         'menu'             => $menu,
    //         'menu_positions'   => $this->getMenuPositions(),
    //         'pages'            => $this->getModulePages(),
    //         'static_pages'     => $this->getStaticPages(),
    //         'sync_sites'       => $this->getSyncSites(),
    //         'multilanguage'    => in_array(
    //             'es.openhost.module.multilanguage',
    //             $this->get('core.instance')->activated_modules
    //         )
    //     ]);
    // }

    // /**
    //  * Updates the menu information
    //  *
    //  * @param Request $request the request object
    //  *
    //  * @return Response the response object
    //  *
    //  * @Security("hasExtension('MENU_MANAGER')
    //  *     and hasPermission('MENU_UPDATE')")
    //  */
    // public function updateAction(Request $request)
    // {
    //     $id   = $request->query->getDigits('id');
    //     $menu = new \Menu($id);

    //     if ($menu->pk_menu == null) {
    //         $this->get('session')->getFlashBag()->add(
    //             'error',
    //             sprintf(_('Unable to find a menu with the id "%s"'), $id)
    //         );

    //         return $this->redirect($this->generateUrl('admin_menus'));
    //     }

    //     // Check empty data
    //     if (empty($request->request)) {
    //         $this->get('session')->getFlashBag()->add('error', _("Menu data sent not valid."));

    //         return $this->redirect($this->generateUrl('admin_menu_show', ['id' => $id]));
    //     }

    //     $data = [
    //         'name'      => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
    //         'params'    => serialize([
    //             'description' => $request->request->filter('description', null, FILTER_SANITIZE_STRING)
    //         ]),
    //         'site'      => SITE,
    //         'pk_father' => $request->request->filter('pk_father', 'user', FILTER_SANITIZE_STRING),
    //         'items'     => json_decode($request->request->get('items')),
    //         'position'  => $request->request->filter('position', '', FILTER_SANITIZE_STRING),
    //     ];

    //     if ($menu->update($data)) {
    //         $this->get('session')->getFlashBag()->add(
    //             'success',
    //             _("Menu updated successfully.")
    //         );
    //     } else {
    //         $this->get('session')->getFlashBag()->add(
    //             'error',
    //             _("There was an error while updating the menu.")
    //         );
    //     }

    //     return $this->redirect($this->generateUrl('admin_menu_show', [
    //         'id' => $menu->pk_menu,
    //         'locale' => $request->get('lang')
    //     ]));
    // }

    // /**
    //  * {@inheritdoc}
    //  */
    // protected function getCategories($items = null)
    // {
    //     $context = $this->get('core.locale')->getContext();
    //     $this->get('core.locale')->setContext('frontend');

    //     $categories = $this->get('api.service.category')->getList();
    //     $this->get('core.locale')->setContext($context);

    //     return $this->get('api.service.category')
    //         ->responsify($categories['items']);
    // }

    // /**
    //  * Returns a list of static pages and their slugs
    //  *
    //  * @return array the list of static pages
    //  */
    // private function getStaticPages()
    // {
    //     $context = $this->get('core.locale')->getContext();
    //     $this->get('core.locale')->setContext('frontend');

    //     $oql = 'content_type_name = "static_page" and in_litter = "0"'
    //        . ' order by created desc';

    //     $response = $this->get('api.service.content')->getList($oql);
    //     $this->get('core.locale')->setContext($context);

    //     return array_map(function ($a) {
    //         return [
    //             'title'      => $a->title,
    //             'slug'       => $a->slug,
    //             'pk_content' => $a->pk_content
    //         ];
    //     }, $response['items']);
    // }

    // /**
    //  * Returns the list of synchronized sites.
    //  *
    //  * @return array The list of synchronized sites.
    //  */
    // private function getSyncSites()
    // {
    //     $syncSites = $this->get('orm.manager')
    //         ->getDataSet('Settings', 'instance')
    //         ->get('sync_params');

    //     if (empty($syncSites)) {
    //         return [];
    //     }

    //     return $syncSites;
    // }

    // /**
    //  * Returns a list of activated module pages
    //  *
    //  * @return array the list of module pages
    //  */
    // private function getModulePages()
    // {
    //     $pages = [['title' => _("Frontpage"),'link' => "/"]];

    //     if ($this->get('core.security')->hasExtension('OPINION_MANAGER')) {
    //         $pages[] = [
    //             'title' => _('Opinions'),
    //             'link'  => trim(
    //                 $this->get('router')->generate('frontend_opinion_frontpage'),
    //                 '/'
    //             )
    //         ];
    //     }

    //     if ($this->get('core.security')->hasExtension('BLOG_MANAGER')) {
    //         $pages[] = [
    //             'title' => _('Blogs'),
    //             'link'  => trim(
    //                 $this->get('router')->generate('frontend_blog_frontpage'),
    //                 '/'
    //             )
    //         ];
    //     }

    //     if ($this->get('core.security')->hasExtension('ALBUM_MANAGER')) {
    //         $pages[] = [
    //             'title' => _('Albums'),
    //             'link'  => trim(
    //                 $this->get('router')->generate('frontend_album_frontpage'),
    //                 '/'
    //             )
    //         ];
    //     }

    //     if ($this->get('core.security')->hasExtension('VIDEO_MANAGER')) {
    //         $pages[] = [
    //             'title' => _('Videos'),
    //             'link'  => trim(
    //                 $this->get('router')->generate('frontend_video_frontpage'),
    //                 '/'
    //             )
    //         ];
    //     }

    //     if ($this->get('core.security')->hasExtension('POLL_MANAGER')) {
    //         $pages[] = [
    //             'title' => _('Polls'),
    //             'link'  => trim(
    //                 $this->get('router')->generate('frontend_poll_frontpage'),
    //                 '/'
    //             )
    //         ];
    //     }

    //     if ($this->get('core.security')->hasExtension('es.openhost.module.events')) {
    //         $pages[] = [
    //             'title' => _('Events'),
    //             'link'  => trim(
    //                 $this->get('router')->generate('frontend_events'),
    //                 '/'
    //             )
    //         ];
    //     }

    //     if ($this->get('core.security')->hasExtension('es.openhost.module.obituaries')) {
    //         $pages[] = [
    //             'title' => _('Obituaries'),
    //             'link'  => trim(
    //                 $this->get('router')->generate('frontend_obituaries'),
    //                 '/'
    //             )
    //         ];
    //     }

    //     if ($this->get('core.security')->hasExtension('LETTER_MANAGER')) {
    //         $pages[] = [
    //             'title' => _('Letters to the Editor'),
    //             'link'  => trim(
    //                 $this->get('router')->generate('frontend_letter_frontpage'),
    //                 '/'
    //             )
    //         ];
    //     }

    //     if ($this->get('core.security')->hasExtension('KIOSKO_MANAGER')) {
    //         $pages[] = [
    //             'title' => _('News Stand'),
    //             'link'  => trim(
    //                 $this->get('router')->generate('frontend_newsstand_frontpage'),
    //                 '/'
    //             )
    //         ];
    //     }

    //     if ($this->get('core.security')->hasExtension('FORM_MANAGER')) {
    //         $pages[] = [
    //             'title' => _('Form'),
    //             'link'  => trim(
    //                 $this->get('router')->generate('frontend_participa_frontpage'),
    //                 '/'
    //             )
    //         ];
    //     }

    //     if ($this->get('core.security')->hasExtension('NEWSLETTER_MANAGER')) {
    //         $pages[] = [
    //             'title' => _('Newsletter'),
    //             'link'  => trim(
    //                 $this->get('router')->generate('frontend_newsletter_subscribe_show'),
    //                 '/'
    //             )
    //         ];
    //     }

    //     if ($this->get('core.security')->hasExtension('LIBRARY_MANAGER')) {
    //         $pages[] = [
    //             'title' => _('Archive'),
    //             'link'  => trim(
    //                 $this->get('router')->generate('frontend_archive', [ 'component' => 'content' ]),
    //                 '/'
    //             )
    //         ];
    //     }

    //     return $pages;
    // }

    // /**
    //  * Returns the list of menu positions
    //  *
    //  * @return array the list of menu positions
    //  */
    // private function getMenuPositions()
    // {
    //     return array_merge(
    //         [ '' => _('Without position') ],
    //         $this->container->get('core.manager.menu')->getMenus()
    //     );
    // }
}
