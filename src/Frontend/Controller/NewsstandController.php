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
 * Displays a newsstand or a list of newsstands.
 */
class NewsstandController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list'    => 'kiosko',
        'show'    => 'kiosko',
        'showamp' => 'kiosko',
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'KIOSKO_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list'    => 'frontpage',
        'show'    => 'frontpage',
        'showamp' => 'amp_inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'album_frontpage' => [ 103, 105 ],
        'album_inner'     => [ 103, 105 ],
    ];

    /**
     * The list of valid query parameters per action.
     *
     * @var array
     */
    protected $queries = [
        'list'    => [ 'page', 'year', 'month' ],
        'showamp' => [ '_format' ],
    ];

    /**
     * The list of routes per action.
     *
     * @var array
     */
    protected $routes = [
        'list' => 'frontend_newsstand_frontpage'
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.newsstand';

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'list'    => 'newsstand/list.tpl',
        'show'    => 'newsstand/item.tpl',
        'showamp' => 'amp/content.tpl',
    ];


    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        $page = $params['page'] ?? 1;
        $date = date('Y-m-d H:i:s');

        $epp = (int) $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        $epp = 1;

        $response = $this->get('api.service.content_old')->getList(sprintf(
            'content_type_name="kiosko" and content_status=1 and in_litter=0 '
            . 'and (starttime IS NULL or starttime < "%s") '
            . 'and (endtime IS NULL or endtime > "%s") '
            . 'order by starttime desc limit %d offset %d',
            $date,
            $date,
            $epp,
            $epp * ($page - 1)
        ));

        $params = array_merge($params, [
            'items'      => $response['items'],
            'pagination' => $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $epp,
                'page'        => $page,
                'total'       => $response['total'],
                'route'       => [
                    'name'   => empty($category)
                        ? 'frontend_newsstand_frontpage'
                        : 'frontend_newsstand_frontpage_category',
                    'params' => empty($category)
                        ? []
                        : [ 'category_name' => $category->name ],
                ]
            ])
        ]);
    }
}
