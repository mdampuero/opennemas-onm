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
class VideosController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list'       => 'video',
        'listauthor' => 'video',
        'show'       => 'video',
        'showamp'    => 'video',
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'VIDEO_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'showamp'    => 'amp_inner',
        'list'       => 'video_frontpage',
        'listauthor' => 'video_frontpage',
        'show'       => 'video_inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'video_frontpage' => [ 7, 9 ],
        'video_inner'     => [ 7 ],
    ];

    /**
     * The list of valid query parameters per action.
     *
     * @var array
     */
    protected $queries = [
        'list'       => [ 'page', 'category_name' ],
        'showamp'    => [ '_format' ],
    ];

    /**
     * The list of routes per action.
     *
     * @var array
     */
    protected $routes = [
        'list' => 'frontend_video_frontpage'
    ];

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'list'       => 'video/video_frontpage.tpl',
        'showamp'    => 'amp/content.tpl',
    ];

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

            $allVideos = [];

            if ($this->category != 0) {
                $criteria = array_merge(
                    $baseCriteria,
                    ['pk_fk_content_category' => [[ 'value' => $this->category ]] ]
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
    public function hydrateList(array &$params): void
    {
        $category = $params['o_category'];
        $page     = $params['page'];

        // Fetch video settings
        $settings = $this->get('orm.manager')->getDataSet('Settings')
            ->get('video_settings');

        $epp = $settings['total_front_more'] ?? 12;

        $order   = [ 'starttime' => 'DESC' ];
        $filters = [
            'content_type_name' => [[ 'value' => 'video' ]],
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            'starttime'       => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'         => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        $route = [ 'name' => 'frontend_video_page_frontpage' ];
        if ($category) {
            $filters['pk_fk_content_category'] = [ [ 'value' => $category->pk_content_category ] ];
            $route                    = [
                'name' => 'frontend_video_page_frontpage_category',
                'params' => [ 'category_name' => $category->pk_content_category ]
            ];
        }

        $oql = sprintf(
            'content_type_name="video" and content_status=1 and in_litter=0 '
            . 'order by starttime desc limit %d offset %d',
            $epp,
            $page
        );

        $response = $this->get('api.service.content_old')->getList($oql);

        $params = array_merge($params, [
            'videos' => $response['items'],
            'pager'  => $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $epp,
                'page'        => $page,
                'total'       => $response['total'],
                'route'       => $route
            ]),
        ]);
    }

    /**
     * Shows an inner video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function showOldAction(Request $request)
    {
        $dirtyID = $request->get('video_id', '', FILTER_SANITIZE_STRING);
        $urlSlug = $request->get('slug', '', FILTER_SANITIZE_STRING);

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
            'o_content'     => $video,
            'x-tags'        => 'video,' . $video->id,
            'x-cache-for'   => '+1 day',
            'x-cacheable'   => $cacheable,
            'tags'          => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($video->tag_ids)['items']
        ]);
    }
}
