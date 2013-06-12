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
namespace Frontend\Controllers;


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
        $subcategoryName    = null;
        $action             = $this->request->query->filter('action', 'frontpage', FILTER_SANITIZE_STRING);

        if (!empty($this->categoryName)) {
            $category = $this->ccm->get_id($this->categoryName);
            $actual_category_id = $category; // FOR WIDGETS
            $categoryRealName = $this->ccm->get_title($this->categoryName); //used in title
            $this->view->assign(
                array(
                    'category_name'         => $this->categoryName ,
                    'category'              => $category ,
                    'actual_category_id'    => $actual_category_id ,
                    'actual_category_title' => $categoryRealName,
                    'category_real_name'    => $categoryRealName ,
                )
            );
        } else {
            $categoryRealName = 'Portada';
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
        $this->page = $request->query->getDigits('page', 1);

        // Setup caching system
        $this->view->setConfig('gallery-frontpage');
        $cacheID = $this->view->generateCacheId($this->categoryName, '', $this->page);

        $ads = $this->getAds();
        $this->view->assign('advertisments', $ads);

        // Don't execute the action logic if was cached before
        if (($this->view->caching == 0)
           || (!$this->view->isCached('gallery/gallery-frontpage.tpl', $cacheID))
        ) {
            $albumSettings = s::get('album_settings');
            $total = isset($albumSettings['total_front']) ? $albumSettings['total_front'] : 2;
            $days  = isset($albumSettings['time_last']) ? $albumSettings['time_last'] : 4;
            $order = isset($albumSettings['orderFrontpage']) ? $albumSettings['orderFrontpage'] : 'views';
            $category = $this->ccm->get_id($this->categoryName);
            if (isset($category)
                && !empty($category)
            ) {
                $albums = $this->cm->find_by_category(
                    'Album',
                    $category,
                    'fk_content_type=7 AND available=1',
                    'ORDER BY  created DESC LIMIT '.$total
                );
            } else {
                if ($order == 'favorite') {
                    $albums = $this->cm->find(
                        'Album',
                        'fk_content_type=7 AND available=1 ',
                        ' ORDER BY favorite DESC,  created DESC LIMIT '.$total
                    );
                } else {
                    $albums = $this->cm->find(
                        'Album',
                        'fk_content_type=7 AND available=1 AND '.
                        'created >=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)  ',
                        ' ORDER BY views DESC,  created DESC LIMIT '.$total
                    );
                }
            }

            foreach ($albums as &$album) {
                $album->cover_image = new \Photo($album->cover_id);
                $album->cover       = $album->cover_image->path_file.$album->cover_image->name;
            }

            $this->view->assign('albums', $albums);
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
        $ads = $this->getAds('innner');
        $this->view->assign('advertisments', $ads);


        $cacheID = $this->view->generateCacheId($this->categoryName, null, $albumID);
        if (($this->view->caching == 0)
            || (!$this->view->isCached('gallery/gallery.tpl', $cacheID))
        ) {
            // Get the album from the id and increment the numviews for it
            $album = new \Album($albumID);
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
     * Returns the
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

        // I have added the element 450 in order to integrate interstitial position
        if ($position == 'inner') {
            $positions = array(
                501, 502, 503, 509, 510, 591, 592
            );
        } else {
            $positions = array(
                450, 401, 402, 403, 405, 409, 410, 491, 492
            );
        }

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
