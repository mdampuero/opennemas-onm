<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Displays an album or a list of albums.
 */
class VideosController extends Controller
{
    /**
     * Common code for all the actions.
     */
    public function init()
    {
        $this->page          = $this->request->query->getDigits('page', 1);
        $this->category_name = $this->request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

        if (!empty($this->category_name) && $this->category_name != 'home') {
            $categoryManager = $this->get('category_repository');
            $category        = $categoryManager->findBy(
                ['name' => [['value' => $this->category_name]]],
                'name ASC'
            );

            if (empty($category)) {
                throw new ResourceNotFoundException();
            }

            $category       = $category[0];
            $this->category = $category->pk_content_category;

            $this->view->assign([
                'category'           => $this->category,
                'actual_category_id' => $this->category,
                'category_name'      => $this->category_name,
                'actual_category'    => $this->category_name,
                'category_data'      => $category,
                'category_real_name' => $category->title,
            ]);
        } else {
            $this->category = 0;
            $this->view->assign('category_real_name', 'Portada');
        }

        $this->cm = new \ContentManager();
    }

    /**
     * Renders the video frontpage
     *
     * @return Response The response object.
     */
    public function frontpageAction()
    {
        // Setup templating cache layer
        $this->view->setConfig('video');
        $cacheID = $this->view->getCacheId('frontpage', 'video', $this->category_name, $this->page);

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('video/video_frontpage.tpl', $cacheID)
        ) {
            // Fetch video settings
            $videosSettings             = s::get('video_settings');
            $totalVideosFrontpage       = isset($videosSettings['total_front']) ? $videosSettings['total_front'] : 2;
            $totalVideosMoreFrontpage   = isset($videosSettings['total_front_more']) ?
                $videosSettings['total_front_more'] :
                12;
            $totalVideosFrontpageOffset = isset($videosSettings['front_offset']) ? $videosSettings['front_offset'] : 3;
            $totalVideosBlockInCategory = isset($videosSettings['block_in_category']) ?
                $videosSettings['block_in_category'] :
                0;
            $totalVideosBlockOther      = isset($videosSettings['block_others']) ? $videosSettings['block_others'] : 6;

            if ($this->category_name != 'home') {
                // Fetch total of videos for this category
                $allVideos = $this->cm->findAll(
                    'Video',
                    'content_status=1 AND `contents_categories`.`pk_fk_content_category` ='
                    . $this->category . '',
                    'ORDER BY created DESC LIMIT ' . ($totalVideosFrontpage + $totalVideosBlockInCategory)
                );

                // Videos on frontpage left column
                $frontVideos = array_slice($allVideos, 0, $totalVideosFrontpage);

                // Videos on more in category block
                $videos = array_slice($allVideos, $totalVideosFrontpage, $totalVideosBlockInCategory);

                // Videos on others videos block
                $othersVideos = $this->cm->findAll(
                    'Video',
                    'content_status=1 AND `contents_categories`.`pk_fk_content_category` <>'
                    . $this->category . '',
                    'ORDER BY created DESC LIMIT ' . $totalVideosMoreFrontpage
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
                    'ORDER BY created DESC LIMIT ' . $totalVideosFrontpageOffset
                );

                $order   = ['created' => 'DESC'];
                $filters = [
                    'content_type_name' => [['value' => 'video']],
                    'content_status'    => [['value' => '1']],
                ];

                $em           = $this->get('entity_repository');
                $othersVideos = $em->findBy(
                    $filters,
                    $order,
                    $totalVideosMoreFrontpage,
                    $this->page,
                    $totalVideosFrontpageOffset
                );
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

            // Pagination for block more videos (ajax)
            $url   = [ 'name' => 'frontend_video_ajax_paginated' ];
            $total = count($othersVideos) + 1;
            if ($this->category != 0) {
                $url   = [
                    'name'   => 'frontend_video_ajax_paginated',
                    'params' => [ 'category_name' => $this->category_name ]
                ];
                $total = count($allVideos) + 1;
            }
            $pagination = $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $totalVideosMoreFrontpage,
                'page'        => $this->page,
                'total'       => $total,
                'route'       => $url
            ]);

            if (isset($pagination->links)) {
                $pagination = $pagination->links;
            }

            // New pagination for video frontpage
            $route = [ 'name' => 'frontend_video_page_frontpage' ];
            if ($this->category != 0) {
                $route = [
                    'name' => 'frontend_video_page_frontpage_category',
                    'params' => [ 'category_name' => $this->category_name ]
                ];
            }
            $pager = $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $totalVideosMoreFrontpage,
                'page'        => $this->page,
                'total'       => $total,
                'route'       => $route
            ]);

            $this->view->assign([
                'videos'        => $videos,
                'others_videos' => $othersVideos,
                'page'          => '1',
                'pager'         => $pager,
                'pagination'    => $pagination,
            ]);
        }

        list($positions, $advertisements) = $this->getAds($this->category);

        if ($this->category_name != 'home') {
            return $this->render('video/video_frontpage.tpl', [
                'ads_positions'  => $positions,
                'advertisements' => $advertisements,
                'cache_id'       => $cacheID,
                'categoryName'   => $this->category_name,
                'x-tags'         => 'video-frontpage,' . $this->category_name . ',' . $this->page
            ]);
        } else {
            return $this->render('video/video_main_frontpage.tpl', [
                'ads_positions'  => $positions,
                'advertisements' => $advertisements,
                'cache_id'       => $cacheID,
                'x-tags'         => 'video-frontpage,' . $this->category_name . ',' . $this->page
            ]);
        }
    }

    /**
     * Renders the video frontpage
     *
     * @return Response the response object
     */
    public function frontpagePaginatedAction()
    {
        // Setup templating cache layer
        $this->view->setConfig('video');
        $cacheID = $this->view->getCacheId('frontpage', 'video', $this->category_name, $this->page);

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('video/video_frontpage.tpl', $cacheID)
        ) {
            // Fetch video settings
            $videosSettings = $this->get('setting_repository')->get('video_settings');
            $itemsPerPage   = isset($videosSettings['total_front_more']) ? $videosSettings['total_front_more'] : 12;

            $order   = [ 'created' => 'DESC' ];
            $filters = [
                'content_type_name' => [[ 'value' => 'video' ]],
                'content_status'    => [[ 'value' => 1 ]],
                'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            ];

            $route = [ 'name' => 'frontend_video_page_frontpage' ];
            if ($this->category != 0) {
                $filters['category_name'] = [ [ 'value' => $this->category_name ] ];
                $route                    = [
                    'name' => 'frontend_video_page_frontpage_category',
                    'params' => [ 'category_name' => $this->category_name ]
                ];
            }

            $er          = $this->get('entity_repository');
            $videos      = $er->findBy($filters, $order, $itemsPerPage, $this->page);
            $countVideos = $er->countBy($filters);

            $pager = $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $itemsPerPage,
                'page'        => $this->page,
                'total'       => $countVideos,
                'route'       => $route
            ]);

            $this->view->assign([
                'videos' => $videos,
                'pager'  => $pager,
            ]);
        }

        list($positions, $advertisements) = $this->getAds($this->category);

        return $this->render('video/video_frontpage.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheID,
            'categoryName'   => $this->category_name,
            'x-tags'         => 'video-frontpage,' . $this->category_name . ',' . $this->page
        ]);
    }

    /**
     * Shows an inner video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function showAction(Request $request)
    {
        $dirtyID = $request->query->filter('video_id', '', FILTER_SANITIZE_STRING);
        $urlSlug = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        $video = $this->get('content_url_matcher')
            ->matchContentUrl('video', $dirtyID, $urlSlug, $this->category_name);

        if (empty($video)) {
            throw new ResourceNotFoundException();
        }

        $subscriptionFilter = new \Frontend\Filter\SubscriptionFilter($this->view, $this->getUser());
        $cacheable          = $subscriptionFilter->subscriptionHook($video);

        // Setup templating cache layer
        $this->view->setConfig('video-inner');
        $cacheID = $this->view->getCacheId('content', $video->id);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('video/video_inner.tpl', $video->id)
        ) {
            // Load Video and categories
            $video->category_name = $video->loadCategoryName($video->id);

            // Fetch video author
            $ur            = getService('user_repository');
            $video->author = $ur->find($video->fk_author);

            //Get other_videos for widget video most
            $otherVideos = $this->cm->findAll(
                'Video',
                ' content_status=1 AND `contents_categories`.`pk_fk_content_category` ='
                . $this->category . ' AND pk_content <> ' . $video->id,
                ' ORDER BY created DESC LIMIT 4'
            );

            if (count($otherVideos) > 0) {
                foreach ($otherVideos as &$otherVideo) {
                    $otherVideo->thumb          = $otherVideo->getThumb();
                    $otherVideo->category_name  = $otherVideo->loadCategoryName($otherVideo->id);
                    $otherVideo->category_title = $otherVideo->loadCategoryTitle($otherVideo->id);
                }
            }

            $this->view->assign(['others_videos' => $otherVideos]);
        }

        list($positions, $advertisements) = $this->getAds($this->category, 'inner');

        return $this->render('video/video_inner.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'video'         => $video,
            'content'       => $video,
            'category'      => $video->category,
            'category_name' => $video->category_name,
            'contentId'     => $video->id,
            'action'        => 'inner',
            'cache_id'      => $cacheID,
            'x-tags'        => 'video,' . $video->id,
            'x-cache-for'   => '+1 day',
            'x-cacheable'   => $cacheable,
        ]);
    }

    /**
     * Return via ajax more videos of a category
     *
     * @return Response the response object
     */
    public function ajaxMoreAction()
    {
        // Fetch video settings
        $videosSettings        = s::get('video_settings');
        $totalVideosBlockOther = isset($videosSettings['block_others']) ?
            $videosSettings['block_others'] :
            6;

        $limit = ($this->page - 1) * $totalVideosBlockOther . ', ' . $totalVideosBlockOther;

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
                $this->generateUrl('frontend_video_ajax_more', ['category' => $this->category])
            );
        }

        return $this->render('video/partials/_widget_video_more_interesting.tpl', [
            'others_videos'      => $videos,
            'actual_category_id' => $this->category,
            'page'               => $this->page,
        ]);
    }

    /**
     * Return via ajax videos of a category
     *
     * @return Response the response object
     */
    public function ajaxInCategoryAction(Request $request)
    {
        // Fetch video settings
        $videosSettings             = s::get('video_settings');
        $totalVideosBlockInCategory = isset($videosSettings['block_in_category']) ?
            $videosSettings['block_in_category'] :
            3;

        $limit = ($this->page - 1) * $totalVideosBlockInCategory . ', ' . $totalVideosBlockInCategory;

        if (empty($this->category)) {
            $this->category = $request->query->getDigits('category', 0);
        }

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
                $this->generateUrl('frontend_video_ajax_incategory', ['category' => $this->category])
            );
        }

        return $this->render('video/partials/_widget_video_incategory.tpl', [
            'videos'             => $videos,
            'actual_category_id' => $this->category,
            'page'               => $this->page,
            // 'total_incategory'   => 9, commented on all templates
        ]);
    }

    /**
     * Returns a list of videos.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function ajaxPaginatedAction(Request $request)
    {
        $videosSettings = s::get('video_settings');
        $epp            = isset($videosSettings['total_front_more']) ?
            $videosSettings['total_front_more'] : 12;
        $offset         = isset($videosSettings['front_offset']) ?
            $videosSettings['front_offset'] : 3;

        if (empty($this->category)) {
            $this->category = $request->query->getDigits('category', 0);
        }

        $order   = ['created' => 'DESC'];
        $filters = [
            'content_type_name' => [['value' => 'video']],
            'content_status'    => [['value' => 1]],
            'in_litter'         => [['value' => 1, 'operator' => '!=']],
        ];

        if ($this->category != 0) {
            $category                 = $this->get('category_repository')->find($this->category);
            $filters['category_name'] = [['value' => $category->name]];
        }

        $em = $this->get('entity_repository');

        $othersVideos = $em->findBy($filters, $order, $epp, $this->page, $offset);
        $countVideos  = $em->countBy($filters);

        if ($countVideos == 0) {
            return new RedirectResponse(
                $this->generateUrl('frontend_video_ajax_paginated')
            );
        }

        $pagination = $this->get('paginator')->get([
            'boundary'    => false,
            'directional' => true,
            'maxLinks'    => 0,
            'epp'         => $epp,
            'page'        => $this->page,
            'total'       => count($othersVideos) + 1,
            'route'       => [
                'name'   => 'frontend_video_ajax_paginated',
                'params' => ['category' => $this->category]
            ]
        ]);

        return $this->render('video/partials/_widget_more_videos.tpl', [
            'others_videos'      => $othersVideos,
            'page'               => $this->page,
            'pagination'         => $pagination,
        ]);
    }

    /**
     * Returns a list of advertisements for a category and page.
     *
     * @param mixed  $category The category id.
     * @param string $page     The page type.
     *
     * @return array The list of advertisements.
     */
    private function getAds($category = 'home', $page = '')
    {
        $category = (!isset($category) || ($category == 'home')) ? 0 : $category;

        // Get video positions
        $positionManager = $this->get('core.helper.advertisement');

        if ($page == 'inner') {
            $positions = $positionManager->getPositionsForGroup('video_inner', [ 7 ]);
        } else {
            $positions = $positionManager->getPositionsForGroup('video_frontpage', [ 7, 9 ]);
        }

        $advertisements = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
    }
}
