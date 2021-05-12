<?php

namespace Frontend\Controller;

use Api\Exception\GetItemException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

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
     * Renders the blog opinion frontpage.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function hydrateList(array &$params = []) : void
    {
        $date = date('Y-m-d H:i:s');

        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        try {
            $response = $this->get($this->service)->getList(sprintf(
                'content_type_name="opinion" and content_status=1 and in_litter=0 '
                . 'and blog=1 and (starttime is null or starttime < "%s") '
                . 'and (endtime is null or endtime > "%s") '
                . 'order by starttime desc limit %d offset %d',
                $date,
                $date,
                $params['epp'],
                $params['epp'] * ($params['page'] - 1)
            ));
        } catch (GetListException $e) {
            throw new ResourceNotFoundException();
        }

        // No first page and no contents
        if ($params['page'] > 1 && empty($response['items'])) {
            throw new ResourceNotFoundException();
        }

        $pagination = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $params['epp'],
            'total'       => $response['total'],
            'page'        => $params['page'],
            'route'       => 'frontend_blog_frontpage',
        ]);

        $this->view->assign([
            'opinions'   => $response['items'],
            'pagination' => $pagination,
            'page'       => $params['page'],
            'total'      => $response['total'],
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

        try {
            $response = $this->get($this->service)->getList(sprintf(
                'content_type_name="opinion" and content_status=1 and in_litter=0 '
                . 'and fk_author = %d '
                . 'and (starttime is null or starttime < "%s") '
                . 'and (endtime is null or endtime > "%s") '
                . 'order by starttime desc limit %d offset %d',
                $author->id,
                $date,
                $date,
                $params['epp'],
                $params['epp'] * ($params['page'] - 1)
            ));
        } catch (GetListException $e) {
            throw new ResourceNotFoundException();
        }

        // No first page and no contents
        if ($params['page'] > 1 && empty($response['items'])) {
            throw new ResourceNotFoundException();
        }

        $pagination = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $params['epp'],
            'page'        => $params['page'],
            'total'       => $response['total'],
            'route'       => [
                'name'   => 'frontend_blog_author_frontpage',
                'params' => [ 'author_slug' => $author->slug ]
            ],
        ]);

        $this->view->assign([
            'pagination' => $pagination,
            'blogs'      => $response['items'],
            'author'     => $author,
            'page'       => $params['page'],
            'total'      => $response['total'],
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
