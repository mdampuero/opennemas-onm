<?php
/**
 * Handles the actions for the system information
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
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class AlbumsController extends Controller
{
    /**
     * Common code for all the actions.
     */
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('ALBUM_MANAGER');

        $request = $this->get('request');

        $contentType = \ContentManager::getContentTypeIdFromName('album');

        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->categoryData)
            = $this->ccm->getArraysMenu($category, $contentType);

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone  = new \DateTimeZone($timezones[s::get('time_zone', 'UTC')]);

        $this->view->assign(
            array(
                'category'     => $category,
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                'datos_cat'    => $this->categoryData,
                'timezone'     => $timezone->getName()
            )
        );
    }

    /**
     * Lists all albums.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('ALBUM_ADMIN')")
     */
    public function listAction(Request $request)
    {
        return $this->render('album/list.tpl');
    }

    /**
     * Lists all the albums for the widget.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('ALBUM_ADMIN')")
     */
    public function widgetAction(Request $request)
    {
        return $this->render(
            'album/list.tpl',
            array(
                'category'   => 'widget',
            )
        );
    }

    /**
     * Shows and handles the form for create a new album.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('ALBUM_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' == $this->request->getMethod()) {
            $album = new \Album();
            $album->create($_POST);
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Album created successfully')
            );

            // Get category name
            $ccm = \ContentCategoryManager::get_instance();
            $categoryName = $ccm->getName($category);

            // Clean cache album home and frontpage for category
            $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName).'|1');
            $tplManager->delete('home|1');

            // Return user to list if has no update acl
            if (Acl::check('ALBUM_UPDATE')) {
                return $this->redirect(
                    $this->generateUrl('admin_album_show', array('id' => $album->id))
                );
            } else {
                return $this->redirect(
                    $this->generateUrl('admin_albums')
                );
            }

        } else {
            $authorsComplete = \User::getAllUsersAuthors();
            $authors = array( '0' => _(' - Select one author - '));
            foreach ($authorsComplete as $author) {
                $authors[$author->id] = $author->name;
            }

            return $this->render(
                'album/new.tpl',
                array ( 'authors' => $authors, 'commentsConfig' => s::get('comments_config'),)
            );
        }
    }

    /**
     * Deletes an album given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('ALBUM_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $request = $this->get('request');
        $id      = $request->query->getDigits('id');
        $page    = $request->query->getDigits('page', 1);

        $album = new \Album($id);

        if (is_null($album->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find an album with the id "%d".'), $id)
            );
        }

        $rel= new \RelatedContent();
        $rel->deleteAll($id);
        $album->delete($id, $_SESSION['userid']);

        $this->get('session')->getFlashBag()->add(
            'success',
            _('Album delete successfully.')
        );

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_albums',
                    array(
                        'category' => $album->category,
                        'page'     => $page,
                    )
                )
            );
        } else {
            return new Response('ok');
        }
    }

    /**
     * Shows the information for an album given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('ALBUM_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $album = new \Album($id);

        if (is_null($album->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find an album with the id "%d".'), $id)
            );

            return $this->redirect($this->generateUrl('admin_albums'));
        }

        $photos          = $album->_getAttachedPhotos($id);
        $authorsComplete = \User::getAllUsersAuthors();
        $authors         = array( '0' => _(' - Select one author - '));
        foreach ($authorsComplete as $author) {
            $authors[$author->id] = $author->name;
        }

        return $this->render(
            'album/new.tpl',
            array(
                'category'       => $album->category,
                'photos'         => $photos,
                'album'          => $album,
                'authors'        => $authors,
                'commentsConfig' => s::get('comments_config'),
            )
        );
    }

    /**
     * Updates the album information.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('ALBUM_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id    = $request->request->getDigits('id');
        $album = new \Album($id);

        if (is_null($album->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find an album with the id "%d".'), $id)
            );

            return $this->redirect($this->generateUrl('admin_albums'));
        }

        if (!Acl::isAdmin()
            && !Acl::check('CONTENT_OTHER_UPDATE')
            && !$album->isOwner($_SESSION['userid'])
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You don't have enough privileges for modify this album.")
            );

            return $this->redirect(
                $this->generateUrl(
                    'admin_albums',
                    array('category' => $album->category,)
                )
            );
        }

        // Check empty data
        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Album data sent not valid.")
            );

            return $this->redirect($this->generateUrl('admin_album_show', array('id' => $id)));
        }

        $data = array(
            'id'             => $id,
            'content_status' => $request->request->getDigits('content_status', 0, FILTER_SANITIZE_STRING),
            'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
            'category'       => $request->request->getDigits('category'),
            'agency'         => $request->request->filter('agency', '', FILTER_SANITIZE_STRING),
            'description'    => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
            'metadata'       => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
            'album_frontpage_image' =>
                $request->request->filter('album_frontpage_image', '', FILTER_SANITIZE_STRING),
            'album_photos_id'       => $request->request->get('album_photos_id'),
            'album_photos_footer'   => $request->request->get('album_photos_footer'),
            'fk_author'             => $request->request->filter('fk_author', 0, FILTER_VALIDATE_INT),
            'starttime'             => $album->starttime,
        );

        $album->update($data);
        $this->get('session')->getFlashBag()->add(
            'success',
            _("Album updated successfully.")
        );

        $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
        $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $album->category_name).'|'.$album->id);
        $tplManager->delete('home|1');

        return $this->redirect(
            $this->generateUrl('admin_album_show', array('id' => $album->id))
        );
    }

    /**
     * Save positions for widget.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('ALBUM_ADMIN')")
     */
    public function savePositionsAction(Request $request)
    {
        $positions = $request->query->get('positions');

        $result = true;
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $pos = 1;
            foreach ($positions as $id) {
                $album = new \Album($id);
                $result = $result && $album->setPosition($pos);

                $pos++;
            }
        }

        if ($result) {
            $message = _("Positions saved successfully.");
        } else {
            $message = _("Unable to save the new positions.");
        }

        return new Response($message);
    }

    /**
     * Render the content provider for albums.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;

        $em  = $this->get('entity_repository');
        $ids = $this->get('frontpage_repository')->getContentIdsForHomepageOfCategory();

        $filters = array(
            'content_type_name' => array(array('value' => 'album')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
            'pk_content'        => array(array('value' => $ids, 'operator' => 'NOT IN'))
        );

        $albums      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countAlbums = $em->countBy($filters);

        // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countAlbums,
                'fileName'    => $this->generateUrl(
                    'admin_albums_content_provider',
                    array('category' => $categoryId)
                ).'&page=%d',
            )
        );

        return $this->render(
            'album/content-provider.tpl',
            array(
                'albums' => $albums,
                'pager'  => $pagination,
            )
        );
    }

    /**
     * Lists all the albums withing a category for the related manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = array(
            'content_type_name' => array(array('value' => 'album')),
            'in_litter'         => array(array('value' => 1, 'operator' => '!='))
        );

        if ($categoryId != 0) {
            $filters['category_name'] = array(array('value' => $category->name));
        }

        $albums      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countAlbums = $em->countBy($filters);

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 1,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countAlbums,
                'fileName'    => $this->generateUrl(
                    'admin_albums_content_provider_related',
                    array('category' => $categoryId)
                ).'&page=%d',
            )
        );

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            array(
                'contentType'           => 'Album',
                'contents'              => $albums,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $categoryId,
                'pagination'            => $pagination->links,
                'contentProviderUrl'    => $this->generateUrl('admin_albums_content_provider_related'),
            )
        );
    }

    /**
     * Handles and shows the album configuration form.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('ALBUM_SETTINGS')")
     */
    public function configAction(Request $request)
    {
        if ('POST' !== $this->request->getMethod()) {
            $configurationsKeys = array('album_settings',);
            $configurations = s::get($configurationsKeys);

            return $this->render(
                'album/config.tpl',
                array('configs'   => $configurations,)
            );
        }

        $settings = array(
            'album_settings' => array(
                'total_widget'     => $request->request->getDigits('album_settings_total_widget'),
                'crop_width'       => $request->request->getDigits('album_settings_crop_width'),
                'crop_height'      => $request->request->getDigits('album_settings_crop_height'),
                'orderFrontpage'   => $request->request->filter('album_settings_orderFrontpage', '', FILTER_SANITIZE_STRING),
                'time_last'        => $request->request->getDigits('album_settings_time_last'),
                'total_front'      => $request->request->getDigits('album_settings_total_front'),
                'total_front_more' => $request->request->getDigits('album_settings_total_front_more'),
            )
        );

        foreach ($settings as $key => $value) {
            s::set($key, $value);
        }

        $this->get('session')->getFlashBag()->add(
            'success',
            _('Settings saved successfully.')
        );

        return $this->redirect($this->generateUrl('admin_albums_config'));
    }
}
