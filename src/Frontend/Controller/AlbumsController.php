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
use Onm\Message as m;
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

        // Setting up available categories for menu.
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
                throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
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
     * @param  Request  $request The request object.
     *
     * @return Response          The response object.
     */
    public function frontpageAction(Request $request)
    {
        // Setup caching system
        $this->view->setConfig('gallery-frontpage');

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

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

            $pagination = \Onm\Pager\SimplePager::getPagerUrl(
                array(
                    'page'  => $this->page,
                    'items' => $itemsPerPage,
                    'total' => $countAlbums,
                    'url'   => $this->generateUrl(
                        'frontend_album_frontpage_category',
                        array(
                            'category_name' => $this->categoryName
                        )
                    )
                )
            );
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
                'cache_id' => $cacheID,
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

        // Items_page refers to the widget
        $dirtyID    = $request->query->filter('album_id', null, FILTER_SANITIZE_STRING);
        $albumID    = \Content::resolveID($dirtyID);
        $itemsPerPage = 8;

        // Redirect to album frontpage if id_album wasn't provided
        if (is_null($albumID)) {
            return new RedirectResponse($this->generateUrl('frontend_album_frontpage'));
        }

        $this->view->setConfig('gallery-inner');

        // Load advertisement for this action
        $ads = $this->getAds('inner');
        $this->view->assign('advertisements', $ads);

        $cacheID = $this->view->generateCacheId($this->categoryName, null, $albumID);
        if (($this->view->caching == 0)
            || (!$this->view->isCached('album/album.tpl', $cacheID))
        ) {
            // Get the album from the id
            $album = $this->get('entity_repository')->find('Album', $albumID);

            // Show the album only if it is properly published
            if (($album->content_status == 1) && ($album->in_litter == 0)) {
                $this->view->assign('album', $album);
                $album->with_comment = 1;

                // Get the other albums for the albums widget
                $settings = s::get('album_settings');
                $total    = isset($settings['total_front'])?($settings['total_front']):2;
                $days     = isset($settings['time_last'])?($settings['time_last']):4;

                $otherAlbums = $this->cm->find(
                    'Album',
                    'content_status=1 AND pk_content !='.$albumID
                    .' AND created >=DATE_SUB(CURDATE(), INTERVAL '.$days.' DAY) ',
                    ' ORDER BY views DESC,  created DESC LIMIT '.$total
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
                $_albumArray           = $album->_getAttachedPhotos($album->id);
                $_albumArrayPaged      = $album->getAttachedPhotosPaged($album->id, 8, $this->page);

                if (count($_albumArrayPaged) > $itemsPerPage) {
                    array_pop($_albumArrayPaged);
                }
            } else {
                throw new ResourceNotFoundException();
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
                'cache_id'  => $cacheID,
                'contentId' => $albumID
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
            $this->category = $this->request->query->getDigits('category', 0);
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

        $pagination = \Onm\Pager\SimplePager::getPagerUrl(
            array(
                'page'  => $this->page,
                'items' => $totalAlbumMoreFrontpage,
                'total' => count($othersAlbums)+1,
                'url'   => $this->generateUrl(
                    'frontend_album_ajax_paginated',
                    array(
                        'category' => $this->category
                    )
                )
            )
        );

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
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        if ($position == 'inner') {
            $positions = $positionManager->getAdsPositionsForGroup('album_inner', array(7, 9));
        } else {
            $positions = $positionManager->getAdsPositionsForGroup('album_frontpage', array(7, 9));
        }

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
