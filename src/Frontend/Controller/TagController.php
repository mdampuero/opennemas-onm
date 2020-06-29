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
        $params = $this->getQueryParameters($action, $request->query->all());

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
        $locale = $this->get('core.locale')->getRequestLocale();
        $slug   = $request->get('slug', null);

        try {
            $item = $this->get('api.service.tag')->getList(sprintf(
                '(locale = "%s" or locale is null) and slug = "%s"',
                $locale,
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
        $page = (int) ($params['page'] ?? 1);
        $epp  = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_in_blog', 10);

        $ids = array_map(function ($a) {
            return $a->id;
        }, $params['items']);

        $criteria = [
            'join' => [
                [
                    'table' => 'contents_tags',
                    'type'  => 'inner',
                    'content_id' => [
                        [ 'value' => 'pk_content', 'field' => true ]
                    ],
                    'tag_id' => [
                        [ 'value' => $ids, 'operator' => 'in' ]
                    ]
                ]
            ],
            'fk_content_type' => [
                [ 'value' => [ 1, 4, 7, 9, 11 ], 'operator' => 'in' ],
            ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 0 ] ],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        $em       = $this->get('entity_repository');
        $contents = $em->findBy($criteria, 'starttime DESC', $epp, $page);
        $total    = $em->countBy($criteria);

        // No first page and no contents
        if ($page > 1 && empty($contents)) {
            throw new ResourceNotFoundException();
        }

        // TODO: review this piece of CRAP
        foreach ($contents as &$item) {
            if (isset($item->img1) && ($item->img1 > 0)) {
                $image = $em->find('Photo', $item->img1);
                if (is_object($image) && !is_null($image->id)) {
                    $item->img1_path = $image->path_file . $image->name;
                    $item->img1      = $image;
                }
            } elseif ($item->fk_content_type == 7) {
                $image           = $em->find('Photo', $item->cover_id);
                $item->img1_path = $image->path_file . $image->name;
                $item->img1      = $image;
            } elseif ($item->fk_content_type == 9) {
                $item->obj_video = $item;
                $item->summary   = $item->description;
            }

            if (isset($item->fk_video) && ($item->fk_video > 0)) {
                $item->video = $em->find('Video', $item->fk_video2);
            }
        }

        $params = array_merge($params, [
            'contents'   => $contents,
            'tag'        => $params['item'],
            'pagination' => $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $epp,
                'maxLinks'    => 0,
                'page'        => $page,
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

            if (in_array($slug[0], $letters)) {
                $tags[$slug[0]][] = $item;
                continue;
            }

            $tags['*'][] = $tag;
        }

        ksort($tags);

        $params = array_merge($params, [
            'tags' => $tags,
        ]);
    }
}
