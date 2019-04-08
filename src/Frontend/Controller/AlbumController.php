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
 * Displays an album or a list of albums.
 */
class AlbumController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list'       => 'gallery-frontpage',
        'listauthor' => 'gallery-frontpage',
        'show'       => 'gallery-inner',
        'showamp'    => 'gallery-inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'ALBUM_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'showamp'    => 'amp_inner',
        'list'       => 'album_frontpage',
        'listauthor' => 'album_frontpage',
        'show'       => 'album_inner',
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
        'list'       => [ 'page', 'category_name' ],
        'showamp'    => [ '_format' ],
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
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'list'       => 'album/album_frontpage.tpl',
        'show'       => 'album/album.tpl',
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

        $albumSettings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('album_settings');
        $epp           = isset($albumSettings['total_front']) ? $albumSettings['total_front'] : 8;
        $orderBy       = isset($albumSettings['orderFrontpage']) ? $albumSettings['orderFrontpage'] : 'created';
        $order         = ($orderBy == 'favorite') ? 'favorite desc, created des' : 'created desc';

        $categoryOQL = ($category)
            ? sprintf(' and pk_fk_content_category=%d', $category->pk_content_category)
            : '';

        $response = $this->get('api.service.content_old')->getList(sprintf(
            'content_type_name="album" and content_status=1 and in_litter=0 %s '
            . 'and (starttime IS NULL or starttime < "%s") '
            . 'and (endtime IS NULL or endtime > "%s") '
            . 'order by %s limit %d offset %d',
            $categoryOQL,
            $date,
            $date,
            $order,
            $epp,
            $page
        ));

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
                    'name'   => (!$category)
                        ? 'frontend_album_frontpage'
                        : 'frontend_album_frontpage_category',
                    'params' => (!$category)
                        ? []
                        : ['category_name' => $category->name],
                ]
            ])
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateShow(array &$params = []):void
    {
        $params['content']->author =
            $this->get('user_repository')->find((int) $params['content']->fk_author);

        $params = array_merge($params, [
            'album_photos' => $params['content']->_getAttachedPhotos($params['content']->id),
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * This func overrides the parent function just to
     * propertly generate urls to category frontpages
     **/
    protected function getRoute($action, $params = [])
    {
        if ($action == 'list' && array_key_exists('category_name', $params)) {
            return 'frontend_album_frontpage_category';
        }

        return parent::getRoute($action, $params);
    }
}
