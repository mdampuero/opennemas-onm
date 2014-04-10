<?php
/**
 * Handles the actions for frontend albums
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
     * Renders the album frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {

        // Setup caching system
        $this->view->setConfig('gallery-frontpage');
        $cacheID = $this->view->generateCacheId($this->categoryName, '', $this->page);

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        // Don't execute the action logic if was cached before
        if (($this->view->caching == 0)
           || (!$this->view->isCached('gallery/gallery-frontpage.tpl', $cacheID))
        ) {
            $albumSettings = s::get('album_settings');
            $itemsPerPage = isset($albumSettings['total_front']) ? $albumSettings['total_front'] : 8;
            $days  = isset($albumSettings['time_last']) ? $albumSettings['time_last'] : 4;
            $order = isset($albumSettings['orderFrontpage']) ? $albumSettings['orderFrontpage'] : 'created';

            if ($order == 'favorite') {

                list($countAlbums, $albums)= $this->cm->getCountAndSlice(
                    'Album',
                    (int) $this->category,
                    'in_litter != 1 AND contents.available=1',
                    'ORDER BY favorite DESC, created DESC',
                    $this->page,
                    $itemsPerPage
                );

            } elseif ($order == 'views') {

                list($countAlbums, $albums)= $this->cm->getCountAndSlice(
                    'Album',
                    (int) $this->category,
                    'in_litter != 1 AND contents.available=1 '
                    .' AND created >=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)',
                    'ORDER BY views DESC, created DESC',
                    $this->page,
                    $itemsPerPage
                );
            } else {
                list($countAlbums, $albums)= $this->cm->getCountAndSlice(
                    'Album',
                    (int) $this->category,
                    'in_litter != 1 AND contents.available=1',
                    'ORDER BY created DESC',
                    $this->page,
                    $itemsPerPage
                );
            }


            $total = count($albums)+1;

            $pagination = \Onm\Pager\SimplePager::getPagerUrl(
                array(
                    'page'  => $this->page,
                    'items' => $itemsPerPage,
                    'total' => $total,
                    'url'   => $this->generateUrl(
                        'frontend_album_frontpage_category',
                        array(
                            'category_name' => $this->categoryName
                        )
                    )
                )
            );

            foreach ($albums as &$album) {
                $album->cover_image = new \Photo($album->cover_id);
                $album->cover       = $album->cover_image->path_file.$album->cover_image->name;
            }
        }

        // Send the response to the user
        return $this->render(
            'album/album_frontpage.tpl',
            array(
                'cache_id' => $cacheID,
                'albums'              => $albums,
                'pagination'            => $pagination,
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
            || (!$this->view->isCached('gallery/gallery.tpl', $cacheID))
        ) {
            // Get the album from the id and increment the numviews for it
            $album = new \Album($albumID);
            if (($album->available == 1) && ($album->in_litter == 0)) {
                $this->view->assign('album', $album);
                $album->with_comment = 1;

                // Get the other albums for the albums widget
                $settings = s::get('album_settings');
                $total    = isset($settings['total_front'])?($settings['total_front']):2;
                $days     = isset($settings['time_last'])?($settings['time_last']):4;

                $otherAlbums = $this->cm->find(
                    'Album',
                    'available=1 AND pk_content !='.$albumID
                    .' AND created >=DATE_SUB(CURDATE(), INTERVAL '
                    . $days . ' DAY) ',
                    ' ORDER BY views DESC,  created DESC LIMIT '.$total
                );

                foreach ($otherAlbums as &$content) {
                    $content->cover_image    = new \Photo($content->cover_id);
                    $content->cover          = $content->cover_image->path_file.$content->cover_image->name;
                    $content->category_name  = $content->loadCategoryName($content->id);
                    $content->category_title = $content->loadCategoryTitle($content->id);
                }

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
        $album = new \Album($albumID);

        $album->category_name  = $album->loadCategoryName($album->id);
        $album->category_title = $album->loadCategoryTitle($album->id);
        $_albumArray           = $album->_getAttachedPhotos($album->id);
        $_albumArrayPaged      = $album->getAttachedPhotosPaged($album->id, 8, $page);

        if (count($_albumArrayPaged) > $itemsPage) {
            array_pop($_albumArrayPaged);
        }

        return $this->render(
            'album/partials/_gallery_thumbs.tpl',
            array(
                'album_photos'       => $_albumArray,
                'album_photos_paged' => $_albumArrayPaged,
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

        // Fetch Albums paginated
        list($countAlbums, $othersAlbums)= $this->cm->getCountAndSlice(
            'Album',
            (int) $this->category,
            'in_litter != 1 AND contents.available=1',
            'ORDER BY created DESC',
            $this->page,
            $totalAlbumMoreFrontpage
        );

        if ($countAlbums > 0) {
            foreach ($othersAlbums as &$album) {
                $album->category_name  = $album->loadCategoryName($album->id);
                $album->category_title = $album->loadCategoryTitle($album->id);
                $album->cover_image    = new \Photo($album->cover_id);
                $album->cover          = $album->cover_image->path_file.$album->cover_image->name;
            }
        } else {
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
