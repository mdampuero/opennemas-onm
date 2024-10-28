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

use Api\Exception\GetListException;
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
        'list' => [ 'category_slug', 'page' ]
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
        $params = $request->query->all();

        // Fix category_slug from query basing on item
        $params['category_slug'] = $item->name;

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
        $slug  = $request->query->filter('category_slug', '', FILTER_SANITIZE_STRING);
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

            $category = unserialize($cm->getUrlContent(
                $wsUrl . '/ws/categories/object/' . $slug,
                true
            ));

            if (empty($category)) {
                throw new ResourceNotFoundException();
            }

            // Get all contents for this frontpage
            list($pagination, $articles, $related) = unserialize(
                mb_convert_encoding(
                    $cm->getUrlContent(
                        $wsUrl . '/ws/frontpages/allcontentblog/' . $slug . '/' . $page,
                        true
                    ),
                    'ISO-8859-1',
                    'UTF-8'
                )
            );

            $this->view->assign([
                'articles'   => $articles,
                'related'    => $related,
                'category'   => $category,
                'pagination' => $pagination
            ]);
        }

        $this->getAdvertisements();

        return $this->render('blog/blog.tpl', [
            'cache_id'    => $cacheId,
            'x-cache-for' => '3h',
            'x-cacheable' => true,
            'x-tags'      => 'ext-category,' . $slug . ',' . $page
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * TODO: Remove when only an advertisement group without article_inner.
     */
    protected function getAdvertisements($category = null, $token = null)
    {
        $categoryId = empty($category) ? 0 : $category->id;
        $action     = $this->get('core.globals')->getAction();
        $group      = $this->getAdvertisementGroup($action);

        $positions = array_merge(
            $this->get('core.helper.advertisement')->getPositionsForGroup('all'),
            $this->get('core.helper.advertisement')->getPositionsForGroup($group),
            $this->get('core.helper.advertisement')->getPositionsForGroup('article_inner'),
            $this->getAdvertisementPositions($group)
        );

        $advertisements = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, $categoryId);

        $this->get('frontend.renderer.advertisement')
            ->setPositions($positions)
            ->setAdvertisements($advertisements);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCacheId($params)
    {
        return $this->view->getCacheId(
            $this->get('core.globals')->getExtension(),
            $this->get('core.globals')->getAction(),
            $params['category']->id,
            $params['page']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getItem(Request $request)
    {
        $item = $this->getCategory($request->get('category_slug'));

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
        $service     = $this->get('api.service.content');
        $now         = date('Y-m-d H:i:s');
        $categoryIds = [$params['category']->id];

        // Retrieve and accumulate the IDs of all child categories and their descendants
        array_push($categoryIds, ...$this->get('api.service.category')->getChildIds($params['category']));

        $response = $service->getList(
            sprintf(
                'content_status = 1 and in_litter = 0 and category_id in [%s] ' .
                    'and content_type_name in ["article","event","album","video"] ' .
                    'and (starttime is null or starttime < "%s") ' .
                    'and (endtime is null or endtime > "%s") ' .
                    'order by starttime desc limit %d offset %d',
                implode(',', $categoryIds),
                $now,
                $now,
                $params['epp'],
                $params['epp'] * ($params['page'] - 1)
            )
        );

        return [
            $response['items'],
            $response['total']
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($request, $item = null)
    {
        $action      = $this->get('core.globals')->getAction();
        $extension   = $this->get('core.globals')->getExtension();
        $categoryIds = [$item->id];

        // Retrieve and accumulate the IDs of all child categories and their descendants
        array_push($categoryIds, ...$this->get('api.service.category')->getChildIds($item));

        $categoriesWithPrefix = array_map(function ($category) use ($extension) {
            return $extension . '-' . $category;
        }, $categoryIds);

        $params = array_merge($request->query->all(), [
            'category'   => $item,
            'time'       => time(),
            'o_category' => $item,
            'x-tags'     => implode(',', $categoriesWithPrefix)
        ]);

        if (!array_key_exists('page', $params)) {
            $params['page'] = 1;
        }

        $params['epp'] = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_in_blog', 10);

        // Prevent invalid page when page is not numeric
        $params['page'] = (int) $params['page'];

        $this->getAdvertisements($item);

        return array_merge($this->params, $params, [
            'cache_id'    => $this->getCacheId($params),
            'o_canonical' => $this->getCanonicalUrl($action, $params)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        try {
            list($contents, $total) = $this->getItems($params);
        } catch (GetListException $e) {
            throw new ResourceNotFoundException();
        }

        // No first page and no contents
        if ($params['page'] > 1 && empty($contents)) {
            throw new ResourceNotFoundException();
        }

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);

            $params['x-cache-for'] = $expire;
        }

        $params['articles'] = $contents;

        $xtags = [];
        foreach ($contents as $content) {
            if ($content->fk_author) {
                $xtags[] = ',author-' . $content->fk_author;
            }
        }

        $params['x-tags']    .= implode(',', array_unique($xtags));
        $params['total']      = $total;
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
                    'category_slug' => $params['category']->name
                ]
            ]
        ]);
    }
}
