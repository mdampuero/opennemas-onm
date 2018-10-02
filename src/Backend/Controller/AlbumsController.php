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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
class AlbumsController extends Controller
{
    /**
     * Common code for all the actions.
     */
    public function init()
    {
        $request     = $this->get('request_stack')->getCurrentRequest();
        $contentType = \ContentManager::getContentTypeIdFromName('album');
        $category    = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm = \ContentCategoryManager::get_instance();

        list($this->parentCategories, $this->subcat, $this->categoryData)
            = $this->ccm->getArraysMenu($category, $contentType);

        $this->view->assign([
            'category'     => $category,
            'subcat'       => $this->subcat,
            'allcategorys' => $this->parentCategories,
            'datos_cat'    => $this->categoryData,
        ]);
    }

    /**
     * Lists all albums.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('ALBUM_MANAGER')
     *     and hasPermission('ALBUM_ADMIN')")
     */
    public function listAction()
    {
        $categories = [ [ 'name' => _('All'), 'value' => null ] ];

        foreach ($this->parentCategories as $key => $category) {
            $categories[] = [
                'name' => $category->title,
                'value' => $category->pk_content_category
            ];

            foreach ($this->subcat[$key] as $subcategory) {
                $categories[] = [
                    'name' => '&rarr; ' . $subcategory->title,
                    'value' => $subcategory->pk_content_category
                ];
            }
        }

        return $this->render('album/list.tpl', [ 'categories' => $categories ]);
    }

    /**
     * Lists all the albums for the widget.
     *
     * @return Response          The response object.
     *
     * @Security("hasExtension('ALBUM_MANAGER')
     *     and hasPermission('ALBUM_ADMIN')")
     */
    public function widgetAction()
    {
        return $this->render('album/list.tpl', [ 'category' => 'widget' ]);
    }

    /**
     * Shows and handles the form for create a new album.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('ALBUM_MANAGER')
     *     and hasPermission('ALBUM_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            return $this->render('album/new.tpl', [
                'authors'        => $this->getAuthors(),
                'commentsConfig' => $this->get('orm.manager')
                    ->getDataSet('Settings')
                    ->get('comments_config'),
                'locale'         => $this->get('core.locale')
                    ->getLocale('frontend'),
                'tags'           => []
            ]);
        }

        $data = [
            'content_status' => $request->request->getDigits('content_status', 0, FILTER_SANITIZE_STRING),
            'title'          => $request->request
                ->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'category'       => $request->request->getDigits('category'),
            'agency'         => $request->request
                ->filter('agency', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'description'    => $request->request->get('description', ''),
            'with_comment'   => $request->request->filter('with_comment', 0, FILTER_SANITIZE_STRING),
            'album_frontpage_image' => $request->request->filter('album_frontpage_image', '', FILTER_SANITIZE_STRING),
            'album_photos_id'       => $request->request->get('album_photos_id'),
            'album_photos_footer'   => $request->request->get('album_photos_footer'),
            'fk_author'             => $request->request->filter('fk_author', 0, FILTER_VALIDATE_INT),
            'endtime'        => $request->request
                ->filter('endtime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'starttime'      => $request->request
                ->filter('starttime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'params'         => $request->request->get('params', []),
            'tag_ids'        => json_decode($request->request->get('tag_ids', ''), true)
        ];

        $album = new \Album();
        $album->create($data);

        $this->get('session')->getFlashBag()->add(
            'success',
            _('Album created successfully')
        );

        // Return user to list if has no update acl
        if ($this->get('core.security')->hasPermission('ALBUM_UPDATE')) {
            return $this->redirect(
                $this->generateUrl('admin_album_show', [ 'id' => $album->id ])
            );
        } else {
            return $this->redirect(
                $this->generateUrl('admin_albums')
            );
        }
    }

    /**
     * Deletes an album given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('ALBUM_MANAGER')
     *     and hasPermission('ALBUM_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $id    = $request->query->getDigits('id');
        $page  = $request->query->getDigits('page', 1);
        $album = new \Album($id);

        if (is_null($album->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find an album with the id "%d".'), $id)
            );
        }

        // Delete all related and relations
        getService('related_contents')->deleteAll($id);

        $album->delete($id, $this->getUser()->id);

        $this->get('session')->getFlashBag()->add(
            'success',
            _('Album delete successfully.')
        );

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('admin_albums', [
                'category' => $album->category,
                'page'     => $page,
            ]));
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
     * @Security("hasExtension('ALBUM_MANAGER')
     *     and hasPermission('ALBUM_UPDATE')")
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

        $auxTagIds      = $album->getContentTags($album->id);
        $album->tag_ids = array_key_exists($album->id, $auxTagIds) ?
            $auxTagIds[$album->id] :
            [];

        if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
            && !$album->isOwner($this->getUser()->id)
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You don't have enough privileges for modify this album.")
            );

            return $this->redirect($this->generateUrl('admin_albums', [
                'category' => $album->category
            ]));
        }

        return $this->render('album/new.tpl', [
            'category'       => $album->category,
            'photos'         => $album->_getAttachedPhotos($id),
            'album'          => $album,
            'authors'        => $this->getAuthors(),
            'commentsConfig' => $this->get('orm.manager')->getDataSet('Settings')
                ->get('comments_config'),
            'locale'         => $this->get('core.locale')
                ->getRequestLocale('frontend'),
            'tags'           => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($album->tag_ids)['items']
        ]);
    }

    /**
     * Updates the album information.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('ALBUM_MANAGER')
     *     and hasPermission('ALBUM_UPDATE')")
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

        if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
            && !$album->isOwner($this->getUser()->id)
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You don't have enough privileges for modify this album.")
            );

            return $this->redirect($this->generateUrl('admin_albums', [
                'category' => $album->category
            ]));
        }

        // Check empty data
        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Album data sent not valid.")
            );

            return $this->redirect($this->generateUrl('admin_album_show', [ 'id' => $id ]));
        }

        $requestPost = $request->request;

        $data = [
            'id'             => $id,
            'content_status' => $requestPost->getDigits('content_status', 0, FILTER_SANITIZE_STRING),
            'title'          => $requestPost->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'category'       => $requestPost->getDigits('category'),
            'agency'         => $requestPost
                ->filter('agency', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'description'    => $requestPost->get('description', ''),
            'with_comment'   => $requestPost->filter('with_comment', 0, FILTER_SANITIZE_STRING),
            'album_frontpage_image' => $requestPost->filter('album_frontpage_image', '', FILTER_SANITIZE_STRING),
            'album_photos_id'       => $requestPost->get('album_photos_id'),
            'album_photos_footer'   => $requestPost->get('album_photos_footer'),
            'fk_author'             => $requestPost->filter('fk_author', 0, FILTER_VALIDATE_INT),
            'endtime'        => $requestPost
                ->filter('endtime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'starttime'      => $requestPost
                ->filter('starttime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'params'         => $requestPost->get('params', []),
            'tag_ids'        => json_decode($request->request->get('tag_ids', ''), true)
        ];

        $album->update($data);
        $this->get('session')->getFlashBag()->add(
            'success',
            _("Album updated successfully.")
        );

        return $this->redirect($this->generateUrl('admin_album_show', [ 'id' => $album->id ]));
    }

    /**
     * Save positions for widget.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('ALBUM_MANAGER')
     *     and hasPermission('ALBUM_ADMIN')")
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
                $album  = new \Album($id);
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
     * @Security("hasExtension('ALBUM_MANAGER')")
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId         = $request->query->getDigits('category', 0);
        $page               = $request->query->getDigits('page', 1);
        $itemsPerPage       = 8;
        $frontpageVersionId =
            $request->query->getDigits('frontpage_version_id', null);
        $frontpageVersionId = $frontpageVersionId === '' ?
            null :
            $frontpageVersionId;

        $em  = $this->get('entity_repository');
        $ids = $this->get('api.service.frontpage_version')
            ->getContentIds((int) $categoryId, $frontpageVersionId, 'Album');

        $filters = [
            'content_type_name' => [ [ 'value' => 'album' ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ],
            'pk_content'        => [ [ 'value' => $ids, 'operator' => 'NOT IN' ] ]
        ];

        $countAlbums = true;
        $albums      = $em->findBy($filters, [ 'created' => 'desc' ], $itemsPerPage, $page, 0, $countAlbums);

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

        return $this->render('album/content-provider.tpl', [
            'albums'     => $albums,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Lists all the albums withing a category for the related manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('ALBUM_MANAGER')")
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = $this->get('orm.manager')->getDataSet('Settings')
            ->get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = [
            'content_type_name' => [['value' => 'album']],
            'in_litter'         => [['value' => 1, 'operator' => '!=']]
        ];

        if ($categoryId != 0) {
            $filters['category_name'] = [['value' => $category->name]];
        }

        $countAlbums = true;
        $albums      = $em->findBy($filters, ['created' => 'desc'], $itemsPerPage, $page, 0, $countAlbums);

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
            [
                'contentType'           => 'Album',
                'contents'              => $albums,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $categoryId,
                'pagination'            => $pagination,
                'contentProviderUrl'    => $this->generateUrl('admin_albums_content_provider_related'),
            ]
        );
    }

    /**
     * Handles and shows the album configuration form.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasPermission('ALBUM_SETTINGS')")
     *
     * @Security("hasExtension('ALBUM_MANAGER')")
     */
    public function configAction(Request $request)
    {
        $ds = $this->get('orm.manager')->getDataSet('Settings');

        if ('POST' !== $this->request->getMethod()) {
            return $this->render('album/config.tpl', [
                'configs' => $ds->get([ 'album_settings' ])
            ]);
        }

        $settings = [
            'album_settings' => [
                'total_widget'     => $request->request->getDigits('album_settings_total_widget'),
                'crop_width'       => $request->request->getDigits('album_settings_crop_width'),
                'crop_height'      => $request->request->getDigits('album_settings_crop_height'),
                'orderFrontpage'   => $request->request
                    ->filter('album_settings_orderFrontpage', '', FILTER_SANITIZE_STRING),
                'time_last'        => $request->request->getDigits('album_settings_time_last'),
                'total_front'      => $request->request->getDigits('album_settings_total_front'),
                'total_front_more' => $request->request->getDigits('album_settings_total_front_more'),
            ]
        ];


        try {
            $ds->set($settings);

            $type    = 'success';
            $message = _('Settings saved successfully.');
        } catch (\Exception $e) {
            $type    = 'error';
            $message = _('Unable to save the settings.');
        }

        $this->get('session')->getFlashBag()->add($type, $message);

        return $this->redirect($this->generateUrl('admin_albums_config'));
    }

    /**
     * Returns the list of authors.
     *
     * @return array The list of authors.
     */
    protected function getAuthors()
    {
        $response = $this->get('api.service.author')
            ->getList('order by name asc');

        return $this->get('data.manager.filter')
            ->set($response['items'])
            ->filter('mapify', [ 'key' => 'id'])
            ->get();
    }
}
