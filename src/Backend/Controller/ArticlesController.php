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
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for managing articles
 */
class ArticlesController extends Controller
{
    /**
     * Lists articles in the system
     *
     * @return Response the response object
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     and hasPermission('ARTICLE_ADMIN')
     *     and hasPermission('ARTICLE_PENDINGS')")
     */
    public function listAction()
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
        return $this->render('article/new.tpl', [
            'commentsConfig' => $this->get('setting_repository')
                ->get('comments_config'),
            'locale' => $request->query->get('locale'),
            'timezone' => $this->container->get('core.locale')
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
        return $this->render('article/new.tpl', [
            'commentsConfig' => $this->get('setting_repository')
                ->get('comments_config'),
            'id' => $id,
            'locale' => $request->query->get('locale'),
            'timezone' => $this->container->get('core.locale')
                ->getTimeZone()->getName()
        ]);
    }

    /**
     * Shows the content provider with articles in newsletter.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('ARTICLE_MANAGER')")
     */
    public function contentProviderInFrontpageAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = $this->get('settings_repository')->get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = [
            'content_type_name' => [[ 'value' => 'article' ]],
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]]
        ];

        if ($categoryId != 0) {
            $filters['category_name'] = [ [ 'value' => $category->name ] ];
        }

        $countArticles = true;
        $articles      = $em->findBy($filters, [ 'created' => 'desc' ], $itemsPerPage, $page, 0, $countArticles);

        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $itemsPerPage,
            'maxLinks'    => 5,
            'page'        => $page,
            'total'       => $countArticles,
            'route'       => [
                'name'   => 'admin_articles_content_provider_in_frontpage',
                'params' => ['category' => $categoryId]
            ]
        ]);

        return $this->render('common/content_provider/_container-content-list.tpl', [
            'contentType'           => 'Article',
            'contents'              => $articles,
            'contentTypeCategories' => $this->parentCategories,
            'category'              => $this->category,
            'pagination'            => $pagination->links,
            'contentProviderUrl'    => $this->generateUrl('admin_articles_content_provider_in_frontpage'),
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
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);

        $em  = $this->get('entity_repository');
        $ids = $this->get('frontpage_repository')->getContentIdsForHomepageOfCategory(0);

        $filters = [
            'content_type_name' => [ [ 'value' => 'article' ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'frontpage'         => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ],
            'pk_content'        => [ [ 'value' => $ids, 'operator' => 'NOT IN' ] ]
        ];

        $countArticles = true;
        $articles      = $em->findBy($filters, [ 'created' => 'desc' ], 8, $page, 0, $countArticles);

        $this->get('core.locale')->setContext('frontend');

        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => 8,
            'maxLinks'    => 5,
            'page'        => $page,
            'total'       => $countArticles,
            'route'       => [
                'name'   => 'admin_articles_content_provider_suggested',
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
        $categoryId     = $request->query->getDigits('category', 0);
        $page           = $request->query->getDigits('page', 1);
        $filterCategory = $request->query->getDigits('filter_by_category', 1);

        $em       = $this->get('entity_repository');
        $ids      = $this->get('frontpage_repository')->getContentIdsForHomepageOfCategory($categoryId);
        $category = $this->get('category_repository')->find($categoryId);

        $filters = [
            'content_type_name' => [ [ 'value' => 'article' ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ],
            'pk_content'        => [ [ 'value' => $ids, 'operator' => 'NOT IN' ] ],
        ];

        if ($categoryId != 0 && $filterCategory) {
            $filters['category_name'] = [ [ 'value' => $category->name ] ];
        }

        $countArticles = true;
        $articles      = $em->findBy($filters, [ 'created' => 'desc' ], 8, $page, 0, $countArticles);

        $this->get('core.locale')->setContext('frontend');

        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => 8,
            'maxLinks'    => 5,
            'page'        => $page,
            'total'       => $countArticles,
            'route'       => [
                'name'   => 'admin_articles_content_provider_category',
                'params' => [
                    'category'           => $categoryId,
                    'filter_by_category' => $filterCategory,
                ]
            ],
        ]);

        return $this->render('article/content-provider-category.tpl', [
            'articles'   => $articles,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Lists all the articles within a category for the related manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('ARTICLE_MANAGER')")
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = [
            'content_type_name' => [ [ 'value' => 'article' ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ]
        ];

        if ($categoryId != 0) {
            $filters['category_name'] = [ [ 'value' => $category->name ] ];
        }

        $countArticles = true;
        $articles      = $em->findBy($filters, [ 'created' => 'desc' ], $itemsPerPage, $page, 0, $countArticles);

        $this->get('core.locale')->setContext('frontend');

        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $itemsPerPage,
            'maxLinks'    => 5,
            'page'        => $page,
            'total'       => $countArticles,
            'route'       => [
                'name'   => 'admin_articles_content_provider_related',
                'params' => ['category' => $categoryId]
            ],
        ]);

        return $this->render('common/content_provider/_container-content-list.tpl', [
            'contentType'           => 'Article',
            'contents'              => $articles,
            'contentTypeCategories' => $this->parentCategories,
            'category'              => $this->category,
            'pagination'            => $pagination->links,
            'contentProviderUrl'    => $this->generateUrl('admin_articles_content_provider_related'),
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
        $locale = $request->get('locale');

        $this->get('core.locale')->setContext('frontend')
            ->setRequestLocale($locale);

        $this->loadCategories($request);

        $er = $this->get('entity_repository');

        $article         = new \Article();
        $articleContents = $request->request->filter('article');

        // Load config
        $this->view = $this->get('core.template');
        $this->view->setCaching(0);

        // Fetch all article properties and generate a new object
        foreach ($articleContents as $key => $value) {
            if (!empty($value)) {
                $article->{$key} = $value;
            }
        }

        // Disable comments on preview
        $article->with_comment = 0;

        // Set a dummy Id for the article if doesn't exists
        if (empty($article->pk_article) && empty($article->id)) {
            $article->pk_article = '-1';
            $article->id         = '-1';
        }

        // Fetch article category name
        $ccm = \ContentCategoryManager::get_instance();

        $category_name         = $ccm->getName($article->category);
        $actual_category_title = $ccm->getTitle($category_name);
        $actualCategoryId      = $ccm->get_id($category_name);

        list($positions, $advertisements) =
            \Frontend\Controller\ArticlesController::getAds($actualCategoryId);

        // Fetch media associated to the article
        $photoInt = '';
        if (isset($article->img2)
            && ($article->img2 != 0)
        ) {
            $photoInt = $er->find('Photo', $article->img2);
        }

        $videoInt = '';
        if (isset($article->fk_video2)
            && ($article->fk_video2 != 0)
        ) {
            $videoInt = $er->find('Video', $article->fk_video2);
        }

        $ids = [];
        if (!empty($article->relatedInner)) {
            $ids = array_map(function ($a) {
                return [ $a['type'],  $a['id'] ];
            }, $article->relatedInner);
        }

        $related = $this->get('entity_repository')->findMulti($ids);

        // Machine suggested contents code
        $machineSuggestedContents = $this->get('automatic_contents')
            ->searchSuggestedContents(
                'article',
                "category_name= '" . $article->category_name
                    . "' AND pk_content <>" . $article->id,
                4
            );

        $this->view->assign([
            'ads_positions'         => $positions,
            'advertisements'        => $advertisements,
            'relationed'            => $related,
            'suggested'             => $machineSuggestedContents,
            'contentId'             => $article->id,
            'category_name'         => $category_name,
            'actual_category'       => $category_name,
            'article'               => $article,
            'content'               => $article,
            'actual_category_title' => $actual_category_title,
            'photoInt'              => $photoInt,
            'videoInt'              => $videoInt,
        ]);

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
            'extra_fields' => $this->get('setting_repository')
                ->get('extraInfoContents.ARTICLE_MANAGER')
        ]);
    }

    /**
     * Common code for all the actions
     *
     * @return void
     */
    public function loadCategories(Request $request)
    {
        $this->category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm      = \ContentCategoryManager::get_instance();
        $this->category = ($this->category == 'all') ? 0 : $this->category;

        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $this->ccm->getArraysMenu($this->category);

        $this->view->assign([
            'category'     => $this->category,
            'subcat'       => $this->subcat,
            'allcategorys' => $this->parentCategories,
            'datos_cat'    => $this->categoryData,
        ]);
    }
}
