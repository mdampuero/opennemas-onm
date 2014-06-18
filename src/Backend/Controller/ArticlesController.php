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

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone  = new \DateTimeZone($timezones[s::get('time_zone', 'UTC')]);

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

        // Fetch all authors
        $allAuthors = \User::getAllUsersAuthors();

        $_SESSION['_from'] = $this->generateUrl('admin_articles');

        return $this->render(
            'article/list.tpl',
            array(
                'authors' => $allAuthors,
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
                dispatchEventWithParams('frontpage.save_position', array('category' => 0));

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
     * Shows the content provider with articles in newsletter.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function contentProviderInFrontpageAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = array(
            'content_type_name' => array(array('value' => 'article')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!='))
        );

        if ($categoryId != 0) {
            $filters['category_name'] = array(array('value' => $category->name));
        }

        $articles      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countArticles = $em->countBy($filters);

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
                    'admin_articles_content_provider_in_frontpage',
                    array('category' => $categoryId)
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
                'contentProviderUrl'    => $this->generateUrl('admin_articles_content_provider_in_frontpage'),
            )
        );
    }

    /**
     * Lists all the articles with the suggested flag activated.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
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
     * Lists all the articles within a category.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
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
                    array('category' => $categoryId)
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
     * Lists all the articles within a category for the related manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
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
                    array('category' => $categoryId)
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
            'article',
            "category_name= '".$article->category_name."' AND pk_content <>".$article->id,
            4
        );

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
