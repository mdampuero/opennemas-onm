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
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays an video frontpage and video inner.
 */
class VideoController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list'    => 'video',
        'show'    => 'video-inner',
        'showamp' => 'video-inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'VIDEO_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'showamp' => 'amp_inner',
        'list'    => 'video_frontpage',
        'show'    => 'video_inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'video_frontpage' => [ 7, 9 ],
        'video_inner'     => [ 7 ],
    ];

    /**
     * {@inheritdoc}
     */
    protected $queries = [
        'list'       => [ 'page', 'category_slug' ],
        'showamp'    => [ '_format' ],
    ];

    /**
     * {@inheritdoc}
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
     * Returns the item based on the slug.
     *
     * @param Request $request The request.
     *
     * @return Content The item to return.
     */
    protected function getItem(Request $request)
    {
        try {
            $item = $this->get('api.service.content')
                ->getItemBySlugAndContentType(
                    $request->get('slug'),
                    \ContentManager::getContentTypeIdFromName('video')
                );
        } catch (\Exception $e) {
            throw new ResourceNotFoundException();
        }

        if (!$this->get('core.helper.content')->isReadyForPublish($item)) {
            throw new ResourceNotFoundException();
        }

        return $item;
    }

    /**
     * Returns the list of items basing on a list of parameters.
     *
     * @param array $params The list of parameters.
     *
     * @return array The list of items.
     */
    protected function getItems($params)
    {
        $date     = date('Y-m-d H:i:s');
        $offset   = $params['epp'] * ($params['page'] - 1);
        $category = $params['o_category'];

        $replacements = [
                $date,
                $date,
                $params['epp'],
                $offset
            ];

        $query = "SELECT SQL_CALC_FOUND_ROWS DISTINCT pk_content, contentmeta.meta_value AS type " .
            "FROM contents join contentmeta " .
            "on contents.pk_content = contentmeta.fk_content " .
            "and contentmeta.meta_name = 'type' ";

        if (!empty($category)) {
            array_unshift($replacements, $category->name);
            $query .= "JOIN content_category on contents.pk_content = content_category.content_id " .
                "JOIN category on category.name = ? " .
                "and content_category.category_id = category.id ";
        }

        $query .= "WHERE fk_content_type = 9 AND content_status = 1 and in_litter = 0 "
            . "AND (starttime IS NULL OR starttime <= ? ) "
            . "AND (endtime IS NULL OR endtime > ?) "
            . " ORDER BY pk_content DESC LIMIT ? OFFSET ?";

        $videoIds = $this->get('orm.manager')->getConnection('instance')
            ->fetchAll(
                $query,
                $replacements
            );

        $sql = 'SELECT FOUND_ROWS()';

        $total = $this->get('orm.manager')->getConnection('instance')
            ->fetchAssoc($sql);

        $total = array_pop($total);

        $contents = $this->get('api.service.content')
            ->getListByIds(array_map(function ($video) {
                return $video['pk_content'];
            }, $videoIds));

        return [
            $contents['items'],
            $total
        ];
    }


    /**
     * {@inheritdoc}
     */
    protected function getParameters($request, $item = null)
    {
        $params = parent::getParameters($request, $item);

        if (!array_key_exists('page', $params)) {
            $params['page'] = 1;
        }

        $params['epp'] = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);
        // Prevent invalid page when page is not numeric
        $params['page'] = (int) $params['page'];

        list($positions, $advertisements) = $this->getAdvertisements($item);

        return array_merge($this->params, $params, [
            'cache_id'       => $this->getCacheId($params),
            'ads_positions'  => $positions,
            'advertisements' => $advertisements
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []): void
    {
        $category = $params['o_category'];

        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        list($contents, $total) = $this->getItems($params);

        $epp = (int) $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        // No first page and no contents
        if ($params['page'] > 1 && empty($contents)) {
            throw new ResourceNotFoundException();
        }

        $params = array_merge($params, [
            'videos'      => $contents,
            'pagination'  => $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $epp,
                'page'        => $params['page'],
                'total'       => $total,
                'route'       => [
                    'name'   => (!$category)
                        ? 'frontend_video_frontpage'
                        : 'frontend_video_frontpage_category',
                    'params' => (!$category)
                        ? []
                        : ['category_slug' => $category->name],
                ],
            ]),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateShow(array &$params = []):void
    {
        $params = array_merge($params, [
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
        if ($action == 'list' && array_key_exists('category_slug', $params)) {
            return 'frontend_video_frontpage_category';
        }

        return parent::getRoute($action, $params);
    }
}
