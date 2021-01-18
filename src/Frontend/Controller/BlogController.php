<?php

namespace Frontend\Controller;

use Api\Exception\GetItemException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Common\Core\Controller\Controller;

class BlogController extends FrontendController
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
    protected $extension = 'BLOG_MANAGER';

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
        'listauthor' => [ 'author_slug', 'page' ],
    ];

    /**
     * {@inheritdoc}
     */
    protected $routes = [
        'listauthor' => 'frontend_blog_author_frontpage',
        'list'       => 'frontend_blog_frontpage',
        'show'       => 'frontend_blog_show',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.opinion';

    /**
     * {@inheritdoc}
     */
    protected $templates = [
        'list'       => 'opinion/blog_frontpage.tpl',
        'listauthor' => 'opinion/blog_author_index.tpl',
        'showamp'    => 'amp/content.tpl',
        'show'       => 'opinion/blog_inner.tpl',
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

        $slug = $request->get('author_slug', null);

        try {
            $author = $this->container->get('api.service.author')
                ->getItemBy("username = '$slug' or slug = '$slug'");
        } catch (GetItemException $e) {
            throw new ResourceNotFoundException();
        }

        if ($author->is_blog == 0) {
            return new RedirectResponse(
                $this->generateUrl('frontend_opinion_author_frontpage', [
                    'author_id'   => $author->id,
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
        $locale = $this->get('core.locale')->getRequestLocale();
        $params = parent::getParameters($params, $item);

        if (!empty($item)) {
            $params[$item->content_type_name] = $item;

            $params['tags'] = $this->get('api.service.tag')
                ->getListByIdsKeyMapped($item->tags, $locale)['items'];

            if (array_key_exists('bodyLink', $item->params)) {
                $params['o_external_link'] = $item->params['bodyLink'];
            }
        }

        return $params;
    }

    /**
     * Renders the blog opinion frontpage.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function hydrateList(array &$params = []) : void
    {
        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        $epp = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        $authors = $this->get('api.service.author')
            ->getList('is_blog = 1 order by name asc');

        $authors = $this->get('data.manager.filter')
            ->set($authors['items'])
            ->filter('mapify', [ 'key' => 'id' ])
            ->get();

        $order   = [ 'starttime' => 'DESC' ];
        $date    = date('Y-m-d H:i:s');
        $filters = [
            'content_type_name' => [[ 'value' => 'opinion' ]],
            'blog'              => [[ 'value' => 1 ]],
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS' ],
                [ 'value' => $date, 'operator' => '<' ]
            ],
            'endtime'           => [
                'union'   => 'OR',
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => $date, 'operator' => '>' ]
            ],
        ];

        $em    = $this->get('opinion_repository');
        $blogs = $em->findBy($filters, $order, $epp, $params['page']);
        $total = $em->countBy($filters);

        // No first page and no contents
        if ($params['page'] > 1 && empty($blogs)) {
            throw new ResourceNotFoundException();
        }

        $pagination = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $epp,
            'total'       => $total,
            'page'        => $params['page'],
            'route'       => 'frontend_blog_frontpage',
        ]);

        $this->view->assign([
            'opinions'   => $blogs,
            'authors'    => $authors,
            'pagination' => $pagination,
            'page'       => $params['page']
        ]);
    }

    /**
     * Renders the opinion author's frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function hydrateListAuthor(array &$params, $author) : void
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
            'contents`.`fk_author' => [['value' => $author->id ]],
            'content_status'    => [['value' => 1]],
            'in_litter'         => [['value' => 0]],
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

        $orderBy = ['created' => 'DESC'];

        $total    = $this->get('opinion_repository')->countBy($filters);
        $contents = $this->get('opinion_repository')
            ->findBy($filters, $orderBy, $epp, $params['page']);

        // No first page and no contents
        if ($params['page'] > 1 && empty($contents)) {
            throw new ResourceNotFoundException();
        }

        $pagination = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $epp,
            'page'        => $params['page'],
            'total'       => $total,
            'route'       => [
                'name'   => 'frontend_blog_author_frontpage',
                'params' => [ 'author_slug' => $author->slug ]
            ],
        ]);

        $this->view->assign([
            'pagination' => $pagination,
            'blogs'      => $contents,
            'author'     => $author,
            'page'       => $params['page'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateShow(array &$params = []) : void
    {
        $params['blog'] = $params['content'];
    }
}
