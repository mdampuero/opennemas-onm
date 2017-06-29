<?php
/**
 * Defines the frontend controller for the album content type
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for frontend albums
 *
 * @package Frontend_Controllers
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
        if (!$this->get('core.security')->hasExtension('ALBUM_MANAGER')) {
            throw new ResourceNotFoundException();
        }

        $this->ccm = new \ContentCategoryManager();
        $this->cm  = new \ContentManager();

        $this->categoryName = $this->request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $this->page         = $this->request->query->getDigits('page', 1);

        if (!empty($this->categoryName) && $this->categoryName != 'home') {
            $categoryManager = $this->get('category_repository');
            $category = $categoryManager->findBy(
                [ 'name' => [[ 'value' => $this->categoryName ]] ],
                'name ASC'
            );

            if (empty($category)) {
                throw new ResourceNotFoundException();
            }

            $category         = $category[0];
            $this->category   = $category->pk_content_category;

            $this->view->assign([
                'category_name'         => $this->categoryName ,
                'category'              => $category->pk_content_category,
                'actual_category_id'    => $category->pk_content_category,
                'actual_category_title' => $category->title,
                'category_real_name'    => $category->title,
                'category_data'         => $category,
            ]);
        } else {
            $this->category = 0;
            $this->view->assign([
                'actual_category_title' => 'Portada',
                'category_real_name'    => 'Portada',
            ]);
        }

        $this->view->assign('actual_category', $this->categoryName);
    }

    /**
     * Renders the album frontpage.
     *
     * @return Response          The response object.
     */
    public function frontpageAction()
    {
        // Setup templating cache layer
        $this->view->setConfig('gallery-frontpage');
        $cacheID = $this->view->getCacheId('frontpage', 'album', $this->categoryName, $this->page);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('album/album_frontpage.tpl', $cacheID))
        ) {
            $albumSettings = s::get('album_settings');
            $itemsPerPage  = isset($albumSettings['total_front']) ? $albumSettings['total_front'] : 8;
            $days          = isset($albumSettings['time_last']) ? $albumSettings['time_last'] : 4;
            $orderBy       = isset($albumSettings['orderFrontpage']) ? $albumSettings['orderFrontpage'] : 'created';

            $order = array();
            $filters = array(
                'content_type_name' => [[ 'value' => 'album' ]],
                'content_status'    => [[ 'value' => 1 ]],
                'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            );

            if ($this->category != 0) {
                $category = $this->get('category_repository')->find($this->category);
                $filters['category_name'] = [[ 'value' => $category->name ]];
            }

            if ($orderBy == 'favorite') {
                $order = [ 'favorite' => 'DESC', 'created' => 'DESC' ];
            } else {
                $order = [ 'created' => 'DESC' ];
            }

            $em          = $this->get('entity_repository');
            $albums      = $em->findBy($filters, $order, $itemsPerPage, $this->page);
            $countAlbums = $em->countBy($filters);


            $pagination = $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $itemsPerPage,
                'page'        => $this->page,
                'total'       => $countAlbums,
                'route'       => [
                    'name'   => 'frontend_album_frontpage_category',
                    'params' => ['category_name' => $this->categoryName]
                ]
            ]);
            $this->view->assign([
                'albums'     => $albums,
                'pagination' => $pagination,
            ]);
        }

        list($positions, $advertisements) = $this->getAds($this->category);

        // Send the response to the user
        return $this->render('album/album_frontpage.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheID,
            'x-tags'         => 'album-frontpage,'.$this->page
        ]);
    }

    /**
     * Shows an inner album
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->page   = $request->query->getDigits('page', 1);
        $dirtyID      = $request->query->filter('album_id', null, FILTER_SANITIZE_STRING);
        $urlSlug      = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);
        $itemsPerPage = 8; // Items_page refers to the widget

        $album = $this->get('content_url_matcher')
            ->matchContentUrl('album', $dirtyID, $urlSlug, $this->categoryName);

        if (empty($album)) {
            throw new ResourceNotFoundException();
        }

        $subscriptionFilter = new \Frontend\Filter\SubscriptionFilter($this->view, $this->getUser());
        $cacheable = $subscriptionFilter->subscriptionHook($album);

        // Setup templating cache layer
        $this->view->setConfig('gallery-inner');
        $cacheID = $this->view->getCacheId('content', $album->id);

        if (($this->view->getCaching() === 0)
            || (!$this->view->isCached('album/album.tpl', $cacheID))
        ) {
            // Get the other albums for the albums widget
            $settings = s::get('album_settings');
            $total    = isset($settings['total_front'])?($settings['total_front']):2;
            $days     = isset($settings['time_last'])?($settings['time_last']):4;

            $otherAlbums = $this->cm->findAll(
                'Album',
                'content_status=1 AND pk_content !='.$album->id
                .' AND `contents_categories`.`pk_fk_content_category` ='.$this->category
                .' AND created >=DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY) ',
                ' ORDER BY created DESC LIMIT '.$total
            );

            foreach ($otherAlbums as &$content) {
                $content->cover_image = $this->get('entity_repository')->find('Photo', $content->cover_id);

                $content->cover = is_object($content->cover_image)
                    ? $content->cover_image->path_file.$content->cover_image->name
                    : '';
                $content->category_name  = $content->loadCategoryName($content->id);
                $content->category_title = $content->loadCategoryTitle($content->id);
            }

            // Fetch album author
            $album->author = $this->get('user_repository')->find($album->fk_author);

            // Load category and photos
            $album->category_name  = $album->loadCategoryName($album->id);
            $album->category_title = $album->loadCategoryTitle($album->id);

            // TODO: Improve this.
            // In order to make subscription module to work remove the attached album photos when not cacheable
            $_albumArray           = (!isset($album->album_content_replaced))
                ? $album->_getAttachedPhotos($album->id) : null;
            $_albumArrayPaged      = (!isset($album->album_content_replaced))
                ? $album->getAttachedPhotosPaged($album->id, 8, $this->page) : null;

            if (count($_albumArrayPaged) > $itemsPerPage) {
                array_pop($_albumArrayPaged);
            }

            $this->view->assign([
                'album_photos'       => $_albumArray,
                'album_photos_paged' => $_albumArrayPaged,
                'items_page'         => $itemsPerPage,
                'gallerys'           => $otherAlbums,
            ]);
        } // END iscached

        list($positions, $advertisements) = $this->getAds($this->category, 'inner');

        return $this->render('album/album.tpl',[
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'album'          => $album,
            'content'        => $album,
            'page'           => $this->page,
            'cache_id'       => $cacheID,
            'contentId'      => $album->id,
            'x-tags'         => 'album,'.$album->id,
            'x-cache-for'    => '+1 day',
            'x-cacheable'    => $cacheable
        ]);
    }

    /**
     * Returns via ajax the interval photos in album page
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function ajaxPaginatedAction(Request $request)
    {
        // Items_page refers to the widget
        $albumID   = $request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $page      = $request->query->filter('page', 1, FILTER_VALIDATE_INT);
        $itemsPage = 8;

        // Redirect to album frontpage if id_album wasn't provided
        if (is_null($albumID)) {
            return new RedirectResponse($this->generateUrl('frontend_album_frontpage'));
        }

        // Get the album from the id and increment the numviews for it
        $album = $this->get('entity_repository')->find('Album', $albumID);

        $album->category_name  = $album->loadCategoryName($album->id);
        $album->category_title = $album->loadCategoryTitle($album->id);
        $albumPhotos           = $album->_getAttachedPhotos($album->id);
        $albumPhotosPaged      = $album->getAttachedPhotosPaged($album->id, 8, $page);

        if (count($_albumArrayPaged) > $itemsPage) {
            array_pop($_albumArrayPaged);
        }

        return $this->render('album/partials/_gallery_thumbs.tpl', [
            'album_photos'       => $albumPhotos,
            'album_photos_paged' => $albumPhotosPaged,
            'page'               => $page,
            'items_page'         => $itemsPage,
            'album'              => $album,
        ]);
    }

    /**
     * Returns via ajax the albums of the category in a page
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function ajaxAlbumPaginatedAction(Request $request)
    {
        // Fetch album settings
        $albumSettings = s::get('album_settings');
        $totalAlbumMoreFrontpage   = isset($albumSettings['total_front_more'])?$albumSettings['total_front_more']:6;

        if (empty($this->category)) {
            $this->category = $request->query->getDigits('category', 0);
        }

        $order = array('created' => 'DESC');
        $filters = array(
            'content_type_name' => array(array('value' => 'album')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
        );

        if ($this->category != 0) {
            $category = $this->get('category_repository')->find($this->category);
            $filters['category_name'] = array(array('value' => $category->name));
        }

        $em           = $this->get('entity_repository');
        $othersAlbums = $em->findBy($filters, $order, $totalAlbumMoreFrontpage, $this->page);
        $countAlbums  = $em->countBy($filters);

        if ($countAlbums == 0) {
            return new RedirectResponse(
                $this->generateUrl('frontend_album_ajax_paginated')
            );
        }

        $pagination = $this->get('paginator')->get([
            'boundary'    => false,
            'directional' => true,
            'maxLinks'    => 0,
            'epp'         => $totalAlbumMoreFrontpage,
            'page'        => $this->page,
            'total'       => count($othersAlbums)+1,
            'route'       => [
                'name'   => 'frontend_album_ajax_paginated',
                'params' => ['category' => $this->category]
            ]
        ]);

        return $this->render('album/partials/_widget_more_albums.tpl',[
            'others_albums'      => $othersAlbums,
            'page'               => $this->page,
           'pagination'         => $pagination,
        ]);
    }

    /**
     * Fetches the ads given a category and page
     *
     * @param mixed $category the category to fetch ads from
     * @param string $page the page to fetch ads from
     *
     * @return array the list of advertisements for this page
     **/
    private function getAds($category = 'home', $page = '')
    {
        $category = (!isset($category) || ($category == 'home'))? 0: $category;

        // Get album_inner positions
        $positionManager = getService('core.helper.advertisement');
        if ($page == 'inner') {
            $positions = $positionManager->getPositionsForGroup('album_inner', [ 7 ]);
        } else {
            $positions = $positionManager->getPositionsForGroup('album_frontpage', [ 7, 9 ]);
        }

        $advertisements = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
    }
}
