<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

require_once('../admin/session_bootstrap.php');

/**
 * Check admin session
 */
if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    Application::forward('/'); // Send to home page
}

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);
$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();


/**
 * Getting action for the controller
 **/
$action = $request->query->filter('action', null, FILTER_SANITIZE_STRING);

switch ($action) {
    case 'article': {

        $articleContents = $request->request->filter('contents');
        $article = new Article();

        // Fetch all article properties and generate a new object
        foreach ($articleContents as $key => $value) {
            if ( isset($value['name']) && !empty($value['name'])) {
                $article->$value['name'] = $value['value'];
            }
        }

        // Set a dummy Id for the article if doesn't exists
        if (empty($article->pk_article) && empty($article->id)) {
            $article->pk_article = '-1';
            $article->id = '-1';
        }

        // Load config
        $tpl->setConfig('articles');

        // Fetch article category name
        $category_name = $ccm->get_name($article->category);
        $actual_category_title = $ccm->get_title($category_name);

        // Advertisements for single article NO CACHE
        require_once 'article_advertisement.php';

        $tpl->assign('actual_category_title', $actual_category_title);
        $tpl->assign('contentId', $article->id); // Used on module_comments.tpl
        $tpl->assign('article', $article);
        $tpl->assign('category_name', $category_name);

        // Fetch media associated to the article
        if (isset($article->img2)
            && ($article->img2 != 0)
        ) {
            $photoInt = new Photo($article->img2);
            $tpl->assign('photoInt', $photoInt);
        }

        if (isset($article->fk_video2)
            && ($article->fk_video2 != 09)
        ) {
            $videoInt = new Video($article->fk_video2);
            $tpl->assign('videoInt', $videoInt);
        } else {
            $video = $cm->find_by_category_name(
                'Video',
                $category_name,
                'contents.content_status=1',
                'ORDER BY created DESC LIMIT 0 , 1'
            );
            if (isset($video[0])) {
                $tpl->assign('videoInt', $video[0]);
            }
        }


        // Fetch related contents to the inner article
        $relationes = array();
        $innerRelations = json_decode(json_decode($article->relatedInner, true));
        foreach ($innerRelations as $key => $value) {
            $relationes[$key] = $value->id;
        }

        $relat = $cm->cache->getContents($relationes);
        $relat = $cm->getInTime($relat);
        $relat = $cm->cache->getAvailable($relat);

        foreach ($relat as $ril) {
            $ril->category_name =
                $ccm->get_category_name_by_content_id($ril->id);
        }

        $tpl->assign('relationed', $relat);

        // Get suggested contents to the article
        $objSearch = cSearch::getInstance();
        $arrayResults=$objSearch->searchSuggestedContents(
            $article->metadata,
            'Article',
            'pk_fk_content_category= '.$article->category.
            ' AND contents.available=1 AND pk_content = pk_fk_content',
            4
        );

        $tpl->assign('suggested', $arrayResults);
        $tpl->caching = 0;
        $tpl->display('article/article.tpl');

    } break;

    default: {
        Application::forward301('index.php');
    } break;
}
