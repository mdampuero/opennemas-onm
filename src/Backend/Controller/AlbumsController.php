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
use Backend\Annotation\CheckModuleAccess;
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

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
     * @return Response          The response object.
     *
     * @Security("has_role('ALBUM_ADMIN')")
     *
     * @CheckModuleAccess(module="ALBUM_MANAGER")
     */
    public function listAction()
    {
        $categories = [ [ 'name' => _('All'), 'value' => -1 ] ];

        foreach ($this->parentCategories as $key => $category) {
            $categories[] = [
                'name' => $category->title,
                'value' => $category->name
            ];

            foreach ($this->subcat[$key] as $subcategory) {
                $categories[] = [
                    'name' => '&rarr; ' . $subcategory->title,
                    'value' => $subcategory->name
                ];
            }
        }

        return $this->render(
            'album/list.tpl',
            [ 'categories' => $categories ]
        );
    }

    /**
     * Lists all the albums for the widget.
     *
     * @return Response          The response object.
     *
     * @Security("has_role('ALBUM_ADMIN')")
     *
     * @CheckModuleAccess(module="ALBUM_MANAGER")
     */
    public function widgetAction()
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
     *
     * @CheckModuleAccess(module="ALBUM_MANAGER")
     */
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $album = new \Album();
            $album->create($request->request->all());

            $this->get('session')->getFlashBag()->add(
                'success',
                _('Album created successfully')
            );

            // Get category name
            $ccm = \ContentCategoryManager::get_instance();
            $categoryName = $ccm->getName($request->request->get('category'));

            // TODO: remove cache cleaning actions
            // Clean cache album home and frontpage for category
            $cacheManager = $this->get('template_cache_manager');
            $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));
            $cacheManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName).'|1');
            $cacheManager->delete('home|1');

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
            $authors = array('0' => _(' - Select one author - '));
            foreach ($authorsComplete as $author) {
                $authors[$author->id] = $author->name;
            }

            return $this->render(
                'album/new.tpl',
                array ('authors' => $authors, 'commentsConfig' => s::get('comments_config'),)
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
     *
     * @CheckModuleAccess(module="ALBUM_MANAGER")
     */
    public function deleteAction(Request $request)
    {
        $id      = $request->query->getDigits('id');
        $page    = $request->query->getDigits('page', 1);

        $album = new \Album($id);

        if (is_null($album->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find an album with the id "%d".'), $id)
            );
        }

        // Delete all related and relations
        getService('related_contents')->deleteAll($id);

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
     *
     * @CheckModuleAccess(module="ALBUM_MANAGER")
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
        $authors         = array('0' => _(' - Select one author - '));
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
     *
     * @CheckModuleAccess(module="ALBUM_MANAGER")
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
            'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'category'       => $request->request->getDigits('category'),
            'agency'         => $request->request->filter('agency', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'description'    => $request->request->get('description', ''),
            'metadata'       => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
            'with_comment'   => $request->request->filter('with_comment', 0, FILTER_SANITIZE_STRING),
            'album_frontpage_image' => $request->request->filter('album_frontpage_image', '', FILTER_SANITIZE_STRING),
            'album_photos_id'       => $request->request->get('album_photos_id'),
            'album_photos_footer'   => $request->request->get('album_photos_footer'),
            'fk_author'             => $request->request->filter('fk_author', 0, FILTER_VALIDATE_INT),
            'starttime'             => $album->starttime,
            'params'         => $request->request->get('params', []),
        );

        $album->update($data);
        $this->get('session')->getFlashBag()->add(
            'success',
            _("Album updated successfully.")
        );

        // TODO: remove cache cleaning actions
        $cacheManager = $this->get('template_cache_manager');
        $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));
        $cacheManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $album->category_name).'|'.$album->id);
        $cacheManager->delete('home|1');

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
     *
     * @CheckModuleAccess(module="ALBUM_MANAGER")
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
     *
     * @CheckModuleAccess(module="ALBUM_MANAGER")
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;

        $em  = $this->get('entity_repository');
        $ids = $this->get('frontpage_repository')->getContentIdsForHomepageOfCategory((int)$categoryId);

        $filters = array(
            'content_type_name' => array(array('value' => 'album')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
            'pk_content'        => array(array('value' => $ids, 'operator' => 'NOT IN'))
        );

        $albums      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countAlbums = $em->countBy($filters);

        // Build the pagination
        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $itemsPerPage,
            'page'        => $page,
            'total'       => $countAlbums,
            'route'       => [
                'name'   => 'admin_albums_content_provider',
                'params' => ['category' => $categoryId]
            ],
        ]);

        return $this->render(
            'album/content-provider.tpl',
            array(
                'albums'     => $albums,
                'pagination' => $pagination,
            )
        );
    }

    /**
     * Lists all the albums withing a category for the related manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @CheckModuleAccess(module="ALBUM_MANAGER")
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = array(
            'content_type_name' => [['value' => 'album']],
            'in_litter'         => [['value' => 1, 'operator' => '!=']]
        );

        if ($categoryId != 0) {
            $filters['category_name'] = [['value' => $category->name]];
        }

        $albums      = $em->findBy($filters, ['created' => 'desc'], $itemsPerPage, $page);
        $countAlbums = $em->countBy($filters);

        // Build the pagination
        $pagination = $this->get('paginator')->get([
            'epp'   => $itemsPerPage,
            'page'  => $page,
            'total' => $countAlbums,
            'route' => [
                'name'   => 'admin_albums_content_provider_related',
                'params' => ['category' => $categoryId]
            ],
        ]);

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            array(
                'contentType'           => 'Album',
                'contents'              => $albums,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $categoryId,
                'pagination'            => $pagination,
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
     *
     * @CheckModuleAccess(module="ALBUM_MANAGER")
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
