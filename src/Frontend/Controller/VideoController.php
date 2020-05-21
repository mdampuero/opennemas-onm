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
     * {@inheritdoc}
     */
    protected $service = 'api.service.video';

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
     */
    protected function hydrateList(array &$params = []): void
    {
        $category = $params['o_category'];
        $date     = date('Y-m-d H:i:s');
        $page     = array_key_exists('page', $params)
            ? (int) $params['page']
            : 1;

        // Invalid page provided as parameter
        if ($page <= 0) {
            throw new ResourceNotFoundException();
        }

        $epp = (int) $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        $categoryOQL = !empty($category)
            ? sprintf(' and category_id=%d', $category->id)
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
            $epp * ($page - 1)
        ));

        // No first page and no contents
        if ($page > 1 && empty($response['items'])) {
            throw new ResourceNotFoundException();
        }

        $params = array_merge($params, [
            'videos'      => $response['items'],
            'pagination'  => $this->get('paginator')->get([
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
        $params['content']->author = $this->get('user_repository')->find(
            (int) $params['content']->fk_author
        );

        $params = array_merge($params, [
            'tags' => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($params['content']->tags)['items']
        ]);
    }

    /**
     * Updates the list of parameters and/or the item when the response for
     * the current request is not cached.
     *
     * @param array $params Thelist of parameters already in set.
     */
    protected function hydrateShowAmp(array &$params = []) : void
    {
        parent::hydrateShowAmp($params);

        $params['content']->author = $this->get('user_repository')->find(
            (int) $params['content']->fk_author
        );
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
