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
        'frontpage' => 'opinion',
        'show'      => 'opinion',
        'showamp'   => 'opinion',
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'OPINION_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'showamp'         => 'amp_inner',
        'frontpage'       => 'opinion_frontpage',
        'frontpageauthor' => 'opinion_frontpage',
        'show'            => 'opinion_inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'opinion_frontpage' => [ 7, 9 ],
        'opinion_inner'     => [ 7 ],
    ];

    /**
     * The list of valid query parameters per action.
     *
     * @var array
     */
    protected $queries = [
        'frontpage'       => [ 'page' ],
        'frontpageauthor' => [ 'author_id' ],
        'showamp'         => [ '_format' ],
    ];

    /**
     * The list of routes per action.
     *
     * @var array
     */
    protected $routes = [
        'frontpageauthor' => 'frontend_opinion_author_frontpage'
    ];

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'frontpage'       => 'opinion/opinion_frontpage.tpl',
        'frontpageauthor' => 'opinion/opinion_author_index.tpl',
        'showamp'         => 'amp/content.tpl',
    ];

    /**
     * Renders the opinion frontpage.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function frontpageAction(Request $request)
    {
        $action = $this->get('core.globals')->getAction();
        $route  = $this->getRoute($action);

        $expected = $this->get('router')->generate($route);
        $expected = $this->get('core.helper.l10n_route')->localizeUrl($expected);

        if (!$this->get('core.security')->hasExtension($this->extension)) {
            throw new ResourceNotFoundException();
        }

        if ($request->getPathInfo() !== $expected) {
            return new RedirectResponse($expected);
        }

        $params = $this->getParameters($request);
        $this->view->setConfig($this->getCacheConfiguration($action));

        if (!$this->isCached($params)) {
            $this->hydrateFrontpage($params);
        }

        return $this->render($this->getTemplate($action), $params);
    }

    /**
     * Renders the opinion author's frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function frontpageAuthorAction(Request $request)
    {
        $authorID = (int) $request->get('author_id', null);
        $author   = $this->get('user_repository')->find($authorID);
        if (is_null($author)) {
            throw new ResourceNotFoundException();
        }

        if (array_key_exists('is_blog', $author->meta)
            && $author->params['is_blog'] == 1
        ) {
            return new RedirectResponse(
                $this->generateUrl(
                    'frontend_blog_author_frontpage',
                    ['author_slug' => $author->username]
                )
            );
        }

        $action = $this->get('core.globals')->getAction();

        $expected = $this->get('core.helper.url_generator')->generate($author);
        $expected = $this->get('core.helper.l10n_route')->localizeUrl($expected);

        if (!$this->get('core.security')->hasExtension($this->extension)) {
            throw new ResourceNotFoundException();
        }

        if ($request->getPathInfo() !== $expected) {
            return new RedirectResponse($expected);
        }

        $params = $this->getParameters($request);
        $this->view->setConfig($this->getCacheConfiguration($action));

        if (!$this->isCached($params)) {
            $this->hydrateFrontpageAuthor($params, $author);
        }

        return $this->render($this->getTemplate($action), $params);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateShow($params = [], $item = null)
    {
        $author = $this->get('user_repository')->find((int) $item->fk_author);

        $item->author = $author;
        if (is_object($author)
            && is_array($author->meta)
            && array_key_exists('is_blog', $author->meta)
            && $author->meta['is_blog'] == 1
        ) {
            return new RedirectResponse(
                $this->generateUrl('frontend_blog_show', [
                    'blog_id'     => $item->pk_content,
                    'author_name' => $author->username,
                    'blog_title'  => $item->slug,
                ])
            );
        }

        // Associated media code
        if (isset($item->img2) && ($item->img2 > 0)) {
            $photo = $this->get('opinion_repository')->find('Photo', $item->img2);

            $params['photo'] = $photo;
        }

        // Fran is sad about this
        $item->author_name_slug = \Onm\StringUtils::getTitle($item->name);

        $params = array_merge($params, [
            'author' => $author,
        ]);

        $this->view->assign($params);
    }

    /**
     * {@inheritdoc}
     *
     * Action specific for the frontpage
     */
    protected function hydrateFrontpage(array $params) : void
    {
        $epp  = $this->get('orm.manager')->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);
        $page = array_key_exists('page', $params) ? $params['page'] : 1;

        $date    = date('Y-m-d H:i:s');
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

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')->get('opinion_settings', [
                'total_editorial' => 2,
                'total_opinions'  => $epp
            ]);

        // Fetch last editorial opinions from editorial
        if ($settings['total_editorial'] > 0) {
            $filters['type_opinion'] = [['value' => 1]];

            $editorialContents = $em->findBy($filters, $order, $settings['total_editorial'], $page);

            foreach ($editorialContents as &$opinion) {
                if (isset($opinion->img1) && ($opinion->img1 > 0)) {
                    $opinion->img1 = $this->get('entity_repository')
                        ->find('Photo', $opinion->img1);
                }
            }

            $params['editorial'] = $editorialContents;
        }

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

        $filters['type_opinion'] = [['value' => 0]];
        $epp                     = $settings['total_opinions'] ?? $epp;

        $opinions      = $em->findBy($filters, $order, $epp, $page);
        $countOpinions = $em->countBy($filters);

        $pagination = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $epp,
            'page'        => $page,
            'total'       => $countOpinions,
            'route'       => 'frontend_opinion_frontpage'
        ]);

        $authors = [];
        $ur      = $this->get('user_repository');
        foreach ($opinions as &$opinion) {
            if (!array_key_exists($opinion->fk_author, $authors)) {
                $authors[$opinion->fk_author] = $ur->find($opinion->fk_author);
            }

            $opinion->author = $authors[$opinion->fk_author];

            if (isset($opinion->author)
                && (empty($opinion->author->meta)
                || !array_key_exists('is_blog', $opinion->author->meta)
                || $opinion->author->meta['is_blog'] == 0)
            ) {
                $opinion->name             = $opinion->author->name;
                $opinion->author_name_slug = \Onm\StringUtils::getTitle($opinion->name);

                if ($opinion->img1 > 0) {
                    $opinion->img1 = $this->get('entity_repository')
                        ->find('Photo', $opinion->img1);
                }

                $opinion->author->uri = \Uri::generate('opinion_author_frontpage', [
                    'slug' => urlencode(\Onm\StringUtils::generateSlug($opinion->author->name)),
                    'id'   => sprintf('%06d', $opinion->author->id)
                ]);
            }
        }

        $params = array_merge($params, [
            'opinions'   => $opinions,
            'authors'    => $authors,
            'pagination' => $pagination,
            'page'       => $page
        ]);

        $this->view->assign($params);
    }

    /**
     * {@inheritdoc}
     *
     * Action specific for the opinion author frontpage
     */
    protected function hydrateFrontpageAuthor(array $params, $author)
    {
        $page = $params['page'] ?? 1;

        // Setting filters for the further SQLs
        $date    = date('Y-m-d H:i:s');
        $filters = [
            'content_status'    => [['value' => 1]],
            'content_type_name' => [['value' => 'opinion']],
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

        if ($author->id == 1 && $author->username == 'editorial') {
            // Editorial
            $filters['type_opinion'] = [['value' => 1]];

            $author->slug = 'editorial';
        } elseif ($author->id == 2 && $author->username == 'director') {
            // Director
            $filters['type_opinion'] = [['value' => 2]];

            $author->slug = 'director';
        } else {
            // Regular authors
            $filters['type_opinion']         = [['value' => 0]];
            $filters['opinions`.`fk_author'] = [['value' => $author->id]];

            $author->slug = \Onm\StringUtils::getTitle($author->name);
        }

        $orderBy = ['created' => 'DESC'];

        // Total opinions per page
        $numOpinions = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page');
        if (!empty($configurations)
            && array_key_exists('total_opinions', $configurations)
        ) {
            $numOpinions = $configurations['total_opinions'];
        }

        // Get the number of total opinions for this author for pagination purposes
        $countOpinions = $this->get('opinion_repository')->countBy($filters);
        $opinions      = $this->get('opinion_repository')->findBy($filters, $orderBy, $numOpinions, $page);

        foreach ($opinions as &$opinion) {
            // Get author uri
            $opinion->author_uri = $this->generateUrl(
                'frontend_opinion_author_frontpage',
                [
                    'author_id'   => sprintf('%06d', $author->id),
                    'author_slug' => $author->slug,
                ]
            );

            // Get opinion image
            if (isset($opinion->img1) && ($opinion->img1 > 0)) {
                $opinion->img1 = $this->get('entity_repository')->find('Photo', $opinion->img1);
            }
        }

        $pagination = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $numOpinions,
            'page'        => $page,
            'total'       => $countOpinions,
            'route'       => [
                'name'   => 'frontend_opinion_author_frontpage',
                'params' => [
                    'author_id'   => sprintf('%06d', $author->id),
                    'author_slug' => $author->slug,
                ]
            ]
        ]);

        $this->view->assign([
            'pagination' => $pagination,
            'opinions'   => $opinions,
            'author'     => $author,
            'page'       => $page,
        ]);
    }
}
