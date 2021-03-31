<?php

namespace Frontend\Controller;

use Api\Exception\GetItemException;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class OpinionController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list'       => 'opinion',
        'listauthor' => 'opinion',
        'show'       => 'opinion',
        'showamp'    => 'opinion',
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'OPINION_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'showamp'    => 'amp_inner',
        'list'       => 'opinion_frontpage',
        'listauthor' => 'opinion_frontpage',
        'show'       => 'opinion_inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'opinion_frontpage' => [ 7, 9 ],
        'opinion_inner'     => [ 7 ],
    ];

    /**
     * {@inheritdoc}
     */
    protected $queries = [
        'list'       => [ 'page' ],
        'listauthor' => [ 'author_id', 'author_slug', 'page' ],
        'showamp'    => [ '_format' ],
    ];

    /**
     * {@inheritdoc}
     */
    protected $routes = [
        'listauthor' => 'frontend_opinion_author_frontpage',
        'list'       => 'frontend_opinion_frontpage'
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.opinion';

    /**
     * {@inheritdoc}
     */
    protected $templates = [
        'list'       => 'opinion/opinion_frontpage.tpl',
        'listauthor' => 'opinion/opinion_author_index.tpl',
        'showamp'    => 'amp/content.tpl',
    ];

    /**
     * Renders the opinion author's frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function listAuthorAction(Request $request)
    {
        $this->checkSecurity($this->extension);

        $id = (int) $request->get('author_id', null);

        try {
            $author = $this->container->get('api.service.author')
                ->getItem($id);
        } catch (GetItemException $e) {
            throw new ResourceNotFoundException();
        }

        if (!empty($author->is_blog)) {
            return new RedirectResponse(
                $this->generateUrl('frontend_blog_author_frontpage', [
                    'author_slug' => $author->slug
                ])
            );
        }

        $action = $this->get('core.globals')->getAction();

        $expected = $this->get('core.helper.url_generator')->generate($author);
        $expected = $this->get('core.helper.l10n_route')->localizeUrl($expected);

        if ($request->getPathInfo() !== $expected) {
            return new RedirectResponse($expected);
        }

        $params = $this->getParameters($request);
        $this->view->setConfig($this->getCacheConfiguration($action));

        if (!$this->isCached($params)) {
            $this->hydrateListAuthor($params, $author);
        }

        return $this->render($this->getTemplate($action), $params);
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($params, $item = null)
    {
        $params = parent::getParameters($params, $item);

        if (!empty($item)) {
            $params[$item->content_type_name] = $item;

            if (array_key_exists('bodyLink', $item->params)) {
                $params['o_external_link'] = $item->params['bodyLink'];
            }
        }

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        $date = date('Y-m-d H:i:s');

        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        $epp = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        $filters = [
            'content_status' => [['value' => 1]],
            'in_litter'      => [['value' => 0]],
            'starttime' => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00', 'operator' => '=' ],
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => $date, 'operator' => '<=' ],
            ],
            'endtime' => [
                'union'   => 'OR',
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => '0000-00-00 00:00:00', 'operator' => '=' ],
                [ 'value' => $date, 'operator' => '>' ]
            ],
        ];

        $order['starttime'] = 'DESC';

        $em = $this->get('opinion_repository');

        $bloggers = $this->get('api.service.author')
            ->getList('is_blog = 1 order by name asc');

        if (!empty($bloggers['total'])) {
            $filters = array_merge($filters, [
                'opinions`.`fk_author' => [ [
                    'value' => array_map(function ($author) {
                        return $author->id;
                    }, $bloggers['items']),
                    'operator' => 'NOT IN'
                    ] ]
            ]);
        }

        $opinions = $em->findBy($filters, $order, $epp, $params['page']);
        $total    = $em->countBy($filters);

        // No first page and no contents
        if ($params['page'] > 1 && empty($opinions)) {
            throw new ResourceNotFoundException();
        }

        $pagination = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $epp,
            'page'        => $params['page'],
            'total'       => $total,
            'route'       => 'frontend_opinion_frontpage'
        ]);

        $params = array_merge($params, [
            'opinions'   => $opinions,
            'pagination' => $pagination,
            'page'       => $params['page']
        ]);

        $this->view->assign($params);
    }

    /**
     * {@inheritdoc}
     *
     * Action specific for the opinion author frontpage
     */
    protected function hydrateListAuthor(array &$params, $author)
    {
        $date = date('Y-m-d H:i:s');

        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        // Total opinions per page
        $epp = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page');

        $filters = [
            'content_status'       => [['value' => 1]],
            'in_litter'            => [['value' => 0]],
            'content_type_name'    => [['value' => 'opinion']],
            'opinions`.`fk_author' => [['value' => $author->id]],
            'starttime' => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS' ],
                [ 'value' => '0000-00-00 00:00:00', 'operator' => '=' ],
                [ 'value' => $date, 'operator' => '<' ]
            ],
            'endtime' => [
                'union'   => 'OR',
                [ 'value'  => null, 'operator'      => 'IS' ],
                [ 'value' => '0000-00-00 00:00:00', 'operator' => '=' ],
                [ 'value' => $date, 'operator' => '>' ]
            ],
        ];

        $orderBy = ['created' => 'DESC'];

        // Get the number of total opinions for this author for pagination purposes
        $total    = $this->get('opinion_repository')->countBy($filters);
        $opinions = $this->get('opinion_repository')
            ->findBy($filters, $orderBy, $epp, $params['page']);

        // No first page and no contents
        if ($params['page'] > 1 && empty($opinions)) {
            throw new ResourceNotFoundException();
        }

        $pagination = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $epp,
            'page'        => $params['page'],
            'total'       => $total,
            'route'       => [
                'name'   => 'frontend_opinion_author_frontpage',
                'params' => [
                    'author_id'   => $author->id,
                    'author_slug' => $author->slug,
                ]
            ]
        ]);

        $this->view->assign([
            'pagination' => $pagination,
            'opinions'   => $opinions,
            'author'     => $author,
            'page'       => $params['page'],
        ]);
    }
}
