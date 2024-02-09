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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Displays a tag or a list of tags.
 */
class TagController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list' => 'frontpages',
        'show' => 'frontpages'
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.tags';

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list' => 'article_inner',
        'show' => 'article_inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'list' => [ 7, 9 ],
        'show' => [ 7, 9 ]
    ];

    /**
     * The list of valid query parameters per action.
     *
     * @var array
     */
    protected $queries = [
        'list' => [ 'page' ],
        'show' => [ 'page', 'slug' ],
    ];

    /**
     * The list of routes per action.
     *
     * @var array
     */
    protected $routes = [
        'list' => 'frontend_tag_index',
        'show' => 'frontend_tag_frontpage'
    ];

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'list'    => 'tag/index.tpl',
        'show'    => 'frontpage/tags.tpl'
    ];

    /**
     * {@inheritdoc}
     */
    public function listAction(Request $request)
    {
        $this->checkSecurity('es.openhost.module.tagsIndex');

        return parent::listAction($request);
    }

    /**
     * {@inheritdoc}
     */
    public function showAction(Request $request)
    {
        $action = $this->get('core.globals')->getAction();
        $item   = $this->getItem($request);
        $params = $request->query->all();
        $xtags  = [];

        // Deprecate resource parameter
        unset($params['resource']);

        $expected = $this->getExpectedUri($action, $params);

        if (strpos($request->getRequestUri(), $expected) === false) {
            return new RedirectResponse($expected, 301);
        }

        $params = $this->getParameters($request, $item);

        $this->view->setConfig($this->getCacheConfiguration($action));

        if (!$this->isCached($params)) {
            $this->hydrateShow($params);
        }

        foreach ($params['contents'] as $content) {
            if ($content->fk_author) {
                $xtags[] = ',author-' . $content->fk_author;
            }
        }

        $params['x-tags'] .= implode(',', array_unique($xtags));

        return $this->render($this->getTemplate($action), $params);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCacheId($params)
    {
        $cacheParams = [
            $this->get('core.globals')->getExtension(),
            $this->get('core.globals')->getAction()
        ];

        $cacheParams[] = $params['item']->id ?? null;
        $cacheParams[] = $params['page'] ?? null;

        return $this->view->getCacheId($cacheParams);
    }

    /**
     * {@inheritdoc}
     */
    protected function getItem(Request $request)
    {
        $slug = $request->get('slug', null);

        try {
            $item = $this->get('api.service.tag')->getList(sprintf(
                'slug = "%s"',
                $slug
            ))['items'];
        } catch (\Exception $e) {
            throw new ResourceNotFoundException();
        }

        if (empty($item)) {
            throw new ResourceNotFoundException();
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($request, $item = null)
    {
        $action = $this->get('core.globals')->getAction();
        $params = parent::getParameters($request, $item[0]);

        // Remove o_content to generate o_canonical_url properly
        unset($params['o_content']);

        // Remove content to avoid problems with structured data tpls
        unset($params['content']);

        return array_merge($params, [
            'item'        => $item[0],
            'items'       => $item,
            'o_canonical' => $this->getCanonicalUrl($action, $params)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateShow(array &$params = []) : void
    {
        $params['epp'] = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_in_blog', 10);

        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        $service = $this->get('api.service.content');
        $now     = date('Y-m-d H:i:s');

        $response = $service->getList(
            sprintf(
                'content_status = 1 and in_litter = 0 and tag_id = %d ' .
                'and fk_content_type in [1, 4, 5, 7, 9, 11, 18, 19] ' .
                'and (starttime is null or starttime < "%s") ' .
                'and (endtime is null or endtime > "%s") ' .
                'order by starttime desc limit %d offset %d',
                $params['item']->id,
                $now,
                $now,
                $params['epp'],
                $params['epp'] * ($params['page'] - 1)
            )
        );

        $contents = $response['items'];
        $total    = $response['total'];

        // No first page and no contents
        if ($params['page'] > 1 && empty($contents)) {
            throw new ResourceNotFoundException();
        }

        $params = array_merge($params, [
            'contents'   => $contents,
            'total'      => $total,
            'tag'        => $params['item'],
            'pagination' => $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $params['epp'],
                'maxLinks'    => 5,
                'page'        => $params['page'],
                'total'       => $total,
                'route'       => [
                    'name'   => 'frontend_tag_frontpage',
                    'params' => [ 'slug' => $params['slug']]
                ]
            ])
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        $tags    = [ '#' => [], '*' => [] ];
        $letters = range('a', 'z');

        $response = $this->get('api.service.tag')
            ->getListByContentTypes([ 'article' ]);

        foreach ($response['items'] as $item) {
            if (is_numeric($item->name[0])) {
                $tags['#'][] = $item;
                continue;
            }

            $slug = $this->get('data.manager.filter')
                ->set($item->name[0])
                ->filter('slug')
                ->get();

            if (!empty($slug) && in_array($slug[0], $letters)) {
                $tags[$slug[0]][] = $item;
                continue;
            }

            $tags['*'][] = $item;
        }

        ksort($tags);

        $params = array_merge($params, [
            'tags' => $tags,
        ]);
    }
}
