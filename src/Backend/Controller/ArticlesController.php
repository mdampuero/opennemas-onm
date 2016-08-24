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
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
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
    public function listAction(Request $request)
    {
        $this->loadCategories($request);

        // Check if the user has access to this category
        if ($this->category != 'all' && $this->category != '0') {
            if (!Acl::checkCategoryAccess($this->category)) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("You don't have enough privileges to see this category.")
                );

                return $this->redirect($this->generateUrl('admin_welcome'));
            }
        }

        // Build the list of authors to render filters
        $allAuthors = \User::getAllUsersAuthors();
        $authors = [ [ 'name' => _('All'), 'value' => -1 ], ];
        foreach ($allAuthors as $author) {
            $authors[] = [ 'name' => $author->name, 'value' => $author->id ];
        }

        // Build the list of categories to render filters
        $categories = [ [ 'name' => _('All'), 'value' => -1 ], ];
        foreach ($this->parentCategories as $key => $category) {
            $categories[] = [
                'name'  => $category->title,
                'value' => $category->name
            ];

            foreach ($this->subcat[$key] as $subcategory) {
                $categories[] = [
                    'name'  => '&rarr; ' . $subcategory->title,
                    'value' => $subcategory->name
                ];
            }
        }

        return $this->render(
            'article/list.tpl',
            array(
                'authors'    => $authors,
                'categories' => $categories,
            )
        );
    }

    /**
     * Handles the form for creating a new article
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     and hasPermission('ARTICLE_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            $this->loadCategories($request);

            $authorsComplete = \User::getAllUsersAuthors();
            $authors = array('0' => _(' - Select one author - '));
            foreach ($authorsComplete as $author) {
                $authors[$author->id] = $author->name;
            }

            return $this->render(
                'article/new.tpl',
                array(
                    'availableSizes' => array(
                        16 => '16',
                        18 => '18',
                        20 => '20',
                        22 => '22',
                        24 => '24',
                        26 => '26',
                        28 => '28',
                        30 => '30',
                        32 => '32',
                        34 => '34'
                    ),
                    'authors'        => $authors,
                    'commentsConfig' => s::get('comments_config')
                )
            );
        }

        $article = new \Article();

        $postReq = $request->request;

        $params        = $postReq->get('params');
        $contentStatus = $postReq->filter('content_status', '', FILTER_SANITIZE_STRING);
        $frontpage     = $postReq->filter('frontpage', '', FILTER_SANITIZE_STRING);
        $withComment   = $postReq->filter('with_comment', '', FILTER_SANITIZE_STRING);

        $data = array(
            'agency'         => $postReq->filter('agency', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'body'           => $postReq->filter('body', ''),
            'category'       => $postReq->getDigits('category'),
            'content_status' => (empty($contentStatus)) ? 0 : 1,
            'description'    => $postReq->filter('description', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'endtime'        => $postReq->filter('endtime', '', FILTER_SANITIZE_STRING),
            'fk_video'       => $postReq->getDigits('fk_video', ''),
            'fk_video2'      => $postReq->getDigits('fk_video2', ''),
            'footer_video2'  => $postReq->filter('footer_video2', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'frontpage'      => (empty($frontpage)) ? 0 : 1,
            'img1'           => $postReq->getDigits('img1', ''),
            'img1_footer'    => $postReq->filter('img1_footer', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'img2'           => $postReq->getDigits('img2', ''),
            'img2_footer'    => $postReq->filter('img2_footer', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'metadata'       => $postReq->filter('metadata', '', FILTER_SANITIZE_STRING),
            'slug'           => $postReq->filter('slug', '', FILTER_SANITIZE_STRING),
            'starttime'      => $postReq->filter('starttime', '', FILTER_SANITIZE_STRING),
            'subtitle'       => $postReq->filter('subtitle', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'summary'        => $postReq->filter('summary', ''),
            'title'          => $postReq->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'title_int'      => $postReq->filter('title_int', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'with_comment'   => (empty($withComment)) ? 0 : 1,
            'relatedFront'   => json_decode($postReq->get('relatedFront', '')),
            'relatedInner'   => json_decode($postReq->get('relatedInner', '')),
            'relatedHome'    => json_decode($postReq->get('relatedHome', '')),
            'fk_author'      => $postReq->getDigits('fk_author', 0),
            'params' =>  array(
                'agencyBulletin'    => array_key_exists('agencyBulletin', $params) ? $params['agencyBulletin'] : '',
                'bodyLink'          => array_key_exists('bodyLink', $params) ? $params['bodyLink'] : '',
                'imageHome'         => array_key_exists('imageHome', $params) ? $params['imageHome'] : '',
                'imageHomeFooter'   => array_key_exists('imageHomeFooter', $params) ? $params['imageHomeFooter'] : '',
                'imageHomePosition' => array_key_exists('imageHomePosition', $params) ? $params['imageHomePosition'] : '',
                'imagePosition'     => array_key_exists('imagePosition', $params) ? $params['imagePosition'] : '',
                'only_registered'   => array_key_exists('only_registered', $params) ? $params['only_registered'] : '',
                'only_subscribers'  => array_key_exists('only_subscribers', $params) ? $params['only_subscribers'] : '',
                'subtitleHome'      => array_key_exists('subtitleHome', $params) ? $params['subtitleHome'] : '',
                'summaryHome'       => array_key_exists('summaryHome', $params) ? $params['summaryHome'] : '',
                'titleHome'         => array_key_exists('titleHome', $params) ? $params['titleHome'] : '',
                'titleHomeSize'     => array_key_exists('titleHomeSize', $params) ? $params['titleHomeSize'] : '',
                'titleSize'         => array_key_exists('titleSize', $params) ? $params['titleSize'] : '',
                'withGallery'       => array_key_exists('withGallery', $params) ? $params['withGallery'] : '',
                'withGalleryHome'   => array_key_exists('withGalleryHome', $params) ? $params['withGalleryHome'] : '',
                'withGalleryInt'    => array_key_exists('withGalleryInt', $params) ? $params['withGalleryInt'] : '',
            ),
        );

        if ($article->create($data)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Article successfully created.')
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Unable to create the new article.')
            );
        }

        // Return user to list if has no update acl
        if (Acl::check('ARTICLE_UPDATE')) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_article_show',
                    array('id' => $article->id)
                )
            );
        } else {
            return $this->redirect(
                $this->generateUrl('admin_articles')
            );
        }
    }

    /**
     * Displays the article information given the article id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     and hasPermission('ARTICLE_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id', null);

        $article = new \Article($id);

        if (is_null($article->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the article with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_articles'));
        }

        $this->loadCategories($request);

        if (is_string($article->params)) {
            $article->params = unserialize($article->params);
        }

        // Photos de noticia
        if (!empty($article->img1)) {
            $photo1 = new \Photo($article->img1);
            $this->view->assign('photo1', $photo1);
        }

        $img2 = $article->img2;
        if (!empty($img2)) {
            $photo2 = new \Photo($img2);
            $this->view->assign('photo2', $photo2);
        }

        if (is_array($article->params)
            && (array_key_exists('imageHome', $article->params))
            && !empty($article->params['imageHome'])
        ) {
            $photoHome = new \Photo($article->params['imageHome']);
            $this->view->assign('photo3', $photoHome);
        }

        $video = $article->fk_video;
        if (!empty($video)) {
            $video1 = new \Video($video);
            $this->view->assign('video1', $video1);
        }

        $video = $article->fk_video2;
        if (!empty($video)) {
            $video2 = new \Video($video);
            $this->view->assign('video2', $video2);
        }

        if ($article->isInFrontpageOfCategory((int) $article->category)) {
            $article->promoted_to_category_frontpage = true;
        }

        // Get related contents service
        $relationsHandler = getService('related_contents');

        // Get frontpage related
        $orderFront = [];
        $relations = $relationsHandler->getRelations($id);
        foreach ($relations as $aret) {
            $orderFront[] =  new \Content($aret);
        }
        $this->view->assign('orderFront', \Onm\StringUtils::convertToUtf8($orderFront));

        // Get inner related
        $orderInner = [];
        $relations = $relationsHandler->getRelationsForInner($id);
        foreach ($relations as $aret) {
            $orderInner[] = new \Content($aret);
        }
        $this->view->assign('orderInner', \Onm\StringUtils::convertToUtf8($orderInner));

        if (\Onm\Module\ModuleManager::isActivated('CRONICAS_MODULES') && is_array($article->params)) {
            $galleries = [];
            if (array_key_exists('withGalleryHome', $article->params)) {
                $galleries['home'] = new \Album($article->params['withGalleryHome']);
            } else {
                $galleries['home'] = null;
            }

            if (array_key_exists('withGallery', $article->params)) {
                $galleries['front'] = new \Album($article->params['withGallery']);
            } else {
                $galleries['front'] = null;
            }

            if (array_key_exists('withGalleryInt', $article->params)) {
                $galleries['inner'] = new \Album($article->params['withGalleryInt']);
            } else {
                $galleries['inner'] = null;
            }

            \Onm\StringUtils::convertToUtf8($galleries);
            $this->view->assign('galleries', $galleries);

            $orderHome = [];
            $relations = $relationsHandler->getHomeRelations($id);//de portada
            if (!empty($relations)) {
                foreach ($relations as $aret) {
                    $orderHome[] = new \Content($aret);
                }
                $this->view->assign('orderHome', \Onm\StringUtils::convertToUtf8($orderHome));
            }
        }

        $authorsComplete = \User::getAllUsersAuthors();
        $authors = array('0' => _(' - Select one author - '));
        foreach ($authorsComplete as $author) {
            $authors[$author->id] = $author->name;
        }

        return $this->render(
            'article/new.tpl',
            array(
                'article'      => $article,
                'authors'      => $authors,
                'commentsConfig' => s::get('comments_config'),
                'availableSizes' => array(
                    16 => '16', 18 => '18', 20 => '20', 22 => '22',
                    24 => '24', 26 => '26', 28 => '28',30 => '30',
                    32 => '32', 34 => '34'
                ),
            )
        );
    }

    /**
     * Updates the article information sent by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     and hasPermission('ARTICLE_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $article = new \Article($id);

        if ($article->id == null) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Unable to update the article.")
            );

            return $this->redirect(
                $this->generateUrl('admin_articles')
            );
        }

        if (!Acl::isAdmin()
            && !Acl::check('CONTENT_OTHER_UPDATE')
            && !$article->isOwner($this->getUser()->id)
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You can't modify this article because you don't have enought privileges.")
            );

            return $this->redirect($this->generateUrl('admin_articles'));
        }

        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Article data sent not valid.")
            );

            return $this->redirect($this->generateUrl('admin_article_show', array('id' => $id)));
        }

        $article = new \Article();

        $postReq = $request->request;

        $params        = $postReq->get('params');
        $contentStatus = $postReq->filter('content_status', '', FILTER_SANITIZE_STRING);
        $frontpage     = $postReq->filter('frontpage', '', FILTER_SANITIZE_STRING);
        $withComment   = $postReq->filter('with_comment', '', FILTER_SANITIZE_STRING);

        $data = array(
            'agency'         => $postReq->filter('agency', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'body'           => $postReq->filter('body', ''),
            'category'       => $postReq->getDigits('category'),
            'content_status' => (empty($contentStatus)) ? 0 : 1,
            'description'    => $postReq->filter('description', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'endtime'        => $postReq->filter('endtime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'fk_author'      => $postReq->filter('fk_author', 0, FILTER_VALIDATE_INT),
            'fk_video'       => $postReq->getDigits('fk_video', ''),
            'fk_video2'      => $postReq->getDigits('fk_video2', ''),
            'footer_video2'  => $postReq->filter('footer_video2', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'frontpage'      => (empty($frontpage)) ? 0 : 1,
            'id'             => $id,
            'img1'           => $postReq->getDigits('img1', ''),
            'img1_footer'    => $postReq->filter('img1_footer', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'img2'           => $postReq->getDigits('img2', ''),
            'img2_footer'    => $postReq->filter('img2_footer', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'metadata'       => $postReq->filter('metadata', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'relatedFront'   => json_decode($postReq->get('relatedFront', '')),
            'relatedHome'    => json_decode($postReq->get('relatedHome', '')),
            'relatedInner'   => json_decode($postReq->get('relatedInner', '')),
            'slug'           => $postReq->filter('slug', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'starttime'      => $postReq->filter('starttime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'subtitle'       => $postReq->filter('subtitle', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'summary'        => $postReq->filter('summary', ''),
            'title'          => $postReq->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'title_int'      => $postReq->filter('title_int', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'with_comment'   => (empty($withComment)) ? 0 : 1,
            'params'         => array(
                'agencyBulletin'    => array_key_exists('agencyBulletin', $params) ? $params['agencyBulletin'] : '',
                'bodyLink'          => array_key_exists('bodyLink', $params) ? $params['bodyLink'] : '',
                'imageHome'         => array_key_exists('imageHome', $params) ? $params['imageHome'] : '',
                'imageHomeFooter'   => array_key_exists('imageHomeFooter', $params) ? $params['imageHomeFooter'] : '',
                'imageHomePosition' => array_key_exists('imageHomePosition', $params) ? $params['imageHomePosition'] : '',
                'imagePosition'     => array_key_exists('imagePosition', $params) ? $params['imagePosition'] : '',
                'only_registered'   => array_key_exists('only_registered', $params) ? $params['only_registered'] : '',
                'only_subscribers'  => array_key_exists('only_subscribers', $params) ? $params['only_subscribers'] : '',
                'subtitleHome'      => array_key_exists('subtitleHome', $params) ? $params['subtitleHome'] : '',
                'summaryHome'       => array_key_exists('summaryHome', $params) ? $params['summaryHome'] : '',
                'titleHome'         => array_key_exists('titleHome', $params) ? $params['titleHome'] : '',
                'titleHomeSize'     => array_key_exists('titleHomeSize', $params) ? $params['titleHomeSize'] : '',
                'titleSize'         => array_key_exists('titleSize', $params) ? $params['titleSize'] : '',
                'withGallery'       => array_key_exists('withGallery', $params) ? $params['withGallery'] : '',
                'withGalleryHome'   => array_key_exists('withGalleryHome', $params) ? $params['withGalleryHome'] : '',
                'withGalleryInt'    => array_key_exists('withGalleryInt', $params) ? $params['withGalleryInt'] : '',
            ),
        );

        if ($article->update($data)) {
            if ($data['content_status'] == 0) {
                $article->dropFromAllHomePages();
            }

            // Clear caches
            dispatchEventWithParams('frontpage.save_position', array('category' => $data['category']));
            dispatchEventWithParams('frontpage.save_position', array('category' => 0));

            $this->get('session')->getFlashBag()->add(
                'success',
                _("Article successfully updated.")
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Unable to update the article.")
            );
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_article_show',
                array('id' => $article->id)
            )
        );
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
        $itemsPerPage = $this->get('settings_reporitosy')->get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = array(
            'content_type_name' => [[ 'value' => 'article' ]],
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]]
        );

        if ($categoryId != 0) {
            $filters['category_name'] = array(array('value' => $category->name));
        }

        $articles      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countArticles = $em->countBy($filters);

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

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            array(
                'contentType'           => 'Article',
                'contents'              => $articles,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $this->category,
                'pagination'            => $pagination->links,
                'contentProviderUrl'    => $this->generateUrl('admin_articles_content_provider_in_frontpage'),
            )
        );
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

        $filters = array(
            'content_type_name' => array(array('value' => 'article')),
            'content_status'    => array(array('value' => 1)),
            'frontpage'         => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
            'pk_content'        => array(array('value' => $ids, 'operator' => 'NOT IN'))
        );

        $articles      = $em->findBy($filters, array('created' => 'desc'), 8, $page);
        $countArticles = $em->countBy($filters);

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

        return $this->render(
            'article/content-provider-suggested.tpl',
            array(
                'articles'   => $articles,
                'pagination' => $pagination,
            )
        );
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
        $categoryId = $request->query->getDigits('category', 0);
        $page       = $request->query->getDigits('page', 1);

        $em       = $this->get('entity_repository');
        $ids      = $this->get('frontpage_repository')->getContentIdsForHomepageOfCategory($categoryId);
        $category = $this->get('category_repository')->find($categoryId);

        $filters = array(
            'content_type_name' => array(array('value' => 'article')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
            'pk_content'        => array(array('value' => $ids, 'operator' => 'NOT IN')),
        );

        if ($categoryId != 0) {
            $filters['category_name'] = array(array('value' => $category->name));
        }

        $articles      = $em->findBy($filters, array('created' => 'desc'), 8, $page);
        $countArticles = $em->countBy($filters);

        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => 8,
            'maxLinks'    => 5,
            'page'        => $page,
            'total'       => $countArticles,
            'route'       => [
                'name'   => 'admin_articles_content_provider_category',
                'params' => ['category' => $categoryId]
            ],
        ]);

        return $this->render(
            'article/content-provider-category.tpl',
            array(
                'articles'   => $articles,
                'pagination' => $pagination,
            )
        );
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

        $filters = array(
            'content_type_name' => array(array('value' => 'article')),
            'in_litter'         => array(array('value' => 1, 'operator' => '!='))
        );

        if ($categoryId != 0) {
            $filters['category_name'] = array(array('value' => $category->name));
        }

        $articles      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countArticles = $em->countBy($filters);

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

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            array(
                'contentType'           => 'Article',
                'contents'              => $articles,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $this->category,
                'pagination'            => $pagination->links,
                'contentProviderUrl'    => $this->generateUrl('admin_articles_content_provider_related'),
            )
        );
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
        $this->loadCategories($request);

        $er  = $this->get('entity_repository');

        $article    = new \Article();
        $articleContents = $request->request->filter('contents');


        // Load config
        $this->view = $this->get('core.template');
        $this->view->setCaching(0);

        // Fetch all article properties and generate a new object
        foreach ($articleContents as $key => $value) {
            if (isset($value['name']) && !empty($value['name'])) {
                $article->{$value['name']} = $value['value'];
            }
        }

        // Set a dummy Id for the article if doesn't exists
        if (empty($article->pk_article) && empty($article->id)) {
            $article->pk_article = '-1';
            $article->id = '-1';
        }

        // Fetch article category name
        $ccm = \ContentCategoryManager::get_instance();
        $category_name         = $ccm->getName($article->category);
        $actual_category_title = $ccm->getTitle($category_name);

        // Get advertisements for single article
        $actualCategoryId = $ccm->get_id($category_name);
        $ads = \Frontend\Controller\ArticlesController::getAds($actualCategoryId);
        $this->view->assign('advertisements', $ads);

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

        // Fetch related contents to the inner article
        $relationes = array();
        $innerRelations = json_decode($article->relatedInner, true);
        foreach ($innerRelations as $key => $value) {
            $relationes[$key] = $value['id'];
        }

        $cm  = new \ContentManager();
        $relat = $cm->getContents($relationes);
        $relat = $cm->getInTime($relat);
        $relat = $cm->getAvailable($relat);

        foreach ($relat as $ril) {
            $ril->category_name = $ccm->getCategoryNameByContentId($ril->id);
        }

        // Machine suggested contents code
        $machineSuggestedContents = $this->get('automatic_contents')->searchSuggestedContents(
            'article',
            "category_name= '".$article->category_name."' AND pk_content <>".$article->id,
            4
        );

        $this->view->assign([
            'relationed'            => $relat,
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
     * Common code for all the actions
     *
     * @return void
     **/
    public function loadCategories(Request $request)
    {
        $this->category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm      = \ContentCategoryManager::get_instance();
        $this->category = ($this->category == 'all') ? 0 : $this->category;
        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $this->ccm->getArraysMenu($this->category);

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone  = new \DateTimeZone($timezones[s::get('time_zone', 421)]);

        $this->view->assign(
            array(
                'category'     => $this->category,
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                'datos_cat'    => $this->categoryData,
                'timezone'     => $timezone->getName()
            )
        );
    }
}
