<?php
/**
 * Handles the actions for managing articles
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

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
        \Onm\Module\ModuleManager::checkActivatedOrForward('ARTICLE_MANAGER');

        $this->category = $this->get('request')->query
                               ->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm        = \ContentCategoryManager::get_instance();
        $this->category = ($this->category == 'all') ? 0 : $this->category;
        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $this->ccm->getArraysMenu($this->category);

        $this->view->assign(
            array(
                'category'     => $this->category,
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                'datos_cat'    => $this->categoryData,
            )
        );
    }

    /**
     * Lists articles in the system
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('ARTICLE_PENDINGS') and has_role('ARTICLE_ADMIN')")
     **/
    public function listAction(Request $request)
    {
        // Check if the user has access to this category
        if ($this->category != 'all' && $this->category != '0') {
            if (!Acl::checkCategoryAccess($this->category)) {
                m::add(_("You don't have enought privileges to see this category."));

                return $this->redirect($this->generateUrl('admin_welcome'));
            }
        }

        $page     =  $request->query->getDigits('page', 1);
        $title    =  $request->query->filter('title', null, FILTER_SANITIZE_STRING);
        $status   =  (int) $request->query->get('status', -1);
        $category =  $request->query->filter('category', 0, FILTER_VALIDATE_INT);

        if (is_null($category) || $category == 0) {
            $categoryFilter = null;
        } else {
            $categoryFilter = (int) $category;
        }

        $itemsPerPage = s::get('items_per_page');

        $filterSQL = array('in_litter <> 1');
        if ($status >= 0) {
            $filterSQL []= ' contents.available='.$status;
        }
        if (!empty($title)) {
            $filterSQL []=
                "(title LIKE '%{$title}%'"
                ." OR description LIKE '%{$title}%'"
                ." OR metadata LIKE '%{$title}%')";

        }

        $filterSQL = implode(' AND ', $filterSQL);

        $cm      = new \ContentManager();
        list($countArticles, $articles)= $cm->getCountAndSlice(
            'Article',
            $categoryFilter,
            $filterSQL,
            'ORDER BY created DESC, available ASC',
            $page,
            $itemsPerPage
        );

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countArticles,
                'fileName'    => $this->generateUrl(
                    'admin_articles',
                    array(
                        'status'   => $status,
                        'title'    => $title,
                        'category' => $category
                    )
                ).'&page=%d',
            )
        );

        if (isset($articles) && is_array($articles)) {

            $user    = new \User();
            $users   = $user->getUsers();
            $authors = array();
            foreach ($users as $user) {
                $authors[$user->id]  = $user;
            }

            foreach ($articles as &$article) {
                $article->category_name = $article->loadCategoryName($article->id);
                if (!empty($article->fk_publisher)) {
                    $article->publisher = $authors[$article->fk_publisher]->getUserName();
                }
                if (!empty($article->fk_user_last_editor)) {
                    $article->editor    = $authors[$article->fk_user_last_editor]->getUserName();
                }
                if (!empty($article->fk_author)) {
                    $article->author    = $authors[$article->fk_author]->getUserRealName($article->fk_author);
                }
            }

        } else {
            $articles = array();
        }

        $_SESSION['_from'] = $this->generateUrl(
            'admin_articles',
            array(
                'status'   => $status,
                'title'    => $title,
                'category' => $category,
                'page'     => $page
            )
        );

        return $this->render(
            'article/list.tpl',
            array(
                'articles'   => $articles,
                'page'       => $page,
                'status'     => $status,
                'title'      => $title,
                'pagination' => $pagination,
                'totalArticles' => $countArticles
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
     * @Security("has_role('ARTICLE_CREATE')")
     **/
    public function createAction(Request $request)
    {
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
                        'agencyBulletin'    =>
                            array_key_exists('agencyBulletin', $params) ? $params['agencyBulletin'] : '',
                        'imageHomeFooter'   =>
                            array_key_exists('imageHomeFooter', $params) ? $params['imageHomeFooter'] : '',
                        'imageHome'         =>
                            array_key_exists('imageHome', $params) ? $params['imageHome'] : '',
                        'titleSize'         =>
                            array_key_exists('titleSize', $params) ? $params['titleSize'] : '',
                        'imagePosition'     =>
                            array_key_exists('imagePosition', $params) ? $params['imagePosition'] : '',
                        'titleHome'         =>
                            array_key_exists('titleHome', $params) ? $params['titleHome'] : '',
                        'titleHomeSize'     =>
                            array_key_exists('titleHomeSize', $params) ? $params['titleHomeSize'] : '',
                        'subtitleHome'      =>
                            array_key_exists('subtitleHome', $params) ? $params['subtitleHome'] : '',
                        'summaryHome'       =>
                            array_key_exists('summaryHome', $params) ? $params['summaryHome'] : '',
                        'imageHomePosition' =>
                            array_key_exists('imageHomePosition', $params) ? $params['imageHomePosition'] : '',
                        'withGallery'       =>
                            array_key_exists('withGallery', $params) ? $params['withGallery'] : '',
                        'withGalleryInt'    =>
                            array_key_exists('withGalleryInt', $params) ? $params['withGalleryInt'] : '',
                        'withGalleryHome'   =>
                            array_key_exists('withGalleryHome', $params) ? $params['withGalleryHome'] : '',
                        'only_subscribers'          =>
                            array_key_exists('only_subscribers', $params) ? $params['only_subscribers'] : '',
                        'bodyLink'   =>
                            array_key_exists('bodyLink', $params) ? $params['bodyLink'] : '',
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
                'relatedFront'      => json_decode(
                    $request->request->filter('relatedFront', '', FILTER_SANITIZE_STRING)
                ),
                'relatedInner'      => json_decode(
                    $request->request->filter('relatedInner', '', FILTER_SANITIZE_STRING)
                ),
                'relatedHome'       => json_decode(
                    $request->request->filter('relatedHome', '', FILTER_SANITIZE_STRING)
                ),
                'fk_author'         => $request->request->filter('fk_author', 0, FILTER_VALIDATE_INT),
            );

            if ($article->create($data)) {
                if ($data['promoted_to_category_frontpage'] == 1
                    && !$article->isInFrontpageOfCategory($data['category'])
                ) {
                    $article->promoteToCategoryFrontpage($data['category']);
                    // Clear frontpage cache
                    dispatchEventWithParams('frontpage.save_position', array('category' => $data['category']));
                }

                m::add(_('Article successfully created.'), m::SUCCESS);
            } else {
                m::add(_('Unable to create the new article.'), m::ERROR);
            }

            $continue = $request->request->filter('continue', 0);
            if ($continue) {
                return $this->redirect(
                    $this->generateUrl(
                        'admin_article_show',
                        array('id' => $article->id)
                    )
                );
            } else {
                return $this->redirect(
                    $this->generateUrl(
                        'admin_articles',
                        array('status' => $data['content_status'])
                    )
                );
            }
        } else {
            $authorsComplete = \User::getAllUsersAuthors();
            $authors = array( '0' => _(' - Select one author - '));
            foreach ($authorsComplete as $author) {
                $authors[$author->id] = $author->name;
            }

            return $this->render(
                'article/new.tpl',
                array(
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
                    'authors' => $authors,
                    // TODO: clean this from here
                    'MEDIA_IMG_PATH_WEB' => MEDIA_IMG_PATH_WEB,
                )
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
     * @Security("has_role('ARTICLE_UPDATE')")
     **/
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $article = new \Article($id);

        if (is_null($article->id)) {
            m::add(sprintf(_('Unable to find the article with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_articles'));
        }

        if (is_string($article->params)) {
            $article->params = unserialize($article->params);
        }

        // Para usar el id de articulo al borrar un comentario
        $_SESSION['olderId'] = $id;

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

        if (\Onm\Module\ModuleManager::isActivated('CRONICAS_MODULES') && is_array($article->params)) {
            $galleries = array();
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

        $authorsComplete = \User::getAllUsersAuthors();
        $authors = array( '0' => _(' - Select one author - '));
        foreach ($authorsComplete as $author) {
            $authors[$author->id] = $author->name;
        }

        return $this->render(
            'article/new.tpl',
            array(
                'article'      => $article,
                'authors'      => $authors,
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
     * @Security("has_role('ARTICLE_UPDATE')")
     **/
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $article = new \Article($id);

        if ($article->id != null) {
            if (!Acl::isAdmin()
                && !Acl::check('CONTENT_OTHER_UPDATE')
                && !$article->isOwner($_SESSION['userid'])
            ) {
                m::add(_("You can't modify this article because you don't have enought privileges."));

                return $this->redirect($this->generateUrl('admin_articles'));
            }

            if (count($request->request) < 1) {
                m::add(_("Article data sent not valid."), m::ERROR);

                return $this->redirect($this->generateUrl('admin_article_show', array('id' => $id)));
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
                        'agencyBulletin'    =>
                            array_key_exists('agencyBulletin', $params) ? $params['agencyBulletin'] : '',
                        'imageHomeFooter'   =>
                            array_key_exists('imageHomeFooter', $params) ? $params['imageHomeFooter'] : '',
                        'imageHome'         =>
                            array_key_exists('imageHome', $params) ? $params['imageHome'] : '',
                        'titleSize'         =>
                            array_key_exists('titleSize', $params) ? $params['titleSize'] : '',
                        'imagePosition'     =>
                            array_key_exists('imagePosition', $params) ? $params['imagePosition'] : '',
                        'titleHome'         =>
                            array_key_exists('titleHome', $params) ? $params['titleHome'] : '',
                        'titleHomeSize'     =>
                            array_key_exists('titleHomeSize', $params) ? $params['titleHomeSize'] : '',
                        'subtitleHome'      =>
                            array_key_exists('subtitleHome', $params) ? $params['subtitleHome'] : '',
                        'summaryHome'       =>
                            array_key_exists('summaryHome', $params) ? $params['summaryHome'] : '',
                        'imageHomePosition' =>
                            array_key_exists('imageHomePosition', $params) ? $params['imageHomePosition'] : '',
                        'withGallery'       =>
                            array_key_exists('withGallery', $params) ? $params['withGallery'] : '',
                        'withGalleryInt'    =>
                            array_key_exists('withGalleryInt', $params) ? $params['withGalleryInt'] : '',
                        'withGalleryHome'   =>
                            array_key_exists('withGalleryHome', $params) ? $params['withGalleryHome'] : '',
                        'only_subscribers'          =>
                            array_key_exists('only_subscribers', $params) ? $params['only_subscribers'] : '',
                        'bodyLink'   =>
                            array_key_exists('bodyLink', $params) ? $params['bodyLink'] : '',
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
                'relatedFront'      => json_decode(
                    $request->request->filter('relatedFront', '', FILTER_SANITIZE_STRING)
                ),
                'relatedInner'      => json_decode(
                    $request->request->filter('relatedInner', '', FILTER_SANITIZE_STRING)
                ),
                'relatedHome'       => json_decode(
                    $request->request->filter('relatedHome', '', FILTER_SANITIZE_STRING)
                ),
                'fk_author'         => $request->request->filter('fk_author', 0, FILTER_VALIDATE_INT),
            );

            if ($article->update($data)) {
                if ($data['content_status'] == 0) {
                    $article->dropFromAllHomePages();
                }

                // Promote content to category frontpate if user wants to and is not already promoted
                if ($data['promoted_to_category_frontpage'] == 1
                    && !$article->isInFrontpageOfCategory($data['category'])
                ) {
                    $article->promoteToCategoryFrontpage($data['category']);
                }

                // Clear caches
                dispatchEventWithParams('content.update', array('content' => $article));
                dispatchEventWithParams('frontpage.save_position', array('category' => $data['category']));

                m::add(_('Article successfully updated.'), m::SUCCESS);
            } else {
                m::add(_('Unable to update the article.'), m::ERROR);
            }

            $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
            if ($continue) {
                return $this->redirect(
                    $this->generateUrl(
                        'admin_article_show',
                        array('id' => $article->id)
                    )
                );
            } else {
                if (!empty($_SESSION['_from'])) {
                    return $this->redirect($_SESSION['_from']);
                } else {
                    return $this->redirect(
                        $this->generateUrl(
                            'admin_articles',
                            array('status' => $data['content_status'])
                        )
                    );
                }
            }
        }

    }

    /**
     * Deletes an article given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('ARTICLE_DELETE')")
     **/
    public function deleteAction(Request $request)
    {
        $id       = $request->query->getDigits('id');
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 0);
        $title    = $request->query->filter('title', null, FILTER_SANITIZE_STRING);
        $status   = $request->query->filter('status', -1);

        if (!empty($id)) {
            $article = new \Article($id);

            $article->delete($id, $_SESSION['userid']);
            m::add(_("Article deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete an article.'), m::ERROR);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_articles',
                    array(
                        'category' => $category,
                        'page'     => $page,
                        'status'   => $status,
                        'title'   => $title,
                    )
                )
            );
        } else {
            return new Response('ok');
        }
    }

    /**
     * Change available status for one article given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('ARTICLE_AVAILABLE')")
     **/
    public function toggleAvailableAction(Request $request)
    {
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

        return $this->redirect(
            $this->generateUrl(
                'admin_articles',
                array(
                    'category' => $category,
                    'page'     => $page,
                    'status'   => $redirectStatus,
                )
            )
        );
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

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => 8,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countArticles,
                'fileName'    => $this->generateUrl(
                    'admin_articles_content_provider_suggested',
                    array('category' => $category,)
                ).'&page=%d',
            )
        );

        return $this->render(
            'article/content-provider-suggested.tpl',
            array(
                'articles' => $articles,
                'pager'   => $pagination,
            )
        );
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

        if ($category == 0) {
            $category = null;
        };

        list($countArticles, $articles) = $cm->getCountAndSlice(
            'Article',
            $category,
            'contents.content_status=1 AND in_litter != 1 ' . $sqlExcludedOpinions,
            ' ORDER BY created DESC ',
            $page,
            8
        );

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => 8,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countArticles,
                'fileName'    => $this->generateUrl(
                    'admin_articles_content_provider_category',
                    array('category' => (empty($category) ? '0' : $category),)
                ).'&page=%d',
            )
        );

        return $this->render(
            'article/content-provider-category.tpl',
            array(
                'articles' => $articles,
                'pager'   => $pagination,
            )
        );
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
            '',
            ' ORDER BY created DESC ',
            $page,
            $itemsPerPage
        );

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 1,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countArticles,
                'fileName'    => $this->generateUrl(
                    'admin_articles_content_provider_related',
                    array(
                        'category' => $category,)
                ).'&page=%d',
            )
        );

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
     * Shows the content provider with articles suggested for frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderInFrontpageAction(Request $request)
    {
        $category = $request->query->getDigits('category', 0);

        $cm = new  \ContentManager();

        // Get contents for this home
        $contentElementsInFrontpage  = $cm->getContentsForHomepageOfCategory($category);

        // Sort all the elements by its position
        $contentElementsInFrontpage  = $cm->sortArrayofObjectsByProperty($contentElementsInFrontpage, 'position');

        $articles = array();
        foreach ($contentElementsInFrontpage as $content) {
            if ($content->content_type =='1') {
                $articles[] = $content;
            }
        }

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            array(
                'contentType'           => 'Article',
                'contents'              => $articles,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $this->category,
                'contentProviderUrl'    => $this->generateUrl('admin_articles_content_provider_in_frontpage'),
            )
        );
    }

    /**
     * Set the published flag for contents in batch
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('ARTICLE_AVAILABLE')")
     **/
    public function batchPublishAction(Request $request)
    {
        $status         = $request->query->getDigits('new_status', 0);
        $redirectStatus = $request->query->filter('status', '-1', FILTER_SANITIZE_STRING);
        $selected       = $request->query->get('selected_fld', null);
        $category       = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page           = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            foreach ($selected as $id) {
                $article = new \Article($id);
                if ($article->category != 20) {
                    $article->set_available($status, $_SESSION['userid']);
                    if ($status == 0) {
                        $article->set_favorite($status, $_SESSION['userid']);
                    }
                } else {
                    m::add(sprintf(_('You must assign a section for "%s"'), $article->title), m::ERROR);
                }
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_articles',
                array(
                    'category' => $category,
                    'page'     => $page,
                    'status'   => $redirectStatus,
                )
            )
        );
    }

    /**
     * Set the published flag for contents in batch
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('ARTICLE_DELETE')")
     **/
    public function batchDeleteAction(Request $request)
    {
        $selected       = $request->query->get('selected_fld', null);
        $redirectStatus = $request->query->filter('status', '-1', FILTER_SANITIZE_STRING);
        $category       = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page           = $request->query->getDigits('page', 1);
        if (is_array($selected)
            && count($selected) > 0
        ) {
            foreach ($selected as $id) {
                $article = new \Article($id);
                $article->delete($id);
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_articles',
                array(
                    'category' => $category,
                    'page'     => $page,
                    'status'   => $redirectStatus,
                )
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
     * @Security("has_role('ARTICLE_ADMIN')")
     **/
    public function previewAction(Request $request)
    {
        $cm  = new \ContentManager();
        $ccm = \ContentCategoryManager::get_instance();
        $this->view = new \Template(TEMPLATE_USER);
        $article = new \Article();

        $articleContents = $request->request->filter('contents');

        // Fetch all article properties and generate a new object
        foreach ($articleContents as $key => $value) {
            if (isset($value['name']) && !empty($value['name'])) {
                $article->$value['name'] = $value['value'];
            }
        }

        // Set a dummy Id for the article if doesn't exists
        if (empty($article->pk_article) && empty($article->id)) {
            $article->pk_article = '-1';
            $article->id = '-1';
        }

        // Load config
        $this->view->setConfig('articles');

        // Fetch article category name
        $category_name = $ccm->get_name($article->category);
        $actual_category_title = $ccm->get_title($category_name);

        // Get advertisements for single article
        $actualCategoryId = $ccm->get_id($category_name);
        \Frontend\Controller\ArticlesController::getAds($actualCategoryId);

        // Fetch media associated to the article
        $photoInt = '';
        if (isset($article->img2)
            && ($article->img2 != 0)
        ) {
            $photoInt = new \Photo($article->img2);
        }

        $videoInt = '';
        if (isset($article->fk_video2)
            && ($article->fk_video2 != 09)
        ) {
            $videoInt = new \Video($article->fk_video2);
        }

        // Fetch related contents to the inner article
        $relationes = array();
        $innerRelations = json_decode($article->relatedInner, true);
        foreach ($innerRelations as $key => $value) {
            $relationes[$key] = $value['id'];
        }

        $relat = $cm->cache->getContents($relationes);
        $relat = $cm->getInTime($relat);
        $relat = $cm->cache->getAvailable($relat);

        foreach ($relat as $ril) {
            $ril->category_name =
                $ccm->get_category_name_by_content_id($ril->id);
        }

        // Machine suggested contents code -----------------------------
        $machineSuggestedContents = $this->get('automatic_contents')->searchSuggestedContents(
            $article->metadata,
            'article',
            "pk_fk_content_category= ".$article->category.
            " AND contents.available=1 AND pk_content = pk_fk_content",
            4
        );

        foreach ($machineSuggestedContents as &$element) {
            $element['uri'] = \Uri::generate(
                'article',
                array(
                    'id'       => $element['pk_content'],
                    'date'     => date('YmdHis', strtotime($element['created'])),
                    'category' => $element['catName'],
                    'slug'     => \Onm\StringUtils::get_title($element['title']),
                )
            );
        }

        $this->view->caching = 0;

        $session = $this->get('session');

        $session->set(
            'last_preview',
            $this->view->fetch(
                'article/article.tpl',
                array(
                    'relationed'            => $relat,
                    'suggested'             => $machineSuggestedContents,
                    'actual_category_title' => $actual_category_title,
                    'contentId'             => $article->id,
                    'article'               => $article,
                    'category_name'         => $category_name,
                    'photoInt'              => $photoInt,
                    'videoInt'              => $videoInt,
                )
            )
        );

        return new Response('OK');
    }

    /**
     * Description of this action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('ARTICLE_ADMIN')")
     **/
    public function getPreviewAction(Request $request)
    {
        $session = $this->get('session');

        $content = $session->get('last_preview');
        $session->remove('last_preview');

        return new Response($content);
    }
}
