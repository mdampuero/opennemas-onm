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

/**
 * Displays an video frontpage and video inner.
 */
class VideoController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list'       => 'video',
        'listauthor' => 'video',
        'show'       => 'video-inner',
        'showamp'    => 'video-inner',
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
        'show'       => 'video/video_inner.tpl',
        'showamp'    => 'amp/content.tpl',
    ];

    /**
     * {@inheritdoc}
     *
     * Action specific for the frontpage
     */
    public function hydrateList(array &$params = []): void
    {
        $category = $params['o_category'];
        $page     = $params['page'] ?? 1;
        $date     = date('Y-m-d H:i:s');

        // Fetch video settings
        $settings = $this->get('orm.manager')->getDataSet('Settings')
            ->get('video_settings');
        $epp      = $this->get('orm.manager')->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        $epp = $settings['total_front_more'] ?? $epp;

        $categoryOQL = ($category)
            ? sprintf(' and pk_fk_content_category=%d', $category->pk_content_category)
            : '';

        $response = $this->get('api.service.content_old')->getList(sprintf(
            'content_type_name="video" and content_status=1 and in_litter=0 %s '
            . 'and (starttime IS NULL or starttime < "%s") '
            . 'and (endtime IS NULL or endtime > "%s") '
            . 'order by starttime desc limit %d offset %d',
            $categoryOQL,
            $date,
            $date,
            $epp,
            $page
        ));

        $params = array_merge($params, [
            'videos' => $response['items'],
            'pager'  => $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $epp,
                'page'        => $page,
                'total'       => $response['total'],
                'route'       => [
                    'name'   => (!$category)
                        ? 'frontend_video_frontpage'
                        : 'frontend_video_frontpage_category',
                    'params' => (!$category)
                        ? []
                        : ['category_name' => $category->name],
                ],
            ]),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateShow(array &$params = []):void
    {
        $author = $this->get('user_repository')->find((int) $params['content']->fk_author);

        $params['content']->author = $author;

        // Get other_videos for widget video most
        $order   = [ 'starttime' => 'DESC' ];
        $filters = [
            'pk_content'             => [[ 'value' => $params['content']->id, 'operator' => '<>' ]],
            'pk_fk_content_category' => [[ 'value' => $params['o_category']->pk_content_category ]],
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

        $params = array_merge($params, [
            'others_videos' => $otherVideos,
            'tags' => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($params['content']->tags)['items']
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * This func overrides the parent function just to
     * propertly generate urls to category frontpages
     **/
    public function getRoute($action, $params = [])
    {
        if ($action == 'list' && array_key_exists('category_name', $params)) {
            return 'frontend_video_frontpage_category';
        }

        return parent::getRoute($action, $params);
    }
}
