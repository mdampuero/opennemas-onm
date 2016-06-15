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
use Onm\Framework\Controller\Controller;
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
        if (!\Onm\Module\ModuleManager::isActivated('ALBUM_MANAGER')) {
            throw new ResourceNotFoundException();
        }

        $this->view = new \Template(TEMPLATE_USER);

        $this->ccm = new \ContentCategoryManager();
        $this->cm  = new \ContentManager();

        $this->categoryName = $this->request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $this->page         = $this->request->query->getDigits('page', 1);

        if (!empty($this->categoryName) && $this->categoryName != 'home') {
            $categoryManager = $this->get('category_repository');
            $category = $categoryManager->findBy(
                array('name' => array(array('value' => $this->categoryName))),
                'name ASC'
            );

            if (empty($category)) {
                throw new ResourceNotFoundException();
            }

            $category         = $category[0];
            $categoryRealName = $category->title;
            $this->category   = $category->pk_content_category;

            $this->view->assign(
                array(
                    'category_name'         => $this->categoryName ,
                    'category'              => $category->pk_content_category,
                    'actual_category_id'    => $category->pk_content_category,
                    'actual_category_title' => $categoryRealName,
                    'category_real_name'    => $categoryRealName ,
                )
            );
        } else {
            $categoryRealName = 'Portada';
            $this->category   = 0;
            $this->view->assign(
                array(
                    'actual_category_title' => $categoryRealName,
                    'category_real_name'    => $categoryRealName,
                )
            );
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
        // Setup caching system
        $this->view->setConfig('gallery-frontpage');

        // Don't execute the action logic if was cached before
        $cacheID = $this->view->generateCacheId($this->categoryName, '', $this->page);
        if (($this->view->caching == 0)
           || (!$this->view->isCached('album/album_frontpage.tpl', $cacheID))
        ) {
            $albumSettings = s::get('album_settings');
            $itemsPerPage  = isset($albumSettings['total_front']) ? $albumSettings['total_front'] : 8;
            $days          = isset($albumSettings['time_last']) ? $albumSettings['time_last'] : 4;
            $orderBy       = isset($albumSettings['orderFrontpage']) ? $albumSettings['orderFrontpage'] : 'created';

            $order = array();
            $filters = array(
                'content_type_name' => array(array('value' => 'album')),
                'content_status'    => array(array('value' => 1)),
                'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
            );

            if ($this->category != 0) {
                $category = $this->get('category_repository')->find($this->category);
                $filters['category_name'] = array(array('value' => $category->name));
            }

            if ($orderBy == 'favorite') {
                $order = array('favorite' => 'DESC', 'created' => 'DESC');
            } elseif ($orderBy == 'views') {
                $order = array('views' => 'DESC', 'created' => 'DESC');

                $date = strtotime("-$days day");
                $filters['created'] = array(array('value' => date('Y-m-d H:i:s', $date), 'operator' => '>=' ));
            } else {
                $order = array('created' => 'DESC');
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
            $this->view->assign(
                array(
                    'albums'     => $albums,
                    'pagination' => $pagination,
                )
            );
        }

        // Send the response to the user
        return $this->render(
            'album/album_frontpage.tpl',
            array(
                'cache_id'       => $cacheID,
                'advertisements' => $this->getAds(),
                'x-tags'         => 'album-frontpage,'.$this->page
            )
        );
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
        $this->page = $request->query->getDigits('page', 1);
        $dirtyID    = $request->query->filter('album_id', null, FILTER_SANITIZE_STRING);
        $urlSlug      = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        $album = $this->get('content_url_matcher')
            ->matchContentUrl('album', $dirtyID, $urlSlug, $this->categoryName);

        if (empty($album)) {
            throw new ResourceNotFoundException();
        }

        $this->view->setConfig('gallery-inner');

        $subscriptionFilter = new \Frontend\Filter\SubscriptionFilter($this->view, $this->getUser());
        $cacheable = $subscriptionFilter->subscriptionHook($album);

        // Items_page refers to the widget
        $itemsPerPage = 8;

        $cacheID = $this->view->generateCacheId($this->categoryName, null, $album->id);
        if (($this->view->caching == 0)
            || (!$this->view->isCached('album/album.tpl', $cacheID))
        ) {
            $album->with_comment = 1;

            // Get the other albums for the albums widget
            $settings = s::get('album_settings');
            $total    = isset($settings['total_front'])?($settings['total_front']):2;
            $days     = isset($settings['time_last'])?($settings['time_last']):4;

            $otherAlbums = $this->cm->findAll(
                'Album',
                'content_status=1 AND pk_content !='.$album->id.' AND `contents_categories`.`pk_fk_content_category` ='
                . $this->category . ' AND created >=DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY) ',
                ' ORDER BY created DESC LIMIT '.$total
            );

            foreach ($otherAlbums as &$content) {
                $content->cover_image    = $this->get('entity_repository')->find('Photo', $content->cover_id);
                $content->cover          = $content->cover_image->path_file.$content->cover_image->name;
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

            $this->view->assign(
                array(
                    'album'              => $album,
                    'content'            => $album,
                    'album_photos'       => $_albumArray,
                    'album_photos_paged' => $_albumArrayPaged,
                    'page'               => $this->page,
                    'items_page'         => $itemsPerPage,
                    'gallerys'           => $otherAlbums,
                )
            );
        } // END iscached

        return $this->render(
            'album/album.tpl',
            array(
                'cache_id'       => $cacheID,
                'contentId'      => $album->id,
                'advertisements' => $this->getAds('inner'),
                'x-tags'         => 'album,'.$album->id,
                'x-cache-for'    => '+1 day',
                'x-cacheable'    => $cacheable
            )
        );
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

        return $this->render(
            'album/partials/_gallery_thumbs.tpl',
            array(
                'album_photos'       => $albumPhotos,
                'album_photos_paged' => $albumPhotosPaged,
                'page'               => $page,
                'items_page'         => $itemsPage,
                'album'              => $album,
            )
        );
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

        return $this->render(
            'album/partials/_widget_more_albums.tpl',
            array(
                'others_albums'      => $othersAlbums,
                'page'               => $this->page,
                'pagination'         => $pagination,
            )
        );
    }

    /**
     * Retrieves the advertisement for the frontpage
     *
     * @param string $categoryName the category name where fetch ads from
     *
     * @return void
     **/
    public static function getAds($position = '')
    {
        $ccm = \ContentCategoryManager::get_instance();
        $categoryName = 'album';
        $category = $ccm->get_id($categoryName);

        // Get album_inner positions
        $positionManager = getService('core.manager.advertisement');
        if ($position == 'inner') {
            $positions = $positionManager->getPositionsForGroup('album_inner', array(7, 9));
        } else {
            $positions = $positionManager->getPositionsForGroup('album_frontpage', array(7, 9));
        }

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
