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
    public function frontpageAction(Request $request)
    {
        $page         = $request->query->getDigits('page', 1);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

        // Setup templating cache layer
        $this->view->setConfig('video');
        $cacheID = $this->view->getCacheId('frontpage', 'video', $categoryName, $page);

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('video/video_frontpage.tpl', $cacheID)
        ) {
            $settings = $this->get('orm.manager')->getDataSet('Settings')
                ->get('video_settings');

            // Fetch video settings
            $totalVideosFrontpage       = isset($settings['total_front']) ? $settings['total_front'] : 2;
            $totalVideosMoreFrontpage   = isset($settings['total_front_more']) ? $settings['total_front_more'] : 12;
            $totalVideosFrontpageOffset = isset($settings['front_offset']) ? $settings['front_offset'] : 3;

            $baseCriteria = [
                'fk_content_type' => [ [ 'value' => 9 ] ],
                'content_status'  => [ [ 'value' => 1 ] ],
                'in_litter'       => [ [ 'value' => 0 ] ],
                'starttime'       => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ],
                'endtime'         => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                ]
            ];

            $em = $this->get('entity_repository');

            if ($categoryName != 'home') {
                $categoryManager = $this->get('category_repository');
                $category        = $categoryManager->findOneBy(
                    [ 'name' => [['value' => $categoryName ]] ],
                    'name ASC'
                );

                if (empty($category)) {
                    throw new ResourceNotFoundException();
                }

                $criteria = array_merge(
                    $baseCriteria,
                    ['pk_fk_content_category' => [[ 'value' => $category->id ]] ]
                );

                $allVideos = $em->findBy(
                    $criteria,
                    'starttime DESC',
                    (int) ($totalVideosFrontpage),
                    (int) $page
                );

                // Videos on frontpage left column
                $frontVideos = array_slice($allVideos, 0, (int) $totalVideosFrontpage);

                // Videos on more in category block
                $videos = array_slice($allVideos, (int) $totalVideosFrontpage);

                // Videos on others videos block
                $otherVideos = $em->findBy(
                    array_merge(
                        $criteria,
                        [ 'pk_fk_content_category' => [[ 'value' => $this->category, 'operator' => '<>' ]] ]
                    ),
                    'starttime DESC',
                    $totalVideosMoreFrontpage,
                    $page
                );

                $this->view->assign(['front_videos'  => $frontVideos, ]);
            } else {
                // Videos on top of the homepage
                $videos = $em->findBy($baseCriteria, 'starttime DESC', $totalVideosFrontpageOffset, $page);

                // Videos at the bottom of the frontpage
                $otherVideos = $em->findBy(
                    $baseCriteria,
                    'starttime DESC',
                    $totalVideosMoreFrontpage,
                    $page,
                    $totalVideosFrontpageOffset
                );
            }

            // Pagination for block more videos (ajax)
            $url   = [ 'name' => 'frontend_video_ajax_paginated' ];
            $total = count($otherVideos) + 1;
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
                'others_videos' => $otherVideos,
                'page'          => '1',
                'pager'         => $pager,
                'pagination'    => $pagination,
            ]);
        }

        list($positions, $advertisements) = $this->getAds($this->category);

        $template = 'video/video_main_frontpage.tpl';
        $params   = [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheID,
            'x-tags'         => 'video-frontpage,' . $this->category_name . ',' . $this->page
        ];

        if ($this->category_name != 'home') {
            $template               = 'video/video_frontpage.tpl';
            $params['categoryName'] = $this->category_name;
        }

        return $this->render($template, $params);
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
            $settings = $this->get('orm.manager')->getDataSet('Settings')
                ->get('video_settings');

            $itemsPerPage = isset($settings['total_front_more']) ? $settings['total_front_more'] : 12;

            $order   = [ 'starttime' => 'DESC' ];
            $filters = [
                'content_type_name' => [[ 'value' => 'video' ]],
                'content_status'    => [[ 'value' => 1 ]],
                'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
                'starttime'       => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ],
                'endtime'         => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                ]
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
            $countVideos = true;
            $videos      = $er->findBy($filters, $order, $itemsPerPage, $this->page, 0, $countVideos);

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
            // Fetch video author
            $ur            = $this->get('user_repository');
            $video->author = $ur->find($video->fk_author);

            // Get other_videos for widget video most
            $order   = [ 'starttime' => 'DESC' ];
            $filters = [
                'pk_content'             => [[ 'value' => $video->id, 'operator' => '<>' ]],
                'pk_fk_content_category' => [[ 'value' => $this->category ]],
                'content_type_name'      => [[ 'value' => 'video' ]],
                'content_status'         => [[ 'value' => 1 ]],
                'in_litter'              => [[ 'value' => 1, 'operator' => '!=' ]],
                'starttime'       => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ],
                'endtime'         => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                ]
            ];

            $otherVideos = $this->get('entity_repository')->findBy($filters, $order, 4, 1);

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
            'tags'            => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($video->tag_ids)['items']
        ]);
    }

    /**
     * Return via ajax more videos of a category
     *
     * @return Response the response object
     */
    public function ajaxMoreAction()
    {
        $total = 6;
        $limit = ($this->page - 1) * $total . ', ' . $total;

        $order   = [ 'starttime' => 'DESC' ];
        $filters = [
            'pk_fk_content_category' => [[ 'value' => $this->category, 'operator' => '<>' ]],
            'content_type_name'      => [[ 'value' => 'video' ]],
            'content_status'         => [[ 'value' => 1 ]],
            'in_litter'              => [[ 'value' => 1, 'operator' => '!=' ]],
            'starttime'       => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'         => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        $videos = $this->get('entity_repository')->findBy($filters, $order, $limit, 0);

        if (count($videos) <= 0) {
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
        $total = 3;
        $limit = ($this->page - 1) * $total . ', ' . $total;

        if (empty($this->category)) {
            $this->category = $request->query->getDigits('category', 0);
        }

        $order   = [ 'starttime' => 'DESC' ];
        $filters = [
            'pk_fk_content_category' => [[ 'value' => $this->category ]],
            'content_type_name'      => [[ 'value' => 'video' ]],
            'content_status'         => [[ 'value' => 1 ]],
            'in_litter'              => [[ 'value' => 1, 'operator' => '!=' ]],
            'starttime'       => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'         => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        $videos = $this->get('entity_repository')->findBy($filters, $order, $limit, 0);

        if (count($videos) <= 0) {
            return new RedirectResponse(
                $this->generateUrl('frontend_video_ajax_incategory', ['category' => $this->category])
            );
        }

        return $this->render('video/partials/_widget_video_incategory.tpl', [
            'videos'             => $videos,
            'actual_category_id' => $this->category,
            'page'               => $this->page,
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
        // Fetch video settings
        $settings = $this->get('orm.manager')->getDataSet('Settings')
            ->get('video_settings');
        $epp      = isset($settings['total_front_more']) ? $settings['total_front_more'] : 12;
        $offset   = isset($settings['front_offset']) ? $settings['front_offset'] : 3;

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

        $countVideos  = true;
        $othersVideos = $em->findBy($filters, $order, $epp, $this->page, $offset, 0, $countVideos);

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
            'others_videos' => $othersVideos,
            'page'          => $this->page,
            'pagination'    => $pagination,
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
