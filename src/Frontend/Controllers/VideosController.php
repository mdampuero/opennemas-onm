<?php
/**
 * Handles the actions for advertisements
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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Frontend_Controllers
 **/
class VideosController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('video');

        /******************* GESTION CATEGORIAS  *****************************/
        /*
        Setting up available categories for menu.
        $actual_category_id ---> Solo se accede en accion list,
                                    en otras se asigna el tpl->assign pero se toma el valor de category
        $category_real_name ---> Solo se accede aqui
        */
        $this->category_name = $this->request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $this->page = $this->request->query->getDigits('page', 1);

        if (!empty($this->category_name)
            && $this->category_name != 'home'
        ) {
            $ccm = \ContentCategoryManager::get_instance();
            $this->category = $ccm->get_id($this->category_name);
            $category_real_name = $ccm->get_title($this->category_name);
            $this->view->assign(
                array(
                    'category'           => $this->category ,
                    'actual_category_id' => $this->category,
                    'category_name'      => $this->category_name,
                    'actual_category'    => $this->category_name,
                )
            );
        } else {
            $category_real_name = 'Portada';
            $this->category = 0; //NEED CODE WIDGETS
        }
        $this->view->assign('category_real_name', $category_real_name);
        /******************* GESTION CATEGORIAS  *****************************/

        $this->cm = new \ContentManager();
    }

    /**
     * Renders the video frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        $categoryName = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);

        $this->getAds();

        # If is not cached process this action
        $cacheID = $this->view->generateCacheId($categoryName, '', $this->page);
        if (($this->view->caching == 0)
            || !$this->view->isCached('video/video_frontpage.tpl', $cacheID)
        ) {
            $videosSettings = s::get('video_settings');

            $totalVideosFrontpage = isset($videosSettings['total_front'])?$videosSettings['total_front']:2;
            $days = isset($videosSettings['time_last']) ?: 365;

            if (isset($categoryName)
                && !empty($categoryName)
                && $categoryName != 'home'
            ) {
                $frontVideos = $this->cm->find_all(
                    'Video',
                    'available=1 AND `contents_categories`.`pk_fk_content_category` ='
                    . $this->category . '',
                    'ORDER BY created DESC LIMIT '.$totalVideosFrontpage
                );

                $videos = $this->cm->find_all(
                    'Video',
                    'available=1 AND `contents_categories`.`pk_fk_content_category` ='.$this->category,
                    'ORDER BY created DESC LIMIT '.$totalVideosFrontpage.',3'
                );

                $othersVideos = $this->cm->find_all(
                    'Video',
                    'available=1 AND created >=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)  ',
                    'ORDER BY views DESC LIMIT 3, 6'
                );

                if (count($videos) > 0) {
                    foreach ($videos as &$video) {
                        $video->category_name  = $video->loadCategoryName($video->id);
                        $video->category_title = $video->loadCategoryTitle($video->id);
                    }
                }
                $this->view->assign('front_videos', $frontVideos);

            } else {
                $videos = $this->cm->find_all(
                    'Video',
                    ' available=1 ',
                    'ORDER BY created DESC LIMIT 3'
                );

                $othersVideos = $this->cm->find_all(
                    'Video',
                    ' available=1 AND created >= DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)  ',
                    'ORDER BY starttime DESC LIMIT 3,12'
                );
            }

            if (count($videos) > 0) {
                foreach ($videos as &$video) {
                    $video->category_name  = $video->loadCategoryName($video->id);
                    $video->category_title = $video->loadCategoryTitle($video->id);
                }
            }

            if (count($othersVideos) > 0) {
                foreach ($othersVideos as &$video) {
                    $video->category_name = $video->loadCategoryName($video->id);
                    $video->category_title = $video->loadCategoryTitle($video->id);
                }
            }

            $this->view->assign(
                array(
                    'videos'        => $videos,
                    'others_videos' => $othersVideos,
                    'page'          => '1'
                )
            );
        }

        if (isset($categoryName)
            && !empty($categoryName)
            && $categoryName != 'home'
        ) {
            return $this->render(
                'video/video_frontpage.tpl',
                array(
                    'cache_id' => $cacheID,
                )
            );
        } else {
            return $this->render(
                'video/video_main_frontpage.tpl',
                array(
                    'cache_id' => $cacheID,
                )
            );
        }
    }

    /**
     * Shows an inner video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $dirtyID = $request->query->getDigits('video_id', '');
        $videoID = \Content::resolveID($dirtyID);

        // Redirect to album frontpage if id_album wasn't provide
        if (is_null($videoID)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        $this->getAds('inner');

        # If is not cached process this action
        $cacheID = $this->view->generateCacheId($this->category_name, null, $videoID);
        if ($this->view->caching == 0
            || !$this->view->isCached('video/video_inner.tpl', $videoID)
        ) {

            $video = new \Video($videoID);
            $video->category_name = $video->loadCategoryName($video->id);
            $video->category_title = $video->loadCategoryTitle($video->id);
            $video->with_comment = 1;

            //Get other_videos for widget video most
            $otherVideos = $this->cm->find_all(
                'Video',
                ' available=1 AND pk_content <> '.$videoID,
                ' ORDER BY created DESC LIMIT 4'
            );

            if (count($otherVideos) > 0) {
                foreach ($otherVideos as &$otherVideo) {
                    $otherVideo->category_name  = $otherVideo->loadCategoryName($otherVideo->id);
                    $otherVideo->category_title = $otherVideo->loadCategoryTitle($otherVideo->id);
                }
            }

            /******* SUGGESTED CONTENTS *******/
            $objSearch = \cSearch::getInstance();
            $machineRelatedContent = $objSearch->searchSuggestedContents(
                $video->metadata,
                'video',
                "pk_fk_content_category= ".$video->category.
                " AND contents.available=1 AND pk_content = pk_fk_content",
                4
            );
            $this->view->assign(
                array(
                    'video'         => $video,
                    'content'       => $video,
                    'category'      => $video->category,
                    'category_name' => $video->category_name,
                    'contentId'     => $video->id,
                    'action'        => 'inner',
                    'others_videos' => $otherVideos,
                    'suggested'     => $machineRelatedContent
                )
            );
        } //end iscached

        return $this->render(
            'video/video_inner.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * Return via ajax more videos of a category
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function ajaxMoreAction(Request $request)
    {
        if ($this->category == 0) {
            $itemsPage = 6;
        } else {
            $itemsPage = 3;
        }

        $limit = 'LIMIT ' . ($this->page - 1) * $itemsPage . ', ' . ($itemsPage);

        $videos = $this->cm->find_all(
            'Video',
            'available=1 AND `contents_categories`.`pk_fk_content_category` <> ' . $this->category . '',
            'ORDER BY created DESC ' . $limit
        );

        if (count($videos) > 0) {
            foreach ($videos as &$video) {
                $video->category_name  = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }
        } else {
            return new RedirectResponse(
                $this->generateUrl('frontend_video_ajax_more', array('category' => $this->category))
            );
        }

        return $this->render(
            'video/partials/_widget_video_more_interesting.tpl',
            array(
                'others_videos'      => $videos,
                'actual_category_id' => $this->category,
                'page'               => $this->page,
                'total_more'         => 4,
            )
        );
    }

    /**
     * Return via ajax videos of a category
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function ajaxInCategoryAction(Request $request)
    {
        $itemsPage = 3;

        if ($this->category == 0) {
            $this->category = $request->query->filter('category', null, FILTER_SANITIZE_STRING);
        }

        $_limit = 'LIMIT ' . ($this->page - 1) * $itemsPage . ', ' . ($itemsPage);

        $videos = $this->cm->find_all(
            'Video',
            'available=1 AND `contents_categories`.`pk_fk_content_category` =' . $this->category . '',
            'ORDER BY created DESC ' . $_limit
        );

        if (count($videos) > 0) {
            foreach ($videos as &$video) {
                $video->category_name  = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }
        } else {
            return new RedirectResponse(
                $this->generateUrl('frontend_video_ajax_incategory', array('category' => $this->category))
            );
        }

        return $this->render(
            'video/partials/_widget_video_incategory.tpl',
            array(
                'videos'             => $videos,
                'actual_category_id' => $this->category,
                'page'               => $this->page,
                'total_incategory'   => 9,
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
        $albumID   = $request->query->filter('album_id', null, FILTER_SANITIZE_STRING);
        $page      = $request->query->filter('page', 1, FILTER_VALIDATE_INT);
        $itemsPage = 8;

        // Redirect to album frontpage if id_album wasn't provided
        if (is_null($albumID)) {
            return new RedirectResponse($this->generateUrl('frontend_album_frontpage'));
        }

        // Get the album from the id and increment the numviews for it
        $album = new \Album($albumID);
        $this->view->assign('album', $album);

        $album->category_name  = $album->loadCategoryName($album->id);
        $album->category_title = $album->loadCategoryTitle($album->id);
        $_albumArray           = $album->_getAttachedPhotos($album->id);
        $_albumArrayPaged      = $album->getAttachedPhotosPaged($album->id, 8, $page);

        if (count($_albumArrayPaged) > $itemsPage) {
            array_pop($_albumArrayPaged);
        }

        return $this->render(
            'widgets/widget_gallery_thumbs.tpl',
            array(
                'album_photos'       => $_albumArray,
                'album_photos_paged' => $_albumArrayPaged,
                'page'               => $page,
                'items_page'         => $itemsPage,
            )
        );
    }

    /**
     * Render advertisement on videos
     *
     * @param string $context the context to fetch ads from
     */
    private function getAds($context = 'frontpage')
    {
        if ($context == 'inner') {
            $positions = array(301, 302, 303, 305, 309, 310, 391, 392);
            $intersticialId = 350;
        } else {
            $positions = array(201, 202, 203, 205, 209, 210, 291, 292);
            $intersticialId = 250;
        }
        // Asignacion de valores y comprobaciones realizadas en init
        // $ccm = ContentCategoryManager::get_instance();
        // $category = $ccm->get_id($category_name);
        // $category = (!isset($category) || ($category=='home'))? 0: $category;

        $advertisement = \Advertisement::getInstance();

        // Load internal banners, principal banners (1,2,3,11,13) and use cache to performance
        /* $banners = $advertisement->cache->getAdvertisements(array(1, 2, 3, 10, 12, 11, 13), $category); */
        $banners = $advertisement->getAdvertisements($positions, $this->category);
        $banners = $this->cm->getInTime($banners);

        //$advertisement->renderMultiple($banners, &$tpl);
        $advertisement->renderMultiple($banners, $advertisement);

        $intersticial = $advertisement->getIntersticial($intersticialId, '$category');
        if (!empty($intersticial)) {
            $advertisement->renderMultiple(array($intersticial), $advertisement);
        }
    }
}
