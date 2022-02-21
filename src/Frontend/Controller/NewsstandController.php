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
        'newsstand_frontpage' => [ 103, 105 ],
        'newsstand_inner'     => [ 103, 105 ],
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
    protected $service = 'api.service.content';

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
     * {@inheritDoc}
     */
    protected function getRoute($action, $params = [])
    {
        if ($action == 'list' && array_key_exists('year', $params)) {
            return 'frontend_newsstand_frontpage_date';
        }

        return parent::getRoute($action, $params);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        $now = date('Y-m-d H:i:s');

        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        $epp = (int) $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        $oql = sprintf(
            'content_type_name="kiosko" and content_status=1 and in_litter=0 '
            . 'and (starttime is null or starttime < "%s") '
            . 'and (endtime is null or endtime > "%s") '
            . 'order by starttime desc limit %d offset %d',
            $now,
            $now,
            $epp,
            $epp * ($params['page'] - 1)
        );

        $params['x-tags'] .= ',kiosko-frontpage';

        if (array_key_exists('year', $params)) {
            $start = sprintf('%02d-%02d-01', $params['year'], $params['month']);
            $end   = date("Y-m-d", strtotime("+1 month", strtotime($start)));

            $oql = sprintf(
                'content_type_name="kiosko" and content_status=1 and in_litter=0 '
                . 'and (starttime is null or starttime < "%s") '
                . 'and (endtime is null or endtime > "%s") '
                . 'and date >= "%s" and date < "%s" '
                . 'order by starttime desc limit %d offset %d',
                $now,
                $now,
                $start,
                $end,
                $epp,
                $epp * ($params['page'] - 1)
            );

            $params['x-tags'] .= '-' . implode('-', [ $params['year'], $params['month'], $params['day'] ]);
        }

        $response = $this->get($this->service)->getList($oql);

        // No first page and no contents
        if ($params['page'] > 1 && empty($response['items'])) {
            throw new ResourceNotFoundException();
        }

        $params = array_merge($params, [
            'items'      => $response['items'],
            'pagination' => $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $epp,
                'page'        => $params['page'],
                'total'       => $response['total'],
                'route'       => [
                    'name'   => empty($params['year'])
                        ? 'frontend_newsstand_frontpage'
                        : 'frontend_newsstand_frontpage_date',
                    'params' => empty($params['year'])
                        ? []
                        : [
                            'day' => $params['day'],
                            'month' => $params['month'],
                            'year' => $params['year']
                        ],
                ]
            ])
        ]);
    }
}
