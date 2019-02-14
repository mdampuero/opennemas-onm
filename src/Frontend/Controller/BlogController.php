<?php
/**
 * Defines the frontend controller for the opinion-blog content type
 *
 * @package Frontend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for advertisements
 *
 * @package Frontend_Controllers
 */
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
     * The list of valid query parameters per action.
     *
     * @var array
     */
    protected $queries = [
        'list'       => [ 'page' ],
        'listauthor' => [ 'author_slug', 'page' ],
    ];

    /**
     * The list of routes per action.
     *
     * @var array
     */
    protected $routes = [
        'listauthor' => 'frontend_blog_author_frontpage',
        'list'       => 'frontend_blog_frontpage',
        'show'       => 'frontend_blog_show',
    ];

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'list'       => 'opinion/blog_frontpage.tpl',
        'listauthor' => 'opinion/blog_author_index.tpl',
        'showamp'    => 'amp/content.tpl',
        'show'       => 'opinion/blog_inner.tpl',
    ];

    /**
     * Returns a content basing on the parameters in the current request and
     * the current controller.
     *
     * @param Request $request The request object.
     *
     * @return Content The content.
     */
    protected function getItem(Request $request)
    {
        $content = parent::getItem($request);

        $content->author = $this->get('user_repository')
            ->find((int) $content->fk_author);

        return $content;
    }

    /**
     * Renders the opinion author's frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function listAuthorAction(Request $request)
    {
        $slug = $request->get('author_slug', null);

        $criteria = [ 'username' => [ [ 'value' => $slug] ] ];
        $author   = $this->get('user_repository')->findOneBy($criteria);

        if (is_null($author)) {
            throw new ResourceNotFoundException();
        }

        if (array_key_exists('is_blog', $author->meta)
            && $author->meta['is_blog'] == 0
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
     * Renders the blog opinion frontpage.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function hydrateList(array $params) : void
    {
        $page = array_key_exists('page', $params) ? $params['page'] : 1;

        $authors = $this->get('api.service.author')
            ->getList('is_blog = 1 order by name asc');

        $authors = $this->get('data.manager.filter')
            ->set($authors['items'])
            ->filter('mapify', [ 'key' => 'id' ])
            ->get();

        $epp = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_in_blog', 10);
        $epp = (is_null($epp) || $epp <= 0) ? 10 : $epp;

        $order   = [ 'starttime' => 'DESC' ];
        $date    = date('Y-m-d H:i:s');
        $filters = [
            'content_type_name' => [[ 'value' => 'opinion' ]],
            'type_opinion'      => [[ 'value' => 0 ]],
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
                [ 'value' => '0000-00-00 00:00:00', 'operator' => '=' ],
                [ 'value' => $date, 'operator' => '>' ]
            ],
        ];

        $em         = $this->get('opinion_repository');
        $blogs      = $em->findBy($filters, $order, $epp, $page);
        $countItems = $em->countBy($filters);
        $photos     = $this->get('core.helper.user')->getPhotos($authors);

        $pagination = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $epp,
            'total'       => $countItems,
            'route'       => 'frontend_blog_frontpage',
        ]);

        foreach ($blogs as &$blog) {
            if (array_key_exists($blog->fk_author, $authors)) {
                $blog->author           = $authors[$blog->fk_author];
                $blog->name             = $blog->author->name;
                $blog->author_name_slug = $blog->author->username;

                if (array_key_exists($blog->author->avatar_img_id, $photos)) {
                    $blog->author->photo =
                        $photos[$blog->author->avatar_img_id];
                }

                if (isset($blog->img1) && !empty($blog->img1)) {
                    $blog->img1 = $this->get('entity_repository')
                        ->find('Photo', $blog->img1);
                }

                $blog->author->uri = \Uri::generate(
                    'frontend_blog_author_frontpage',
                    [
                        'slug' => urlencode($blog->author->username),
                        'id'   => $blog->author->id
                    ]
                );
            }
        }

        $this->view->assign([
            'opinions'   => $blogs,
            'authors'    => $authors,
            'pagination' => $pagination,
            'page'       => $page
        ]);
    }

    /**
     * Renders the opinion author's frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function hydrateListAuthor(array $params, $author) : void
    {
        $page = $params['page'] ?? 1;

        // Setting filters for the further SQLs
        $date    = date('Y-m-d H:i:s');
        $filters = [
            'contents`.`fk_author'         => [['value' => $author->id ]],
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

        $orderBy = ['created' => 'DESC'];

        $epp = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);
        $epp = (is_null($epp) || $epp <= 0) ? 10 : $epp;

        $author->slug  = $author->username;
        $author->photo = $this->get('entity_repository')->find('Photo', $author->avatar_img_id);
        $author->getMeta();

        $this->cm = new \ContentManager();
        // Get the number of total opinions for this author for pagination purposes
        $total    = $this->get('opinion_repository')->countBy($filters);
        $contents = $this->get('opinion_repository')->findBy($filters, $orderBy, $epp, $page);


        foreach ($contents as &$blog) {
            if (isset($blog->img1) && ($blog->img1 > 0)) {
                $blog->img1 = $this->get('entity_repository')->find('Photo', $blog->img1);
            }
            $blog->author           = $author;
            $blog->author_name_slug = $author->slug;

            // Generate author uri
            $blog->author_uri = $this->get('core.helper.url_generator')->generate($author);
        }

        $pagination = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $epp,
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
            'page'       => $page,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateShow($params = [], $item = null)
    {
        $author = $item->author;

        // Associated media code
        if (isset($item->img2) && ($item->img2 > 0)) {
            $photo = $this->get('opinion_repository')->find('Photo', $item->img2);

            $params['photo'] = $photo;
        }

        // TODO: Remove this ASAP
        $item->author_name_slug = \Onm\StringUtils::getTitle($item->name);

        $params = array_merge($params, [
            'author' => $author,
            'blog'   => $item,
        ]);

        $this->view->assign($params);

        return $params;
    }
}
