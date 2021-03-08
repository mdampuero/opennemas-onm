<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the actions for managing articles
 */
class ArticleController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'article';

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'preview' => 'article_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'article_inner' => [ 7 ]
    ];

    /**
     * The resource name.
     */
    protected $resource = 'article';

    /**
     * Lists articles in the system
     *
     * @return Response the response object
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     and hasPermission('ARTICLE_ADMIN')
     *     and hasPermission('ARTICLE_PENDINGS')")
     */
    public function listAction(Request $request)
    {
        return $this->render('article/list.tpl');
    }

    /**
     * Shows the form to create a new article.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     and hasPermission('ARTICLE_CREATE')")
     */
    public function createAction(Request $request)
    {
        return $this->render('article/item.tpl', [
            'commentsConfig' => $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('comments_config'),
            'locale'         => $request->query->get('locale'),
            'timezone'       => $this->container->get('core.locale')
                ->getTimeZone()->getName()
        ]);
    }

    /**
     * Shows the form to edit an article.
     *
     * @param Request $request The request object.
     * @param integer $id      The article id.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     and hasPermission('ARTICLE_UPDATE')")
     */
    public function showAction(Request $request, $id)
    {
        return $this->render('article/item.tpl', [
            'commentsConfig' => $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('comments_config'),
            'id'             => $id,
            'locale'         => $request->query->get('locale'),
            'timezone'       => $this->container->get('core.locale')
                ->getTimeZone()->getName()
        ]);
    }

    /**
     * Lists all the articles with the suggested flag activated.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('ARTICLE_MANAGER')")
     */
    public function contentProviderSuggestedAction(Request $request)
    {
        $category           = $request->query->getDigits('category', 0);
        $page               = $request->query->getDigits('page', 1);
        $frontpageVersionId =
            $request->query->getDigits('frontpage_version_id', null);
        $frontpageVersionId = $frontpageVersionId === '' ?
            null :
            $frontpageVersionId;

        $em  = $this->get('entity_repository');
        $ids = $this->get('api.service.frontpage_version')
            ->getContentIds($category, $frontpageVersionId, 'Article');

        $filters = [
            'content_type_name' => [ [ 'value' => 'article' ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'frontpage'         => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ],
            'pk_content'        => [ [ 'value' => $ids, 'operator' => 'NOT IN' ] ]
        ];

        $articles = $em->findBy($filters, [ 'created' => 'desc' ], 8, $page);
        $total    = $em->countBy($filters);

        $this->get('core.locale')->setContext('frontend');

        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => 8,
            'maxLinks'    => 5,
            'page'        => $page,
            'total'       => $total,
            'route'       => [
                'name'   => 'backend_articles_content_provider_suggested',
                'params' => ['category' => $category]
            ],
        ]);

        return $this->render('article/content-provider-suggested.tpl', [
            'articles'   => $articles,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Lists all the articles within a category.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("hasExtension('ARTICLE_MANAGER')")
     */
    public function contentProviderCategoryAction(Request $request)
    {
        $categoryId         = $request->query->getDigits('category', 0);
        $page               = $request->query->getDigits('page', 1);
        $frontpageVersionId =
            $request->query->getDigits('frontpage_version_id', null);
        $frontpageVersionId = $frontpageVersionId === '' ?
            null :
            $frontpageVersionId;

        $em  = $this->get('entity_repository');
        $ids = $this->get('api.service.frontpage_version')
            ->getContentIds($categoryId, $frontpageVersionId, 'Article');

        $filters = [
            'content_type_name'      => [ [ 'value' => 'article' ] ],
            'content_status'         => [ [ 'value' => 1 ] ],
            'in_litter'              => [ [ 'value' => 1, 'operator' => '!=' ] ],
            'pk_content'             => [ [ 'value' => $ids, 'operator' => 'NOT IN' ] ],
        ];

        if (!empty($categoryId)) {
            $filters['category_id'] = [ [ 'value' => $categoryId ] ];
        }

        $articles = $em->findBy($filters, [ 'created' => 'desc' ], 8, $page);
        $total    = $em->countBy($filters);

        $this->get('core.locale')->setContext('frontend');

        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => 8,
            'maxLinks'    => 5,
            'page'        => $page,
            'total'       => $total,
            'route'       => [
                'name'   => 'backend_articles_content_provider_category',
                'params' => [
                    'category' => $categoryId
                ]
            ],
        ]);

        return $this->render('article/content-provider-category.tpl', [
            'articles'   => $articles,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Previews an article in frontend by sending the article info by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     and hasPermission('ARTICLE_ADMIN')")
     */
    public function previewAction(Request $request)
    {
        $this->get('core.locale')->setContext('frontend')
            ->setRequestLocale($request->get('locale'));

        $article  = new \Article();
        $category = null;

        $data = array_merge(
            $request->request->filter('article'),
            [
                'pk_article'     => 0,
                'id'             => 0,
                'with_comment'   => 0,
                'content_status' => 1,
                'starttime'      => null,
                'endtime'        => null
            ]
        );

        // Load config
        $this->view = $this->get('core.template');
        $this->view->setCaching(0);

        foreach ($data as $key => $value) {
            $article->{$key} = empty($value) ? null : $value;
        }

        $tags = [];

        if (!empty($article->tags)) {
            $article->tags = array_filter($article->tags, function ($a) {
                return is_numeric($a);
            });

            $tags = $this->get('api.service.tag')
                ->getListByIdsKeyMapped($article->tags)['items'];
        }

        $params = [
            'article'   => $article,
            'content'   => $article,
            'contentId' => $article->id,
            'item'      => $article,
            'tags'      => $tags
        ];

        // Fetch article category name
        if (!empty($article->category_id)) {
            $category = $this->getCategory($article->category_id);
        }

        list($positions, $advertisements) = $this->getAdvertisements($category);

        $params['category']       = $category;
        $params['ads_positions']  = $positions;
        $params['advertisements'] = $advertisements;

        if (!empty($article->relatedInner)) {
            $ids = array_map(function ($a) {
                return [ $a['type'],  $a['id'] ];
            }, $article->relatedInner);

            $params['relationed'] = $this->get('entity_repository')
                ->findMulti($ids);
        }

        if (!empty($article->category_id)) {
            $params['suggested'] = $this->get('core.helper.content')->getSuggested(
                $article->pk_content,
                'article',
                $params['category']->id
            );
        }

        $this->view->assign($params);

        $this->get('session')->set(
            'last_preview',
            $this->view->fetch('article/article.tpl')
        );

        return new Response('OK');
    }

    /**
     * Description of this action
     *
     * @return Response the response object
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     and hasPermission('ARTICLE_ADMIN')")
     */
    public function getPreviewAction()
    {
        $session = $this->get('session');

        $content = $session->get('last_preview');
        $session->remove('last_preview');

        return new Response($content);
    }

    /**
     * Config for article system
     *
     * @return Response the response object
     *
     * @Security("hasExtension('MASTER')")
     */
    public function configAction()
    {
        return $this->render('article/config.tpl', [
            'extra_fields' => $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('extraInfoContents.ARTICLE_MANAGER')
        ]);
    }

    /**
     * Returns the category basing on the name included in the request URI.
     *
     * @param integer $id The category id.
     *
     * @return Category The category.
     */
    protected function getCategory($id)
    {
        try {
            $category = $this->get('orm.manager')->getRepository('Category')
                ->findOneBy(sprintf('id = %s', $id));

            $category->title = $this->get('data.manager.filter')
                ->set($category->title)
                ->filter('localize')
                ->get();

            return $category;
        } catch (EntityNotFoundException $e) {
            throw new ResourceNotFoundException();
        }
    }
}
