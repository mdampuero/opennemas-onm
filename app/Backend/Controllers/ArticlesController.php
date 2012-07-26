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

use Symfony\Component\HttpFoundation\Reponse;
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
        $author =  $request->query->filter('author', 0, FILTER_VALIDATE_INT);
        $status =  (int) $request->query->filter('status', -1, FILTER_VALIDATE_INT);

        $itemsPerPage = s::get('items_per_page');

        $filterSQL = array('in_litter != 1');
        $filterStatus = $filterAuthor = '';
        if ($status >= 0) {
            $filterSQL []= ' content_status='.$status;
        }

        $filterSQL = implode(' AND ', $filterSQL);

        $cm      = new \ContentManager();
        list($countArticles, $articles)= $cm->getCountAndSlice(
            'Article',
            null,
            $filterSQL,
            'ORDER BY content_status ASC, changed, created DESC',
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
                'status' => $status,
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

        return $this->render('article/list.tpl', array(
            'articles' => $articles,
            'page'     => $page,
            'status'   => $status,
            'pagination' => $pagination,
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
            $article = new \Articl();

            $data = array(
                'title'                => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'category'             => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
            );

            if ($opinion->create($data)) {
                m::add(_('Special successfully created.'), m::SUCCESS);
            } else {
                m::add(_('Unable to create the new special.'), m::ERROR);
            }

            if ($continue) {
                return $this->redirect($this->generateUrl(
                    'admin_opinions',
                    array('type_opinion' => $data['category'])
                ));
            } else {
                return $this->redirect($this->generateUrl(
                    'admin_opinion_show',
                    array('id' => $opinion->id)
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
        if (!empty($article->img1)){
            $photo1 = new \Photo($article->img1);
            $this->view->assign('photo1', $photo1);
        }

        $img2 = $article->img2;
        if (!empty($img2)){
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
        foreach($relations as $aret) {
            $orderInner[] = new \Content($aret);
        }
        $this->view->assign('orderInner', $orderInner);

        if (ModuleManager::isActivated('AVANCED_ARTICLE_MANAGER') && is_array($article->params)) {
            $galleries = array();
            $galleries['home'] = (array_key_exists('withGalleryHome',$article->params))? new Album($article->params['withGalleryHome']): null;
            $galleries['front'] = (array_key_exists('withGalleryHome',$article->params))? new Album($article->params['withGallery']): null;
            $galleries['inner'] = (array_key_exists('withGalleryHome',$article->params))? new Album($article->params['withGalleryInt']): null;
            $this->view->assign('galleries', $galleries);

            $orderHome = array();
            $relations = $relationsHandler->getHomeRelations( $_REQUEST['id'] );//de portada
            if (!empty($relations)) {
                foreach($relations as $aret) {
                    $orderHome[] = new Content($aret);
                }
                $this->view->assign('orderHome', $orderHome);
            }

        }

        $comment = new \Comment();
        $comments = $cm->find('Comment', ' fk_content="'.$id.'"', NULL);

        return $this->render('article/new.tpl', array(
            'article'      => $article,
            'availableSizes' => array(
                16 => '16', 18 => '18', 20 => '20', 22 => '22',
                24 => '24', 26 => '26', 28 => '28',30 => '30',
                32 => '32', 34 => '34'
            ),
            'comments' => $comments
        ));

    }

} // END class ArticlesController