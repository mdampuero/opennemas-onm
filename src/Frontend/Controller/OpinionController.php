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
        'listauthor' => [ 'author_id', 'page' ],
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
     *
     * Action specific for the frontpage
     */
    protected function hydrateList(array &$params = []) : void
    {
        $page = array_key_exists('page', $params) ? $params['page'] : 1;

        $epp = $this->get('orm.manager')->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        $filters = [
            'content_status' => [['value' => 1]],
            'in_litter'      => [['value' => 0]],
            'starttime' => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00', 'operator' => '=' ],
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime' => [
                'union'   => 'OR',
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => '0000-00-00 00:00:00', 'operator' => '=' ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ]
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
    protected function hydrateListAuthor(array &$params, $author)
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
                    'author_id'   => $author->id,
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

    /**
     * {@inheritdoc}
     */
    protected function hydrateShow(array &$params = []) : void
    {
        $params['tags'] = $this->getTags($params['content']);

        // Associated media code
        if (isset($params['content']->img2) && ($params['content']->img2 > 0)) {
            $params['photo'] = $this->get('opinion_repository')
                ->find('Photo', $params['content']->img2);
        }

        // TODO: Remove this ASAP
        $params['author'] = $this->get('user_repository')
            ->find((int) $params['content']->fk_author);

        $params['content']->author           = $params['author'];
        $params['content']->author_name_slug =
            \Onm\StringUtils::getTitle($params['content']->name);
    }
}
