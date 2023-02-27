<?php

namespace Frontend\Controller;

use Api\Exception\GetItemException;
use Api\Exception\GetListException;
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
        $expected = $this->get('core.decorator.url')->prefixUrl($expected);

        if ($request->getPathInfo() !== $expected) {
            return new RedirectResponse($expected);
        }

        $params = $this->getParameters($request);
        $this->view->setConfig($this->getCacheConfiguration($action));

        if (!$this->isCached($params)) {
            $this->hydrateListAuthor($params, $author);
        }

        $params['x-tags'] = sprintf('opinion-author-%d-frontpage', $author->id);

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

        try {
            $response = $this->get($this->service)->getList(sprintf(
                'content_type_name="opinion" and content_status=1 and in_litter=0 '
                . 'and blog=0 and (starttime is null or starttime < "%s") '
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
            'page'        => $params['page'],
            'total'       => $response['total'],
            'route'       => 'frontend_opinion_frontpage'
        ]);

        $params = array_merge($params, [
            'opinions'   => $response['items'],
            'pagination' => $pagination,
            'total'      => $response['total']
        ]);

        $params['x-tags'] .= ',opinion-frontpage';

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);

            $params['x-cache-for'] = $expire;
        }

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

        try {
            $response = $this->get($this->service)->getList(sprintf(
                'content_type_name="opinion" and content_status=1 and in_litter=0 '
                . 'and fk_author=%d '
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
                'name'   => 'frontend_opinion_author_frontpage',
                'params' => [
                    'author_id'   => $author->id,
                    'author_slug' => $author->slug,
                ]
            ]
        ]);

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);

            $params['x-cache-for'] = $expire;
        }

        $this->view->assign([
            'pagination' => $pagination,
            'opinions'   => $response['items'],
            'total'      => $response['total'],
            'author'     => $author,
            'page'       => $params['page'],
        ]);
    }
}
