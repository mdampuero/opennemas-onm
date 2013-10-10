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
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('ALBUM_MANAGER');

         // Check if the user can admin album
        $this->checkAclOrForward('ALBUM_ADMIN');

        $request = $this->get('request');

        $contentType = \ContentManager::getContentTypeIdFromName('album');

        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->categoryData)
            = $this->ccm->getArraysMenu($category, $contentType);

        $this->view->assign(
            array(
                'category'     => $category,
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                'datos_cat'    => $this->categoryData
            )
        );
    }

    /**
     * Lists all albums
     *
     * @param Request $request the request object
     *
     * @return void
     **/
    public function listAction(Request $request)
    {
        $itemsPerPage = s::get('items_per_page');

        $page           = $this->get('request')->query->getDigits('page', 1);
        $category       = $this->get('request')->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $cm = new \ContentManager();

        if ($category == 'all') {
            $categoryForLimit = null;
        } else {
            $categoryForLimit = $category;
        }

        list($albumCount, $albums) = $cm->getCountAndSlice(
            'album',
            $categoryForLimit,
            'contents.in_litter!=1',
            'ORDER BY created DESC',
            $page,
            $itemsPerPage
        );

        if (count($albums) > 0) {
            foreach ($albums as &$album) {
                $album->category_name  = $this->ccm->get_name($album->category);
                $album->category_title = $this->ccm->get_title($album->category_name);
                $album->read($album->id);
            }
        }

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
                'totalItems'  => $albumCount,
                'fileName'    => $this->generateUrl(
                    'admin_albums',
                    array('category' => $category)
                ).'&page=%d',
            )
        );

        return $this->render(
            'album/list.tpl',
            array(
                'pagination' => $pagination,
                'albums'     => $albums,
                'page'       => $page,
            )
        );
    }

    /**
     * Lists all the albums for the widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function widgetAction(Request $request)
    {
        $page           = $this->get('request')->query->getDigits('page', 1);
        $category       = $this->get('request')->query->filter('category', 'widget', FILTER_SANITIZE_STRING);

        $configurations = s::get('album_settings');
        $numFavorites = $configurations['total_widget'];

        $itemsPerPage = s::get('items_per_page');

        $cm = new \ContentManager();

        $categoryForLimit = null;

        list($albumCount, $albums) = $cm->getCountAndSlice(
            'album',
            $categoryForLimit,
            'in_home =1 AND available =1 AND contents.in_litter !=1',
            'ORDER BY position ASC, created DESC',
            $page,
            $itemsPerPage
        );

        if (count($albums) != $numFavorites) {
            m::add(sprintf(_("You must put %d albums in the HOME widget"), $numFavorites));
        }

        if (count($albums) > 0) {
            foreach ($albums as &$album) {
                $album->category_name  = $this->ccm->get_name($album->category);
                $album->category_title = $this->ccm->get_title($album->category_name);
                $album->read($album->id);
            }
        }

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
                'totalItems'  => $albumCount,
                'fileName'    => $this->generateUrl(
                    'admin_albums',
                    array('category' => $category)
                ).'&page=%d',
            )
        );

        return $this->render(
            'album/list.tpl',
            array(
                'pagination' => $pagination,
                'albums'     => $albums,
                'category'   => $category,
                'page'       => $page,
            )
        );
    }

    /**
     * Shows and handles the form for create a new album
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('ALBUM_CREATE');

        if ('POST' == $this->request->getMethod()) {
            $request  = $this->request->request;

            $type     = $request->filter('type', null, FILTER_SANITIZE_STRING);
            $page     = $request->getDigits('page', 1);
            $category = $request->getDigits('category');
            $continue = $request->filter('continue', false, FILTER_SANITIZE_STRING);

            $album = new \Album();
            $album->create($_POST);
            m::add(_('Album created successfully'), m::SUCCESS);

            // Get category name
            $ccm = \ContentCategoryManager::get_instance();
            $categoryName = $ccm->get_name($category);

            // Clean cache album home and frontpage for category
            $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName).'|1');
            $tplManager->delete('home|1');

            if ($continue) {
                return $this->redirect(
                    $this->generateUrl('admin_album_show', array('id' => $album->id))
                );
            } else {
                $page = $this->request->request->getDigits('page', 1);

                return $this->redirect(
                    $this->generateUrl(
                        'admin_albums',
                        array(
                            'category' => $album->category,
                            'page'     => $page,
                        )
                    )
                );
            }

            return $this->redirect(
                $this->generateUrl(
                    'admin_albums',
                    array(
                        'category' => $category,
                        'page'     => $page
                    )
                )
            );
        } else {

            $authorsComplete = \User::getAllUsersAuthors();
            $authors = array( '0' => _(' - Select one author - '));
            foreach ($authorsComplete as $author) {
                $authors[$author->id] = $author->name;
            }

            return $this->render(
                'album/new.tpl',
                array ( 'authors' => $authors, )
            );
        }
    }

    /**
     * Deletes an album given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('ALBUM_DELETE');

        $request = $this->get('request');
        $id      = $request->query->getDigits('id');
        $page    = $request->query->getDigits('page', 1);

        $album = new \Album($id);

        if (is_null($album->id)) {
            m::add(sprintf(_('Unable to find an album with the id "%d".'), $id), m::ERROR);
        }

        $rel= new \RelatedContent();
        $rel->deleteAll($id);
        $album->delete($id, $_SESSION['userid']);

        m::add(_('Album delete successfully.'), m::SUCCESS);

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
     * Shows the information for an album given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('ALBUM_UPDATE');

        $request = $this->get('request');
        $id = $request->query->getDigits('id');

        $album = new \Album($id);

        if (is_null($album->id)) {
            m::add(sprintf(_('Unable to find the album with the id "%d"'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_albums'));
        }

        $photos = array();
        $photos = $album->_getAttachedPhotos($id);

        $authorsComplete = \User::getAllUsersAuthors();
        $authors = array( '0' => _(' - Select one author - '));
        foreach ($authorsComplete as $author) {
            $authors[$author->id] = $author->name;
        }


        return $this->render(
            'album/new.tpl',
            array(
                'category' => $album->category,
                'photos'   => $photos,
                'album'    => $album,
                'authors'  => $authors,
            )
        );
    }

    /**
     * Updates the album information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('ALBUM_UPDATE');

        $request  = $this->get('request');
        $id       = $request->request->getDigits('id');
        $continue = $this->request->request->filter('continue', false, FILTER_SANITIZE_STRING);

        $album = new \Album($id);

        if (is_null($album->id)) {
            m::add(sprintf(_('Unable to find the album with the id "%d"'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_albums'));
        }

        if (!\Acl::isAdmin()
            && !\Acl::check('CONTENT_OTHER_UPDATE')
            && $album->fk_user != $_SESSION['userid']
        ) {
            m::add(_("You don't have enought privileges for modify this album."), m::SUCCESS);

            return $this->redirect(
                $this->generateUrl(
                    'admin_albums',
                    array('category' => $album->category,)
                )
            );
        } else {
            // Check empty data
            if (count($request->request) < 1) {
                m::add(_("Album data sent not valid."), m::ERROR);

                return $this->redirect($this->generateUrl('admin_album_show', array('id' => $id)));
            }

            $data = array(
                'id'          => $id,
                'available'   => $request->request->getDigits('available', 0, FILTER_SANITIZE_STRING),
                'title'       => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'category'    => $request->request->getDigits('category'),
                'agency'      => $request->request->filter('agency', '', FILTER_SANITIZE_STRING),
                'description' => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
                'metadata'    => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'album_frontpage_image' =>
                    $request->request->filter('album_frontpage_image', '', FILTER_SANITIZE_STRING),
                'album_photos_id'       => $request->request->get('album_photos_id'),
                'album_photos_footer'   => $request->request->get('album_photos_footer'),
                'fk_author'             => $request->request->filter('fk_author', 0, FILTER_VALIDATE_INT),
            );

            $album->update($data);
            m::add(_("Album updated successfully."), m::SUCCESS);

            $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $album->category_name).'|'.$album->id);
            $tplManager->delete('home|1');

            if ($continue) {
                return $this->redirect(
                    $this->generateUrl('admin_album_show', array('id' => $album->id))
                );
            } else {
                $page = $this->request->request->getDigits('page', 1);

                return $this->redirect(
                    $this->generateUrl(
                        'admin_albums',
                        array(
                            'category' => $album->category,
                            'page'     => $page,
                        )
                    )
                );
            }
        }
    }

    /**
     * Change availability for one video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $this->checkAclOrForward('ALBUM_AVAILABLE');

        $request  = $this->get('request');
        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 0);
        $category = $request->query->get('category', 'all');

        $album = new \Album($id);
        if (is_null($album->id)) {
            m::add(sprintf(_('Unable to find album with id "%d"'), $id), m::ERROR);
        } else {
            $album->toggleAvailable($album->id);
            if ($status == 0) {
                $album->set_favorite($status);
            }
            m::add(sprintf(_('Successfully changed availability for album with id "%d"'), $id), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_albums',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Change suggested flag for one video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleFavoriteAction(Request $request)
    {
        $this->checkAclOrForward('ALBUM_AVAILABLE');

        $request  = $this->get('request');
        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 0);
        $category = $request->query->get('category', 'all');

        $album = new \Album($id);
        if (is_null($album->id)) {
            m::add(sprintf(_('Unable to find album with id "%d"'), $id), m::ERROR);
        } else {

            $album->set_favorite($status);
            m::add(sprintf(_('Successfully changed suggested flag for album with id "%d"'), $id), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_albums',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Change in_home flag for one album given its id
     * Used for putting this content widgets in home
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleInHomeAction(Request $request)
    {
        $this->checkAclOrForward('ALBUM_AVAILABLE');

        $request  = $this->get('request');
        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $page     = $request->query->getDigits('page', 0);
        $category = $request->query->get('category', 'all');

        $album = new \Album($id);
        if (is_null($album->id)) {
            m::add(sprintf(_('Unable to find album with id "%d"'), $id), m::ERROR);
        } else {
            $album->set_inhome($status, $_SESSION['userid']);
            m::add(sprintf(_('Successfully changed suggested flag for album with id "%d"'), $id), m::SUCCESS);
        }

        if ($category == 'widget') {
            return $this->redirect(
                $this->generateUrl(
                    'admin_albums_widget'
                )
            );
        }
        return $this->redirect(
            $this->generateUrl(
                'admin_albums',
                array(
                    'category' => $category,
                    'page'     => $page
                )
            )
        );
    }

    /**
     * Deletes multiple videos at once given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('ALBUM_DELETE');

        $request       = $this->request;
        $category      = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page          = $request->query->getDigits('page', 1);
        $selectedItems = $request->query->get('selected_fld');

        if (is_array($selectedItems)
            && count($selectedItems) > 0
        ) {
            foreach ($selectedItems as $element) {
                $album = new \Album($element);

                $relations = array();
                $relations = \RelatedContent::getContentRelations($element);

                $album->delete($element, $_SESSION['userid']);

                m::add(sprintf(_('Album "%s" deleted successfully.'), $album->title), m::SUCCESS);
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_albums',
                array(
                    'categoy' => $category,
                    'page'    => $page,
                )
            )
        );
    }

    /**
     * Batch set the published flag for contents
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchPublishAction(Request $request)
    {
        $this->checkAclOrForward('ALBUM_AVAILABLE');

        $request  = $this->request;
        $status   = $request->query->getDigits('status', 0);
        $selected = $request->query->get('selected_fld', null);
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            foreach ($selected as $id) {
                $album = new \Album($id);
                $album->set_available($status, $_SESSION['userid']);
                if ($status == 0) {
                    $album->set_favorite($status, $_SESSION['userid']);
                }
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_albums',
                array(
                    'category' => $category,
                    'page'     => $page,
                )
            )
        );
    }

    /**
     * Save positions for widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function savePositionsAction(Request $request)
    {
        $request = $this->get('request');

        $positions = $request->query->get('positions');

        $msg = '';
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $_positions = array();

            foreach ($positions as $pos => $id) {
                $_positions[] = array($pos, '1', $id);
            }

            $album = new \Album();
            $msg = $album->set_position($_positions, $_SESSION['userid']);
        }

        if ($msg) {
            $msg = "<div class='alert alert-success'>"
                ._("Positions saved successfully.")
                .'<button data-dismiss="alert" class="close">×</button></div>';
        } else {
            $msg = "<div class='alert alert-error'>"
                ._("Unable to save the new positions. Please contact with your system administrator.")
                .'<button data-dismiss="alert" class="close">×</button></div>';
        }

        return new Response($msg);
    }

    /**
     * Render the content provider for albums
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderAction(Request $request)
    {
        $request      = $this->get('request');
        $category     = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page');

        if ($category == 'home') {
            $category = 0;
        }

        $cm = new  \ContentManager();

        // Get contents for this home
        $contentElementsInFrontpage  = $cm->getContentsIdsForHomepageOfCategory($category);

        // Fetching opinions
        $sqlExcludedOpinions = '';
        if (count($contentElementsInFrontpage) > 0) {
            $contentsExcluded    = implode(', ', $contentElementsInFrontpage);
            $sqlExcludedOpinions = ' AND `pk_album` NOT IN ('.$contentsExcluded.') ';
        }

        list($countAlbums, $albums) = $cm->getCountAndSlice(
            'Album',
            null,
            'contents.available=1 '.$sqlExcludedOpinions,
            'ORDER BY created DESC ',
            $page,
            8
        );

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
                    array('category' => $category)
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
     * Lists all the albums withing a category for the related manager
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderRelatedAction(Request $request)
    {
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        if ($category == 0) {
            $categoryFilter = null;
        } else {
            $categoryFilter = $category;
        }
        $cm = new  \ContentManager();

        list($countAlbums, $albums) = $cm->getCountAndSlice(
            'Album',
            $categoryFilter,
            '1=1',
            ' ORDER BY created DESC, contents.title ASC ',
            $page,
            $itemsPerPage
        );

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
                    array( 'category' => $category,)
                ).'&page=%d',
            )
        );

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            array(
                'contentType'           => 'Album',
                'contents'              => $albums,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $category,
                'pagination'            => $pagination->links,
                'contentProviderUrl'    => $this->generateUrl('admin_albums_content_provider_related'),
            )
        );
    }

    /**
     * Handles and shows the album configuration form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        $this->checkAclOrForward('ALBUM_SETTINGS');

        if ('POST' == $this->request->getMethod()) {

            $formValues = $this->get('request')->request;

            $settings = array(
                'album_settings' => array(
                    'total_widget'   => $formValues->getDigits('album_settings_total_widget'),
                    'crop_width'     => $formValues->getDigits('album_settings_crop_width'),
                    'crop_height'    => $formValues->getDigits('album_settings_crop_height'),
                    'orderFrontpage' =>
                        $formValues->filter('album_settings_orderFrontpage', '', FILTER_SANITIZE_STRING),
                    'time_last'      => $formValues->getDigits('album_settings_time_last'),
                    'total_front'    => $formValues->getDigits('album_settings_total_front'),
                )
            );

            foreach ($settings as $key => $value) {
                s::set($key, $value);
            }

            m::add(_('Settings saved successfully.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_albums_config'));
        } else {
            $configurationsKeys = array('album_settings',);
            $configurations = s::get($configurationsKeys);

            return $this->render(
                'album/config.tpl',
                array('configs'   => $configurations,)
            );
        }
    }
}
