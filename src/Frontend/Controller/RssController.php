<?php
/**
 * Handles the actions for the public RSS
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the public RSS
 *
 * @package Frontend_Controllers
 **/
class RssController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
    }

    /**
     * Shows a page that shows a list of available RSS sources
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function indexAction()
    {
        $cacheID = $this->view->generateCacheId('Index', '', "RSS");

        // Fetch information for Advertisements
        \Frontend\Controller\ArticlesController::getAds();

        if (($this->view->caching == 0)
            || !$this->view->isCached('rss/index.tpl', $cacheID)
        ) {
            $ccm = \ContentCategoryManager::get_instance();

            $categoriesTree = $ccm->getCategoriesTreeMenu();
            $opinionAuthors = \User::getAllUsersAuthors();

            $this->view->assign('categoriesTree', $categoriesTree);
            $this->view->assign('opinionAuthors', $opinionAuthors);
        }

        return $this->render('rss/index.tpl', array('cache_id' => $cacheID));
    }

    /**
     * Displays the RSS feed for a given category, opinion or topic
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function generalRssAction(Request $request)
    {
        $categoryName    = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $subcategoryName = $request->query->filter('subcategory_name', null, FILTER_SANITIZE_STRING);
        $author          = $request->query->filter('author', null, FILTER_SANITIZE_STRING);

        $this->view->setConfig('rss');
        $title_rss = "";
        $rss_url   = SITE_URL;

        if (strtolower($categoryName) == "opinion" && isset($author)) {
            $cache_id = $this->view->generateCacheId($categoryName, $subcategoryName, "RSS".$author);
        } else {
            $cache_id = $this->view->generateCacheId($categoryName, $subcategoryName, "RSS");
        }

        if (!$this->view->isCached('rss/rss.tpl', $cache_id)) {
            $ccm = \ContentCategoryManager::get_instance();
            $cm = new \ContentManager();
            // Setting up some variables to print out in the final rss

            if (isset($categoryName)
                && !empty($categoryName)
            ) {
                $category = $ccm->get_id($categoryName);
                $rss_url .= $categoryName.SS;
                $category_title = $ccm->get_title($categoryName);
                $title_rss .= !empty($category_title)?$category_title:$categoryName;

                if (isset($subcategoryName)
                    && !empty($subcategoryName)
                ) {
                    $subcategory = $ccm->get_id($subcategoryName);
                    $rss_url .= $subcategoryName.SS;
                    $subcategory_title = $ccm->get_title($subcategoryName);
                    $title_rss .= " > ". !empty($subcategory_title)?$subcategory_title:$subcategoryName;
                }
            } else {
                $rss_url .= "home".SS;
                $title_rss .= "PORTADA";
            }

            $photos = array();

            // If is home retrive all the articles available in there
            if ($categoryName == 'home') {
                $contentsInHomepage = $cm->getContentsForHomepageOfCategory($category);

                // Filter articles if some of them has time scheduling and sort them by position
                $contentsInHomepage = $cm->getInTime($contentsInHomepage);
                $articles_home = $cm->sortArrayofObjectsByProperty($contentsInHomepage, 'position');
            } elseif ($categoryName == 'opinion') {
                $author = $request->query->filter('author', null, FILTER_SANITIZE_STRING);

                // get all the authors of opinions
                if (!isset($author) || empty($author)) {
                    $articles_home = $cm->getOpinionArticlesWithAuthorInfo(
                        'contents.available=1 and contents.content_status=1',
                        'ORDER BY created DESC LIMIT 50'
                    );
                    $title_rss = 'Últimas Opiniones';
                } else {
                    // get articles for the author in opinion
                    $articles_home = $cm->getOpinionArticlesWithAuthorInfo(
                        'opinions.fk_author='.((int) $author)
                        .' AND  contents.available=1  '
                        .'AND contents.content_status=1',
                        'ORDER BY created DESC  LIMIT 50'
                    );

                    if (count($articles_home)) {
                        $title_rss = 'Opiniones de «'.$articles_home[0]['name'].'»';
                    } else {
                        $title_rss = 'Este autor no tiene opiniones todavía.';
                    }
                }
                //Generate author-name-slug for generate_uri
                foreach ($articles_home as &$art) {
                    $art['author_name_slug'] = \Onm\StringUtils::get_title($art['name']);

                    $art['uri'] = \Uri::generate(
                        'opinion',
                        array(
                            'id'       => sprintf('%06d', $art['id']),
                            'date'     => date('YmdHis', strtotime($art['created'])),
                            'category' => $art['author_name_slug'],
                            'slug'     => $art['slug'],
                        )
                    );
                }
            } elseif ($categoryName == 'last') {
                $articles_home = $cm->find(
                    'Article',
                    'available=1 AND content_status=1 AND fk_content_type=1',
                    'ORDER BY created DESC, changed DESC LIMIT 50'
                );

                $title_rss = 'Últimas Noticias';
            } else {
                // Get the RSS for the rest of categories

                // If frontpage contains a SUBCATEGORY the SQL request will be diferent

                $articles_home = $cm->find_by_category_name(
                    'Article',
                    $categoryName,
                    'contents.content_status=1 AND '
                    .'contents.available=1 AND contents.fk_content_type=1',
                    'ORDER BY created DESC LIMIT 50'
                );
            }

            // Filter by scheduled
            $articles_home = $cm->getInTime($articles_home);

            // Fetch the photo and category name for this element
            foreach ($articles_home as $i => $article) {
                if (isset($article->img1) && $article->img1 != 0) {
                    $photos[$article->id] = new \Photo($article->img1);
                }

                // Exclude articles with external link from RSS
                if (isset($article->params['bodyLink']) && !empty($article->params['bodyLink'])) {
                    unset($articles_home[$i]);
                }
            }

            $this->view->assign(
                array(
                    'title_rss'     => $title_rss,
                    'rss'           => $articles_home,
                    'photos'        => $photos,
                    'RSS_URL'       => $rss_url,
                    'category_name' => $categoryName,
                )
            );
        } // IS CACHED

        $response = new Response(
            '',
            200,
            array(
                'Content-Type' => 'text/xml; charset=UTF-8',
                'x-tags'       => 'rss',
            )
        );

        return $this->render(
            'rss/rss.tpl',
            array('cache_id' => $cache_id),
            $response
        );
    }

    /**
     * Shows the author frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function authorRSSAction(Request $request)
    {

        $slug         = $request->query->filter('author_slug', '', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 50;

        $this->view->setConfig('rss');

        $cacheID = $this->view->generateCacheId('authorRSS-'.$slug, '', $page);

        if (($this->view->caching == 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
            // Get user by slug
            $ur = $this->get('user_repository');
            $user = $ur->findOneBy("username='{$slug}'", 'ID DESC');
            if (!empty($user)) {
                $title_rss   = 'RSS de «'.$user->name.'»';
                $user->photo = new \Photo($user->avatar_img_id);
                $user->getMeta();

                $searchCriteria =  "`fk_author`={$user->id}  AND fk_content_type IN (1, 4, 7) "
                    ."AND available=1 AND in_litter=0";

                $er = $this->get('entity_repository');
                $contentsCount  = $er->count($searchCriteria);
                $contents = $er->findBy($searchCriteria, 'starttime DESC', $itemsPerPage, $page);
                $photos = array();

                foreach ($contents as &$item) {
                    $item = $item->get($item->id);
                    $item->author = $user;
                    if (isset($item->img1) && ($item->img1 > 0)) {
                        $photos[$item->id] = new \Photo($item->img1);

                    }

                    if ($item->fk_content_type == 7) {
                        $photos[$item->id] = new \Photo($item->cover_id);
                    }

                    if (empty($item->summary)) {
                        $item->summary = substr(strip_tags($item->body), 0, 350);
                    }
                }

                $this->view->assign(
                    array(
                        'rss'       => $contents,
                        'author'    => $user,
                        'title_rss' => $title_rss,
                    )
                );
            }
        }

        $response = new Response(
            '',
            200,
            array(
                'Content-Type' => 'text/xml; charset=UTF-8',
                'x-tags'       => 'rss',
            )
        );

        return $this->render(
            'rss/rss.tpl',
            array('cache_id' => $cacheID),
            $response
        );

    }
}
