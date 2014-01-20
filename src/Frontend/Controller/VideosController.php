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
namespace Frontend\Controller;

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

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        # If is not cached process this action
        $cacheID = $this->view->generateCacheId($categoryName, '', $this->page);
        if (($this->view->caching == 0)
            || !$this->view->isCached('video/video_frontpage.tpl', $cacheID)
        ) {
            $videosSettings = s::get('video_settings');

            $totalVideosFrontpage = isset($videosSettings['total_front'])?$videosSettings['total_front']:2;

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
                    'available=1 ',
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


                $itemsPerPage = 12;//$totalVideosFrontpage;
                list($countVideos, $othersVideos)= $this->cm->getCountAndSlice(
                    'Video',
                    (int) $this->category,
                    'in_litter != 1 AND contents.available=1',
                    'ORDER BY created DESC',
                    $this->page,
                    $itemsPerPage
                );

                $total = count($othersVideos)+1;

                $pagination = \Onm\Pager\SimplePager::getPagerUrl(
                    array(
                        'page'  => $this->page,
                        'items' => $itemsPerPage,
                        'total' => $total,
                        'url'   => $this->generateUrl(
                            'frontend_video_ajax_paginated'
                        )
                    )
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
                    'pagination'    => $pagination,
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

        $ads = $this->getAds('inner');
        $this->view->assign('advertisements', $ads);

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

            // Machine suggested contents code -----------------------------
            $machineSuggestedContents = $this->get('automatic_contents')->searchSuggestedContents(
                $video->metadata,
                'video',
                "pk_fk_content_category = ".$video->category.
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
                    'suggested'     => $machineSuggestedContents
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
        $itemsPerPage = 12;//$totalVideosFrontpage;
        list($countVideos, $othersVideos)= $this->cm->getCountAndSlice(
            'Video',
            (int) $this->category,
            'in_litter != 1 AND contents.available=1',
            'ORDER BY created DESC',
            $this->page,
            $itemsPerPage
        );

        $total = count($othersVideos)+1;

        if ($countVideos > 0) {
            foreach ($othersVideos as &$video) {
                $video->category_name  = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }

        } else {

            return new RedirectResponse(
                $this->generateUrl('frontend_video_ajax_paginated')
            );
        }


        $pagination = \Onm\Pager\SimplePager::getPagerUrl(
            array(
                'page'  => $this->page,
                'items' => $itemsPerPage,
                'total' => $total,
                'url'   => $this->generateUrl(
                    'frontend_video_ajax_paginated'
                )
            )
        );

        return $this->render(
            'video/partials/_widget_more_videos.tpl',
            array(
                'others_videos'      => $othersVideos,
                'page'               => $this->page,
                'pagination'         => $pagination,
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
        $category = (!isset($category) || ($category == 'home'))? 0: $category;

        // Get video positions
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        if ($context == 'inner') {
            $positions = $positionManager->getAdsPositionsForGroup('video_inner', array(7, 9));
        } else {
            $positions = $positionManager->getAdsPositionsForGroup('video_frontpage', array(7, 9));
        }

        return \Advertisement::findForPositionIdsAndCategory($positions, $this->category);
    }
}
