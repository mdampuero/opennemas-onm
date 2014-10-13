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

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
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

        if (!\Onm\Module\ModuleManager::isActivated('VIDEO_MANAGER')) {
            throw new ResourceNotFoundException();
        }

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('video');

        $this->page          = $this->request->query->getDigits('page', 1);
        $this->category_name = $this->request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

        if ($this->category_name != 'home') {
            $ccm = \ContentCategoryManager::get_instance();
            $this->category = $ccm->get_id($this->category_name);
            $category_real_name = $ccm->getTitle($this->category_name);
            $this->view->assign(
                array(
                    'category'           => $this->category,
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
        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        # If is not cached process this action
        $cacheID = $this->view->generateCacheId($this->category_name, '', $this->page);
        if (($this->view->caching == 0)
                || !$this->view->isCached('video/video_frontpage.tpl', $cacheID)
        ) {
            // Fetch video settings
            $videosSettings = s::get('video_settings');
            $totalVideosFrontpage       = isset($videosSettings['total_front'])?$videosSettings['total_front']:2;
            $totalVideosMoreFrontpage   = isset($videosSettings['total_front_more'])?$videosSettings['total_front_more']:12;
            $totalVideosFrontpageOffset = isset($videosSettings['front_offset'])?$videosSettings['front_offset']:3;
            $totalVideosBlockInCategory = isset($videosSettings['block_in_category'])?$videosSettings['block_in_category']:3;
            $totalVideosBlockOther      = isset($videosSettings['block_others'])?$videosSettings['block_others']:6;

            if ($this->category_name != 'home') {
                // Fetch total of videos for this category
                $allVideos = $this->cm->findAll(
                    'Video',
                    'content_status=1 AND `contents_categories`.`pk_fk_content_category` ='
                    . $this->category . '',
                    'ORDER BY created DESC LIMIT '.($totalVideosFrontpage+$totalVideosBlockInCategory)
                );

                // Videos on frontpage left column
                $frontVideos  = array_slice($allVideos, 0, $totalVideosFrontpage);

                // Videos on more in category block
                $videos       = array_slice($allVideos, $totalVideosFrontpage, $totalVideosBlockInCategory);

                // Videos on others videos block
                $othersVideos = $this->cm->findAll(
                    'Video',
                    'content_status=1 ',
                    'ORDER BY views DESC LIMIT '.$totalVideosBlockOther
                );

                if (count($frontVideos) > 0) {
                    foreach ($frontVideos as &$video) {
                        $video->thumb          = $video->getThumb();
                        $video->category_name  = $video->loadCategoryName($video->id);
                        $video->category_title = $video->loadCategoryTitle($video->id);
                    }
                }
                $this->view->assign('front_videos', $frontVideos);

            } else {
                // Videos on top of the homepage
                $videos = $this->cm->findAll(
                    'Video',
                    'content_status=1 ',
                    'ORDER BY created DESC LIMIT '.$totalVideosFrontpageOffset
                );

                $order = array('created' => 'DESC');
                $filters = array(
                    'content_type_name' => array(array('value' => 'video')),
                    'content_status'    => array(array('value' => '1')),
                );

                $em = $this->get('entity_repository');
                $othersVideos = $em->findBy(
                    $filters,
                    $order,
                    $totalVideosMoreFrontpage,
                    $this->page,
                    $totalVideosFrontpageOffset
                );

                // Pagination for block more videos
                $pagination = \Onm\Pager\SimplePager::getPagerUrl(
                    array(
                        'page'  => $this->page,
                        'items' => $totalVideosMoreFrontpage,
                        'total' => count($othersVideos)+1,
                        'url'   => $this->generateUrl(
                            'frontend_video_ajax_paginated'
                        )
                    )
                );

                $this->view->assign('pagination', $pagination);
            }

            if (count($videos) > 0) {
                foreach ($videos as &$video) {
                    $video->thumb          = $video->getThumb();
                    $video->category_name  = $video->loadCategoryName($video->id);
                    $video->category_title = $video->loadCategoryTitle($video->id);
                }
            }

            if (count($othersVideos) > 0) {
                foreach ($othersVideos as &$video) {
                    $video->thumb          = $video->getThumb();
                    $video->category_name  = $video->loadCategoryName($video->id);
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

        if ($this->category_name != 'home') {
            return $this->render(
                'video/video_frontpage.tpl',
                array(
                    'cache_id' => $cacheID,
                    'categoryName' => $this->category_name,
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

            // Load Video and categories
            $video = $this->get('entity_repository')->find('Video', $videoID);
            $video->category_name = $video->loadCategoryName($video->id);
            $video->category_title = $video->loadCategoryTitle($video->id);
            $video->with_comment = 1;

            if ($video->content_status == 0 || $video->in_litter == 1) {
                throw new ResourceNotFoundException();
            }

            // Fetch video author
            $ur = getService('user_repository');
            $video->author = $ur->find($video->fk_author);

            //Get other_videos for widget video most
            $otherVideos = $this->cm->findAll(
                'Video',
                ' content_status=1 AND pk_content <> '.$videoID,
                ' ORDER BY created DESC LIMIT 4'
            );

            if (count($otherVideos) > 0) {
                foreach ($otherVideos as &$otherVideo) {
                    $otherVideo->thumb          = $otherVideo->getThumb();
                    $otherVideo->category_name  = $otherVideo->loadCategoryName($otherVideo->id);
                    $otherVideo->category_title = $otherVideo->loadCategoryTitle($otherVideo->id);
                }
            }

            $this->view->assign(
                array(
                    'video'         => $video,
                    'content'       => $video,
                    'category'      => $video->category,
                    'category_name' => $video->category_name,
                    'contentId'     => $video->id,
                    'action'        => 'inner',
                    'others_videos' => $otherVideos
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
        // Fetch video settings
        $videosSettings = s::get('video_settings');
        $totalVideosBlockOther      = isset($videosSettings['block_others'])?$videosSettings['block_others']:6;

        $limit = ($this->page-1)*$totalVideosBlockOther.', '.$totalVideosBlockOther;

        $videos = $this->cm->findAll(
            'Video',
            'content_status=1 AND `contents_categories`.`pk_fk_content_category` <> ' . $this->category . '',
            'ORDER BY created DESC  LIMIT ' . $limit
        );

        if (count($videos) > 0) {
            foreach ($videos as &$video) {
                $video->thumb          = $video->getThumb();
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
        // Fetch video settings
        $videosSettings = s::get('video_settings');
        $totalVideosBlockInCategory = isset($videosSettings['block_in_category'])?$videosSettings['block_in_category']:3;

        $limit = ($this->page-1)*$totalVideosBlockInCategory.', '.$totalVideosBlockInCategory;

        $videos = $this->cm->findAll(
            'Video',
            'content_status=1 AND `contents_categories`.`pk_fk_content_category` =' . $this->category . '',
            'ORDER BY created DESC LIMIT ' . $limit
        );

        if (count($videos) > 0) {
            foreach ($videos as &$video) {
                $video->thumb          = $video->getThumb();
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
                // 'total_incategory'   => 9, commented on all templates
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
        // Fetch video settings
        $videosSettings = s::get('video_settings');
        $totalVideosMoreFrontpage   = isset($videosSettings['total_front_more'])?$videosSettings['total_front_more']:12;
        $totalVideosFrontpageOffset = isset($videosSettings['front_offset'])?$videosSettings['front_offset']:3;
        if (empty($this->category)) {
            $this->category = $request->query->getDigits('category', 0);
        }

        $order = array('created' => 'DESC');
        $filters = array(
            'content_type_name' => array(array('value' => 'video')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
        );

        if ($this->category != 0) {
            $category = $this->get('category_repository')->find($this->category);
            $filters['category_name'] = array(array('value' => $category->name));
        }

        $em = $this->get('entity_repository');
        $othersVideos = $em->findBy($filters, $order, $totalVideosMoreFrontpage, $this->page, $totalVideosFrontpageOffset);
        $countVideos = $em->countBy($filters);

        if ($countVideos == 0) {
            return new RedirectResponse(
                $this->generateUrl('frontend_video_ajax_paginated')
            );
        }

        $pagination = \Onm\Pager\SimplePager::getPagerUrl(
            array(
                'page'  => $this->page,
                'items' => $totalVideosMoreFrontpage,
                'total' => count($othersVideos)+1,
                'url'   => $this->generateUrl(
                    'frontend_video_ajax_paginated',
                    array(
                        'category' => $this->category
                    )
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
        $ccm = \ContentCategoryManager::get_instance();
        $categoryName = 'video';
        $category = $ccm->get_id($categoryName);

        // Get video positions
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        if ($context == 'inner') {
            $positions = $positionManager->getAdsPositionsForGroup('video_inner', array(7, 9));
        } else {
            $positions = $positionManager->getAdsPositionsForGroup('video_frontpage', array(7, 9));
        }

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
