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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CategoryController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list' => 'frontpages'
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list' => 'category_frontpage'
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'category_frontpage' => [ 7, 9 ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $queries = [
        'list' => [ 'category_name', 'page' ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $routes = [
        'list' => 'category_frontpage'
    ];

    /**
     * {@inheritdoc}
     */
    protected $templates = [
        'list' => 'blog/blog.tpl'
    ];

    /**
     * {@inheritdoc}
     */
    public function listAction(Request $request)
    {
        $action = $this->get('core.globals')->getAction();
        $item   = $this->getItem($request);
        $params = $this->getQueryParameters($action, $request->query->all());

        // Fix category_name from query basing on item
        $params['category_name'] = $item->name;

        $expected = $this->getExpectedUri($action, $params);

        if (strpos($request->getRequestUri(), $expected) === false) {
            return new RedirectResponse($expected, 301);
        }

        $params = $this->getParameters($request, $item);

        $this->view->setConfig($this->getCacheConfiguration($action));

        if (!$this->isCached($params)) {
            $this->hydrateList($params);
        }

        return $this->render($this->getTemplate($action), $params);
    }

    /**
     * Action for synchronized blog frontpage.
     *
     * @param Request The request object.
     *
     * @return Response The response object.
     */
    public function extCategoryAction(Request $request)
    {
        $slug  = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $page  = (int) $request->query->get('page', 1);
        $wsUrl = $this->get('core.helper.instance_sync')->getSyncUrl($slug);

        if (empty($wsUrl)) {
            throw new ResourceNotFoundException();
        }

        $this->view->setConfig('frontpages');

        $cacheId = $this->view->getCacheId('category', 'sync', $slug, $page);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('blog/blog.tpl', $cacheId)
        ) {
            $cm = new \ContentManager();

            // Get category object
            $category = unserialize(
                $cm->getUrlContent(
                    $wsUrl . '/ws/categories/object/' . $slug,
                    true
                )
            );

            // Get all contents for this frontpage
            list($pagination, $articles) = unserialize(
                utf8_decode(
                    $cm->getUrlContent(
                        $wsUrl . '/ws/frontpages/allcontentblog/' . $slug . '/' . $page,
                        true
                    )
                )
            );

            $this->view->assign([
                'articles'   => $articles,
                'category'   => $category,
                'pagination' => $pagination
            ]);
        }

        list($positions, $advertisements) = $this->getAdvertisements();

        return $this->render('blog/blog.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheId,
            'x-cache-for'    => '+3 hour',
            'x-cacheable'    => true,
            'x-tags'         => 'ext-category,' . $slug . ',' . $page,
        ]);
    }

    /**
     * Extracts a list of media ids and a list of user ids from every content in
     * the list of contents.
     *
     * @param array $contents The list of contents.
     *
     * @return array The list with the list of media ids and the list of user
     *               ids.
     */
    protected function extractIds($contents)
    {
        $media = [];
        $users = [];

        foreach ($contents as $content) {
            $users[] = $content->fk_author;

            if (isset($content->img1) && !empty($content->img1)) {
                $media[] = $content->img1;
            } elseif (!empty($content->fk_video)) {
                $media[] = $content->fk_video;
            }
        }

        return [ $media, $users ];
    }

    /**
     * {@inheritdoc}
     *
     * TODO: Remove when only an advertisement group.
     */
    protected function getAdvertisements($category = null, $token = null)
    {
        $categoryId = empty($category) ? 0 : $category->pk_content_category;
        $action     = $this->get('core.globals')->getAction();
        $group      = $this->getAdvertisementGroup($action);

        $positions = array_merge(
            $this->get('core.helper.advertisement')->getPositionsForGroup($group),
            $this->get('core.helper.advertisement')->getPositionsForGroup('article_inner'),
            $this->getAdvertisementPositions($group)
        );

        $advertisements = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, $categoryId);

        return [ $positions, $advertisements ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getCacheId($params)
    {
        return $this->view->getCacheId(
            $this->get('core.globals')->getExtension(),
            $this->get('core.globals')->getAction(),
            $params['category']->pk_content_category,
            $params['page']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getItem(Request $request)
    {
        $item = $this->getCategory($request->get('category_name'));

        if (!$item->enabled) {
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
        $em = $this->get('entity_repository');

        $now      = date('Y-m-d H:i:s');
        $order    = [ 'starttime' => 'DESC', 'pk_content' => 'DESC' ];
        $criteria = [
            'pk_fk_content_category' => [
                [ 'value' => $params['category']->pk_content_category ]
            ],
            'fk_content_type' => [
                [ 'value' => [1, 7, 9], 'operator' => 'IN' ]
            ],
            'content_status' => [ [ 'value' => 1 ] ],
            'in_litter'      => [ [ 'value' => 1, 'operator' => '!=' ]],
            'starttime'      => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => $now, 'operator' => '<=' ],
            ],
            'endtime' => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => $now, 'operator' => '>' ],
            ]
        ];

        return [
            $em->findBy($criteria, $order, $params['epp'], $params['page']),
            $em->countBy($criteria)
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($request, $item = null)
    {
        $action = $this->get('core.globals')->getAction();
        $params = array_merge($request->query->all(), [
            'category'   => $item,
            'time'       => time(),
            'o_category' => $item,
            'x-tags'     => $this->get('core.globals')->getExtension()
                . ',' . $item->pk_content_category,
        ]);

        if (!array_key_exists('page', $params)) {
            $params['page'] = 1;
        }

        $params['epp'] = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_in_blog', 10);

        // Prevent invalid page when page is not numeric
        $params['page'] = (int) $params['page'];

        list($positions, $advertisements) = $this->getAdvertisements($item);

        return array_merge($this->params, $params, [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $this->getCacheId($params),
            'o_canonical'    => $this->getCanonicalUrl($action, $params)
        ]);
    }

    /**
     * Returns a list of contents, where the key is the pk_content and the value
     * is the content, basing on a list of ids.
     *
     * @param array $ids The list of ids.
     *
     * @return array The list of contents.
     */
    protected function getMedia($ids)
    {
        if (empty($ids)) {
            return [];
        }

        $media = $this->get('entity_repository')->findBy([
            'pk_content' => [ [ 'value' => $ids, 'operator' => 'IN' ] ]
        ], []);

        return $this->get('data.manager.filter')
            ->set($media)
            ->filter('mapify', [ 'key' => 'pk_content' ])
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        // Invalid page provided as parameter
        if ($params['page'] <= 0) {
            throw new ResourceNotFoundException();
        }

        list($contents, $total) = $this->getItems($params);

        // No first page and no contents
        if ($params['page'] > 1 && empty($contents)) {
            throw new ResourceNotFoundException();
        }

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);

            $params['x-cache-for'] = $expire;
        }

        list($mediaIds, $userIds) = $this->extractIds($contents);

        $params['o_media'] = $this->getMedia($mediaIds);

        $this->hydrateContents($contents, $params);

        $params['articles']   = $contents;
        $params['pagination'] = $this->get('paginator')->get([
            'directional' => true,
            'boundary'    => false,
            'epp'         => $params['epp'],
            'maxLinks'    => 5,
            'page'        => $params['page'],
            'total'       => $total,
            'route'       => [
                'name'   => 'category_frontpage',
                'params' => [
                    'category_name' => $params['category']->name
                ]
            ]
        ]);
    }

    /**
     * Adds more information to contents in the list of contents by using all
     * parameters collected during the current request.
     *
     * @param array $contents The list of contents.
     * @param array $params   The list of parameters.
     */
    protected function hydrateContents($contents, $params)
    {
        foreach ($contents as &$content) {
            $content->loadRelatedContents($params['category']->name);

            if (isset($content->img1)
                && !empty($content->img1)
                && array_key_exists($content->img1, $params['o_media'])
            ) {
                $image = $params['o_media'][$content->img1];

                $content->img1_path = $image->path_file . $image->name;
                $content->img1      = $image;

                continue;
            }

            if (!empty($content->fk_video)
                && array_key_exists($content->fk_video, $params['o_media'])
            ) {
                $video = $params['o_media'][$content->fk_video];

                $content->video     = $video;
                $content->obj_video = $video;
            }
        }
    }
}
