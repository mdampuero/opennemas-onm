<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;
use \Onm\Module\ModuleManager;

/**
 * Handles the actions for managing articles
 *
 * @package Backend_Controllers
 **/
class ArticlesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        ModuleManager::checkActivatedOrForward('COMMENT_MANAGER');

        // Check if the user can admin video
        $this->checkAclOrForward('COMMENT_ADMIN');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->category = $this->get('request')
                               ->query
                               ->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm        = \ContentCategoryManager::get_instance();
        $this->category = ($this->category == 'all') ? 0 : $this->category;
        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $this->ccm->getArraysMenu($this->category);

        $this->view->assign(array(
            'category'     => $this->category,
            'subcat'       => $this->subcat,
            'allcategorys' => $this->parentCategories,
            'datos_cat'    => $this->categoryData,
        ));
    }

    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $this->checkAclOrForward('ARTICLE_PENDINGS');

        // Check if the user has access to this category
        if ($this->category != 'all') {
            if (!\Acl::_C($this->category)) {
                m::add(_("You don't have enought privileges to see this category.") );

                return $this->redirect($this->generateUrl('admin_welcome'));
            }
        }

        $page   =  $request->query->getDigits('page', 1);
        $title =  $request->query->filter('title', null, FILTER_VALIDATE_INT);
        $status =  (int) $request->query->filter('status', -1, FILTER_VALIDATE_INT);
        $category =  $request->query->filter('category', 0, FILTER_VALIDATE_INT);

        if (is_null($category) || $category == 0) {
            $categoryFilter = null;
        } else {
            $categoryFilter = (int) $category;
        }

        $itemsPerPage = s::get('items_per_page');

        $filterSQL = array('in_litter != 1');
        if ($status >= 0) {
            $filterSQL []= ' available='.$status;
        }
        if (!empty($title)) {
            $filterSQL []= ' title LIKE \'%'.$title.'%\'';
        }

        $filterSQL = implode(' AND ', $filterSQL);

        $cm      = new \ContentManager();
        list($countArticles, $articles)= $cm->getCountAndSlice(
            'Article',
            $categoryFilter,
            $filterSQL,
            'ORDER BY available ASC, content_status ASC, changed, created DESC',
            $page,
            $itemsPerPage
        );

        $pagination = \Pager::factory(array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPerPage,
            'append'      => false,
            'path'        => '',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $countArticles,
            'fileName'    => $this->generateUrl('admin_articles', array(
                'status'   => $status,
                'title'    => $title,
                'category' => $category
            )).'&page=%d',
        ));

        if (isset($articles) && is_array($articles)) {
            $user    = new \User();
            $rating  = new \Rating();
            $comment = new \Comment();

            foreach ($articles as &$article) {
                $article->category_name = $article->loadCategoryName($article->id);
                $article->publisher = $user->get_user_name($article->fk_publisher);
                $article->editor    = $user->get_user_name($article->fk_user_last_editor);
                $article->rating    = $rating->getValue($article->id);
                $article->comment   = $comment->count_public_comments($article->id);
            }
        } else {
            $articles = array();
        }

        $_SESSION['_from'] = $this->generateUrl('admin_articles', array(
            'status'   => $status,
            'title'    => $title,
            'category' => $category,
            'page'     => $page
        ));

        return $this->render('article/list.tpl', array(
            'articles'   => $articles,
            'page'       => $page,
            'status'     => $status,
            'title'      => $title,
            'pagination' => $pagination,
            'totalArticles' => $countArticles
        ));
    }

    /**
     * Handles the form for creating a new article
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('ARTICLE_CREATE');

        if ('POST' == $request->getMethod()) {
            $article = new \Article();

            $params = $request->request->get('params');
            $contentStatus = $request->request->filter('content_status', '', FILTER_SANITIZE_STRING);
            $inhome        = $request->request->filter('promoted_to_category_frontpage', '', FILTER_SANITIZE_STRING);
            $frontpage     = $request->request->filter('frontpage', '', FILTER_SANITIZE_STRING);
            $withComment   = $request->request->filter('with_comment', '', FILTER_SANITIZE_STRING);

            $data = array(
                'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'title_int'      => $request->request->filter('title_int', '', FILTER_SANITIZE_STRING),
                'content_status'                 => (empty($contentStatus)) ? 0 : 1,
                'promoted_to_category_frontpage' => (empty($inhome)) ? 0 : 1,
                'with_comment'                   => (empty($withComment)) ? 0 : 1,
                'frontpage'                   => (empty($frontpage)) ? 0 : 1,
                'category'  => $request->request->getDigits('category'),
                'agency'    => $request->request->filter('agency', '', FILTER_SANITIZE_STRING),
                'params' =>  array(
                      'agencyBulletin'    => $params['agencyBulletin'],
                      'imageHomeFooter'   => $params['imageHomeFooter'],
                      'imageHome'         => $params['imageHome'],
                      'titleSize'         => $params['titleSize'],
                      'imagePosition'     => $params['imagePosition'],
                      'titleHome'         => $params['titleHome'],
                      'titleHomeSize'     => $params['titleHomeSize'],
                      'subtitleHome'      => $params['subtitleHome'],
                      'summaryHome'       => $params['summaryHome'],
                      'imageHomePosition' => $params['imageHomePosition'],
                      'withGallery'       => $params['withGallery'],
                      'withGalleryInt'    => $params['withGalleryInt'],
                      'withGalleryHome'   => $params['withGalleryHome'],
                ),
                'subtitle'          => $request->request->filter('subtitle', '', FILTER_SANITIZE_STRING),
                'metadata'          => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'summary'           => $request->request->filter('summary', '', FILTER_SANITIZE_STRING),
                'body'              => $request->request->filter('body', '', FILTER_SANITIZE_STRING),
                'img1'              => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'img1_footer'       => $request->request->filter('img1_footer', '', FILTER_SANITIZE_STRING),
                'img2'              => $request->request->filter('img2', '', FILTER_SANITIZE_STRING),
                'img2_footer'       => $request->request->filter('img2_footer', '', FILTER_SANITIZE_STRING),
                'fk_video'          => $request->request->filter('fk_video', '', FILTER_SANITIZE_STRING),
                'footer_video2'     => $request->request->filter('footer_video2', '', FILTER_SANITIZE_STRING),
                'fk_video2'         => $request->request->filter('fk_video2', '', FILTER_SANITIZE_STRING),
                'slug'              => $request->request->filter('slug', '', FILTER_SANITIZE_STRING),
                'starttime'         => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
                'endtime'           => $request->request->filter('endtime', '', FILTER_SANITIZE_STRING),
                'description'       => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
                'relatedFront'      => json_decode(json_decode($request->request->filter('relatedFront', '', FILTER_SANITIZE_STRING))),
                'relatedInner'      => json_decode(json_decode($request->request->filter('relatedInner', '', FILTER_SANITIZE_STRING))),
                'relatedHome'       => json_decode(json_decode($request->request->filter('relatedHome', '', FILTER_SANITIZE_STRING))),
            );

            if ($article->create($data)) {
                if ($data['promoted_to_category_frontpage'] == 1
                    && !$article->isInFrontpageOfCategory($_POST['category'])
                ) {
                    $article->promoteToCategoryFrontpage($_POST['category']);
                }

                m::add(_('Article successfully created.'), m::SUCCESS);
            } else {
                m::add(_('Unable to create the new article.'), m::ERROR);
            }

            $continue = $request->request->filter('continue', 0);
            if ($continue) {

                return $this->redirect($this->generateUrl(
                    'admin_article_show',
                    array('id' => $article->id)
                ));
            } else {

                return $this->redirect($this->generateUrl(
                    'admin_articles',
                    array('status' => $data['content_status'])
                ));
            }
        } else {
            $category = $request->request->getDigits('category', 0);

            $cm = new \ContentManager();

            return $this->render('article/new.tpl', array(
                'availableSizes'=> array(
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
                // TODO: clean this from here
                'MEDIA_IMG_PATH_WEB' => MEDIA_IMG_PATH_WEB,
            ));
        }
    }

    /**
     * Displays the article information form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->checkAclOrForward('OPINION_UPDATE');

        $id = $request->query->getDigits('id', null);

        $article = new \Article($id);

        if (is_null($article->id)) {
            m::add(sprintf(_('Unable to find the artucle with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_articles'));
        }

        if (is_string($article->params)) {
            $article->params = unserialize($article->params);
        }

        // Para usar el id de articulo al borrar un comentario
        $_SESSION['olderId'] = $id;
        $cm = new \ContentManager();

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

        if (is_array($article->params) &&
            (array_key_exists('imageHome', $article->params)) &&
            !empty($article->params['imageHome'])
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

        $relationsHandler= new \RelatedContent();

        $orderFront = array();
        $relations = $relationsHandler->getRelations($id);//de portada
        foreach ($relations as $aret) {
            $orderFront[] =  new \Content($aret);
        }

        $this->view->assign('orderFront', $orderFront);

        $orderInner = array();
        $relations = $relationsHandler->getRelationsForInner($id);//de interor
        foreach ($relations as $aret) {
            $orderInner[] = new \Content($aret);
        }
        $this->view->assign('orderInner', $orderInner);

        if (ModuleManager::isActivated('AVANCED_ARTICLE_MANAGER') && is_array($article->params)) {
            $galleries = array();
            $galleries['home'] = (array_key_exists('withGalleryHome',$article->params))? new \Album($article->params['withGalleryHome']): null;
            $galleries['front'] = (array_key_exists('withGalleryHome',$article->params))? new \Album($article->params['withGallery']): null;
            $galleries['inner'] = (array_key_exists('withGalleryHome',$article->params))? new \Album($article->params['withGalleryInt']): null;
            $this->view->assign('galleries', $galleries);

            $orderHome = array();
            $relations = $relationsHandler->getHomeRelations($id);//de portada
            if (!empty($relations)) {
                foreach ($relations as $aret) {
                    $orderHome[] = new \Content($aret);
                }
                $this->view->assign('orderHome', $orderHome);
            }

        }

        return $this->render('article/new.tpl', array(
            'article'      => $article,
            'availableSizes' => array(
                16 => '16', 18 => '18', 20 => '20', 22 => '22',
                24 => '24', 26 => '26', 28 => '28',30 => '30',
                32 => '32', 34 => '34'
            ),
        ));

    }

    /**
     * Updates the article information sent by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('ARTICLE_UPDATE');

        $id = $request->query->getDigits('id');

        $article = new \Article($id);

        if ($article->id != null) {
            if (!\Acl::isAdmin()
                && !\Acl::check('CONTENT_OTHER_UPDATE')
                && $article->fk_user != $_SESSION['userid']
            ) {
                m::add(_("You can't modify this article because you don't have enought privileges.") );

                return $this->redirect($this->generateUrl('admin_articles'));
            }

            $article = new \Article();

            $params = $request->request->get('params');
            $contentStatus = $request->request->filter('content_status', '', FILTER_SANITIZE_STRING);
            $inhome        = $request->request->filter('promoted_to_category_frontpage', '', FILTER_SANITIZE_STRING);
            $frontpage     = $request->request->filter('frontpage', '', FILTER_SANITIZE_STRING);
            $withComment   = $request->request->filter('with_comment', '', FILTER_SANITIZE_STRING);

            $data = array(
                'id'             => $id,
                'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'title_int'      => $request->request->filter('title_int', '', FILTER_SANITIZE_STRING),
                'content_status'                 => (empty($contentStatus)) ? 0 : 1,
                'promoted_to_category_frontpage' => (empty($inhome)) ? 0 : 1,
                'with_comment'                   => (empty($withComment)) ? 0 : 1,
                'frontpage'                   => (empty($frontpage)) ? 0 : 1,
                'category'  => $request->request->getDigits('category'),
                'agency'    => $request->request->filter('agency', '', FILTER_SANITIZE_STRING),
                'params' =>  array(
                      'agencyBulletin'    => $params['agencyBulletin'],
                      'imageHomeFooter'   => $params['imageHomeFooter'],
                      'imageHome'         => $params['imageHome'],
                      'titleSize'         => $params['titleSize'],
                      'imagePosition'     => $params['imagePosition'],
                      'titleHome'         => $params['titleHome'],
                      'titleHomeSize'     => $params['titleHomeSize'],
                      'subtitleHome'      => $params['subtitleHome'],
                      'summaryHome'       => $params['summaryHome'],
                      'imageHomePosition' => $params['imageHomePosition'],
                      'withGallery'       => $params['withGallery'],
                      'withGalleryInt'    => $params['withGalleryInt'],
                      'withGalleryHome'   => $params['withGalleryHome'],
                ),
                'subtitle'          => $request->request->filter('subtitle', '', FILTER_SANITIZE_STRING),
                'metadata'          => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'summary'           => $request->request->filter('summary', '', FILTER_SANITIZE_STRING),
                'body'              => $request->request->filter('body', '', FILTER_SANITIZE_STRING),
                'img1'              => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'img1_footer'       => $request->request->filter('img1_footer', '', FILTER_SANITIZE_STRING),
                'img2'              => $request->request->filter('img2', '', FILTER_SANITIZE_STRING),
                'img2_footer'       => $request->request->filter('img2_footer', '', FILTER_SANITIZE_STRING),
                'fk_video'          => $request->request->filter('fk_video', '', FILTER_SANITIZE_STRING),
                'footer_video2'     => $request->request->filter('footer_video2', '', FILTER_SANITIZE_STRING),
                'fk_video2'         => $request->request->filter('fk_video2', '', FILTER_SANITIZE_STRING),
                'slug'              => $request->request->filter('slug', '', FILTER_SANITIZE_STRING),
                'starttime'         => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
                'endtime'           => $request->request->filter('endtime', '', FILTER_SANITIZE_STRING),
                'description'       => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
                'relatedFront'      => json_decode(json_decode($request->request->filter('relatedFront', '', FILTER_SANITIZE_STRING))),
                'relatedInner'      => json_decode(json_decode($request->request->filter('relatedInner', '', FILTER_SANITIZE_STRING))),
                'relatedHome'       => json_decode(json_decode($request->request->filter('relatedHome', '', FILTER_SANITIZE_STRING))),
            );

            if ($article->update($data)) {
                if ($data['content_status'] == 0) {
                    $article->dropFromAllHomePages();
                }

                // Promote content to category frontpate if user wants to and is not already promoted
                if ($data['promoted_to_category_frontpage'] == 1
                    && !$article->isInFrontpageOfCategory($_POST['category'])
                ) {
                    $article->promoteToCategoryFrontpage($_POST['category']);
                }

                m::add(_('Article successfully updated.'), m::SUCCESS);
            } else {
                m::add(_('Unable to update the article.'), m::ERROR);
            }

            $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
            if ($continue) {
                return $this->redirect($this->generateUrl(
                    'admin_article_show',
                    array('id' => $article->id)
                ));
            } else {

                return $this->redirect($this->generateUrl(
                    'admin_articles',
                    array('status' => $data['content_status'])
                ));
            }
        }

    }

    /**
     * Deletes an article given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('ARTICLE_DELETE');

        $id       = $request->query->getDigits('id');
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 0);
        $title    = $request->query->filter('title', null, FILTER_SANITIZE_STRING);
        $status   = $request->query->filter('status', -1);

        if (!empty($id)) {
            $article = new \Article($id);

            $article->delete($id ,$_SESSION['userid']);
            m::add(_("Article deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete an article.'), m::ERROR);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl(
                'admin_articles',
                array(
                    'category' => $category,
                    'page'     => $page,
                    'status'   => $status,
                    'title'   => $title,
                )
            ));
        }
    }

    /**
     * Change available status for one article given its id
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $this->checkAclOrForward('ARTICLE_AVAILABLE');

        $id       = $request->query->getDigits('id', 0);
        $status   = $request->query->getDigits('status', 0);
        $redirectStatus   = $request->query->filter('redirectstatus', -1, FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $article = new \Article($id);
        if (is_null($article->id)) {
            m::add(sprintf(_('Unable to find article with id "%d"'), $id), m::ERROR);
        } else {
            $article->toggleAvailable($article->id);
            if ($status == 0) {
                $article->set_favorite($status);
            }
            m::add(sprintf(_('Successfully changed availability for article with id "%d"'), $id), m::SUCCESS);
        }

        return $this->redirect($this->generateUrl(
            'admin_articles',
            array(
                'category' => $category,
                'page'     => $page,
                'status'   => $redirectStatus,
            )
        ));
    }

    /**
     * Lists all the articles with the suggested flag activated
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderSuggestedAction(Request $request)
    {
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);

        $cm = new  \ContentManager();

        // Get contents for this home
        $contentElementsInFrontpage  = $cm->getContentsIdsForHomepageOfCategory($category);

        // Fetching opinions
        $sqlExcludedOpinions = '';
        if (count($contentElementsInFrontpage) > 0) {
            $contentsExcluded = implode(', ', $contentElementsInFrontpage);
            $sqlExcludedOpinions = ' AND `pk_article` NOT IN ('.$contentsExcluded.')';
        }

        list($countArticles, $articles) = $cm->getCountAndSlice(
            'Article',
            null,
            ' contents.frontpage=1 AND contents.available=1 AND '.
            ' contents.content_status=1 AND in_litter != 1 '. $sqlExcludedOpinions,
            ' ORDER BY created DESC ',
            $page,
            8
        );

        $pagination = \Pager::factory(array(
            'mode'        => 'Sliding',
            'perPage'     => 8,
            'append'      => false,
            'path'        => '',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $countArticles,
            'fileName'    => $this->generateUrl('admin_articles_content_provider_suggested', array(
                'category' => $category,
            )).'&page=%d',
        ));

        return $this->render('article/content-provider-suggested.tpl', array(
            'articles' => $articles,
            'pager'   => $pagination,
        ));
    }

    /**
     * Lists all the articles withing a category
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderCategoryAction(Request $request)
    {
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);

        $cm = new  \ContentManager();

        // Get contents for this home
        $contentElementsInFrontpage  = $cm->getContentsIdsForHomepageOfCategory($category);

        // Fetching opinions
        $sqlExcludedOpinions = '';
        if (count($contentElementsInFrontpage) > 0) {
            $contentsExcluded = implode(', ', $contentElementsInFrontpage);
            $sqlExcludedOpinions = ' AND `pk_article` NOT IN ('.$contentsExcluded.')';
        }

        list($countArticles, $articles) = $cm->getCountAndSlice(
            'Article',
            $category,
            'contents.available=1 AND in_litter != 1 ' . $sqlExcludedOpinions,
            ' ORDER BY created DESC ',
            $page,
            8
        );

        $pagination = \Pager::factory(array(
            'mode'        => 'Sliding',
            'perPage'     => 8,
            'append'      => false,
            'path'        => '',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $countArticles,
            'fileName'    => $this->generateUrl('admin_articles_content_provider_category', array(
                'category' => $category,
            )).'&page=%d',
        ));

        return $this->render('article/content-provider-category.tpl', array(
            'articles' => $articles,
            'pager'   => $pagination,
        ));
    }

    /**
     * Lists all the articles withing a category for the related manager
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderRelatedAction(Request $request)
    {
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        if ($category == 0) {
            $categoryFilter = null;
        } else {
            $categoryFilter = $category;
        }
        $cm = new  \ContentManager();

        list($countArticles, $articles) = $cm->getCountAndSlice(
            'Article',
            $categoryFilter,
            'contents.available=1',
            ' ORDER BY created DESC ',
            $page,
            $itemsPerPage
        );

        $pagination = \Pager::factory(array(
            'mode'        => 'Sliding',
            'perPage'     => $itemsPerPage,
            'append'      => false,
            'path'        => '',
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $countArticles,
            'fileName'    => $this->generateUrl('admin_articles_content_provider_related', array(
                'category' => $category,
            )).'&page=%d',
        ));

        return $this->render('common/content_provider/_container-content-list.tpl', array(
            'contentType'           => 'Article',
            'contents'              => $articles,
            'contentTypeCategories' => $this->parentCategories,
            'category'              => $this->category,
            'pagination'            => $pagination->links,
            'contentProviderUrl'    => $this->generateUrl('admin_articles_content_provider_related'),
        ));
    }

    /**
     * Previews an article in frontend by sending the article info by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function previewAction(Request $request)
    {
        $content = 'default content';

        return new Response($content);
    }

} // END class ArticlesController
