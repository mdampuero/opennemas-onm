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
 * Displays an album or a list of albums.
 */
class AlbumController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list'    => 'gallery-frontpage',
        'show'    => 'gallery-inner',
        'showamp' => 'gallery-inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'ALBUM_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list'    => 'album_frontpage',
        'show'    => 'album_inner',
        'showamp' => 'amp_inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'album_frontpage' => [ 7, 9 ],
        'album_inner'     => [ 7 ],
    ];

    /**
     * The list of valid query parameters per action.
     *
     * @var array
     */
    protected $queries = [
        'list'    => [ 'page', 'category_name' ],
        'showamp' => [ '_format' ],
    ];

    /**
     * The list of routes per action.
     *
     * @var array
     */
    protected $routes = [
        'list' => 'frontend_album_frontpage'
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.album';

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'list'    => 'album/album_frontpage.tpl',
        'show'    => 'album/album.tpl',
        'showamp' => 'amp/content.tpl',
    ];

    /**
     * {@inheritDoc}
     */
    protected function getRoute($action, $params = [])
    {
        if ($action == 'list' && array_key_exists('category_name', $params)) {
            return 'frontend_album_frontpage_category';
        }

        return parent::getRoute($action, $params);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
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
            'content_type_name="album" and content_status=1 and in_litter=0 %s '
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
            'albums'     => $response['items'],
            'pagination' => $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $epp,
                'page'        => $page,
                'total'       => $response['total'],
                'route'       => [
                    'name'   => empty($category)
                        ? 'frontend_album_frontpage'
                        : 'frontend_album_frontpage_category',
                    'params' => !empty($category)
                        ? [ 'category_name' => $category->name ]
                        : []
                ]
            ])
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateShow(array &$params = []) : void
    {
        $params['tags']   = $this->getTags($params['content']);
        $params['author'] = $this->get('user_repository')
            ->find($params['content']->fk_author);

        $cacheIds = array_map(function ($a) {
            return [ 'photo', $a['pk_photo'] ];
        }, $params['content']->photos);

        $photos = $this->get('entity_repository')
            ->findMulti($cacheIds);

        $params['photos'] = $this->get('data.manager.filter')
            ->set($photos)
            ->filter('mapify', [ 'key' => 'pk_photo' ])
            ->get();
    }
}
