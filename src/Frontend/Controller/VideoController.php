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
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []): void
    {
        $category = $params['o_category'];
        $date     = date('Y-m-d H:i:s');

        $categoryOQL = !empty($category)
            ? sprintf(' and category_id=%d', $category->id)
            : '';

        $response = $this->get('api.service.content')->getList(sprintf(
            'content_type_name="video" and content_status=1 and in_litter=0 %s '
            . 'and (starttime is null or starttime < "%s") '
            . 'and (endtime is null or endtime > "%s") '
            . 'order by starttime desc limit %d offset %d',
            $categoryOQL,
            $date,
            $date,
            $params['epp'],
            $params['epp'] * ($params['page'] - 1)
        ));

        // No first page and no contents
        if ($params['page'] > 1 && empty($response['items'])) {
            throw new ResourceNotFoundException();
        }

        $params['x-tags'] .= ',video-frontpage';

        if (!empty($category)) {
            $params['x-tags'] .= sprintf(',category-video-%d', $category->id);
        }

        $params = array_merge($params, [
            'videos'      => $response['items'],
            'total'       => $response['total'],
            'pagination'  => $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $params['epp'],
                'page'        => $params['page'],
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
