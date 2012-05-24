<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

// Start up and setup the app
require_once '../bootstrap.php';

// Redirect Mobile browsers to mobile site unless a cookie exists.
$app->mobileRouter();

// Fetch HTTP variables
$category_name = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
$subcategory_name = $request->query->filter('subcategory_name', 'home', FILTER_SANITIZE_STRING);
$cache_page = $request->query->filter('page', 0, FILTER_VALIDATE_INT);
$action = $request->query->filter('action', 'index_rss', FILTER_SANITIZE_STRING);

// Setup view
$tpl = new Template(TEMPLATE_USER);
//$tpl->setConfig('rss');

switch ($action) {
        case 'index_rss': {

        $cacheID = $tpl->generateCacheId('Index', '', "RSS");

         /**
            * Fetch information for Advertisements
        */
        require_once "index_advertisement.php";

        if (($tpl->caching == 0)
            || !$tpl->isCached('rss/index.tpl', $cacheID) )
        {

            $ccm = ContentCategoryManager::get_instance();

            $categoriesTree = $ccm->getCategoriesTreeMenu();
            $opinionAuthors = Author::list_authors();

            $tpl->assign('categoriesTree', $categoriesTree);
            $tpl->assign('opinionAuthors', $opinionAuthors);
        }

        $tpl->display('rss/index.tpl', $cacheID);
        exit(0);
    }

    case 'rss': {

        // Initialicing variables
        $tpl->setConfig('rss');
        $title_rss = "";
        $rss_url = SITE_URL;
        $author = $request->query->filter('author', null , FILTER_SANITIZE_STRING);

        if ((strtolower($category_name)=="opinion")
            && isset($author))
        {
            $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, "RSS".$author);
        } else {
            $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, "RSS");
        }

        if (!$tpl->isCached('rss/rss.tpl', $cache_id)) {
            $ccm = ContentCategoryManager::get_instance();
            $cm = new ContentManager();
            // Setting up some variables to print out in the final rss
            if (isset($category_name)
                && !empty($category_name))
            {
                $category = $ccm->get_id($category_name);
                $rss_url .= $category_name.SS;
                $title_rss .= $category_name;

                if (isset($subcategory_name)
                    && !empty($subcategory_name))
                {
                    $subcategory = $ccm->get_id($subcategory_name);
                    $rss_url .= $subcategory_name.SS;
                    $title_rss .= " > ".$subcategory_name;
                }

            } else {
                $rss_url .= "home".SS;
                $title_rss .= "PORTADA";
            }

            $photos = array();

            // If is home retrive all the articles available in there
            if ($category_name == 'home') {

                $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);
                // Filter articles if some of them has time scheduling and sort them by position
                $contentsInHomepage = $cm->getInTime($contentsInHomepage);
                $articles_home = $cm->sortArrayofObjectsByProperty($contentsInHomepage, 'position');


                // Fetch the photo and category name for this element
                foreach ($articles_home as $i => $article) {

                    if (isset($article->img1) && $article->img1 != 0) {
                        $photos[$article->id] = new Photo($article->img1);
                    }

                    $article->category_name = $article->loadCategoryName($article->id);
                }

            // If is opinion
            } elseif ($category_name == 'opinion') {

                $author = $request->query->filter('author', null , FILTER_SANITIZE_STRING);

                // get all the authors of opinions
                if (!isset ($author)) {

                    $articles_home = $cm->find_listAuthors('contents.available=1 and contents.content_status=1', 'ORDER BY created DESC LIMIT 0,50');

                // get articles for the author in opinion
                } else {

                    $articles_home = $cm->find_listAuthors('opinions.fk_author='.((int)$author).' and  contents.available=1  and contents.content_status=1','ORDER BY created DESC  LIMIT 0,50');

                    if (count($articles_home)) {
                        $title_rss = 'Opiniones de «'.$articles_home[0]['name'].'»';
                    } else {
                        $title_rss = 'Este autor no tiene opiniones todavía.';
                    }
                }
                //Generate author-name-slug for generate_uri
                foreach ($articles_home as &$art) {
                    $art['author_name_slug'] = StringUtils::get_title($art['name']);
                }

            // Get the RSS for the rest of categories
            } else {

                // If frontpage contains a SUBCATEGORY the SQL request will be diferent

                $articles_home = $cm->find_by_category_name('Article',
                                                            $category_name,
                                                            'contents.content_status=1 AND '
                                                            .'contents.available=1 AND contents.fk_content_type=1',
                                                            'ORDER BY created DESC LIMIT 0,50');

                foreach ($articles_home as $i => $article) {
                    if (isset($article->img1) && $article->img1 != 0) {
                        $photos[$article->id] = new Photo($article->img1);
                    }

                    $article->category_name = $article->loadCategoryName($article->id);
                }

            }

            // Filter by scheduled {{{
            $articles_home = $cm->getInTime($articles_home);
            // }}}

            $tpl->assign('title_rss', strtoupper($title_rss));
            $tpl->assign('rss', $articles_home);

            $tpl->assign('photos', $photos);
            $tpl->assign('RSS_URL', $rss_url);
        } // end if(!$tpl->is_cached('rss.tpl', $cache_id)) (2)

        header('Content-type: application/rss+xml; charset=utf-8');
        $tpl->display('rss/rss.tpl', $cache_id);

        exit(0); // finish execution for don't disturb cache
    } break;
}
