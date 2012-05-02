<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

require_once('../admin/session_bootstrap.php');

/**
 * Check admin session
 */
if(!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    Application::forward('/'); // Send to home page
}

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);
$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

/**
 * Getting request params
 **/

$articleID = $request->query->filter('id', null, FILTER_SANITIZE_STRING);
$tpl->assign('contentId', $articleID); // Used on module_comments.tpl

/**
 * Fetch categories and subcategories
 */
$category_name = $ccm->get_category_name_by_content_id($articleID);
$subcategory_name = $request->query->filter('subcategory_name', null, FILTER_SANITIZE_STRING);
list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);

/**
 * Getting action for the controller
 **/
$action = $request->query->filter('action', null, FILTER_SANITIZE_STRING);

if( !empty($action) ) {

    switch($action) {
        case 'article': {
            // Load config
            $tpl->setConfig('articles');
            $article = new Article($articleID);

            // Fetch advertisement logic
            require_once('article_advertisement.php');

            // Check if is a subcategory and set actual_category
            if(isset($subcategory_name) && !empty($category_name)){
                $actual_category = $subcategory_name;
            }else{
                $actual_category =$category_name;
            }

            $actual_category_id = $ccm->get_id($actual_category);
            $actual_category_title = $ccm->get_title($actual_category);

            $tpl->assign( array (
                    'category_name' => $category_name,
                    'actual_category' => $actual_category,
                    'actual_category_id' => $actual_category_id,
                    'actual_category_title' => $actual_category_title,
                    'contentId' => $articleID,
                    'articleId' => $articleID,
                    'article' => $article,
            ));
            $tpl->assign('contentId', $article->id); // Used on module_comments.tpl

            /**
             *  Fetch media for the article
             */
            if (isset($article->img2)
                && ($article->img2 != 0))
            {
                $photoInt = new Photo($article->img2);
                $tpl->assign('photoInt', $photoInt);
            }

            if (isset($article->fk_video2)
                && ($article->fk_video2 != 09))
            {
                $videoInt = new Video($article->fk_video2);
                $tpl->assign('videoInt', $videoInt);
            }else{
                $video =
                    $cm->find_by_category_name('Video',
                                                $actual_category,
                                                'contents.content_status=1',
                                                'ORDER BY created DESC LIMIT 0 , 1');
                if(isset($video[0])){ $tpl->assign('videoInt', $video[0]); }
            }

            /**
             *  Fetch related contents
             */
            $rel= new RelatedContent();

            $relationes = $rel->cache->get_relations_int($articleID);
            $relat = $cm->cache->getContents($relationes);

            $relat = $cm->getInTime($relat);
            $relat = $cm->cache->getAvailable($relat);

            //Nombre categoria correcto.
            foreach($relat as $ril) {
                $ril->category_name = $ccm->get_category_name_by_content_id($ril->id);
            }
            $tpl->assign('relationed', $relat);

            /**
             * Fetch suggested contents
             */
            $objSearch = cSearch::Instance();
            $arrayResults=$objSearch->SearchSuggestedContents(
                $article->metadata,
                'Article',
                'pk_fk_content_category= '.$article->category.
                ' AND contents.available=1 AND pk_content = pk_fk_content',
                4
            );
            // $arrayResults= $cm->getInTime($arrayResults);
            $tpl->assign('suggested', $arrayResults);

        } break;

        case 'article_new': {

            $articleNewValues = filter_input_array(INPUT_POST);

            $vars = get_class_vars('Article');

            foreach ($vars as $key => $value) {
                if( isset($articleNewValues[$key]) && !empty($articleNewValues[$key])) {
                    $article->$key = $articleNewValues[$key];
                }
            }

            // Set a dummy Id for the article
            $article->pk_article = '-1';
            $article->id = '-1';

            // Load config
            $tpl->setConfig('articles');
            // Require category dependency
            require_once ("index_sections.php");

            $tpl->assign('category_name', $category_name);

            if(isset($subcategory_name) && !empty($category_name)){
                $actual_category = $subcategory_name;
            }else{
                $actual_category =$category_name;
            }
            $actual_category_id = $ccm->get_id($actual_category);
            $actual_category_title = $ccm->get_title($actual_category);
            $tpl->assign('actual_category_title',$actual_category_title);
            $tpl->assign('contentId', $article->id); // Used on module_comments.tpl
            $tpl->assign('article', $article);

            if (isset($article->img2)
                && ($article->img2 != 0))
            {
                $photoInt = new Photo($article->img2);
                $tpl->assign('photoInt', $photoInt);
            }

            if (isset($article->fk_video2)
                && ($article->fk_video2 != 09))
            {
                $videoInt = new Video($article->fk_video2);
                $tpl->assign('videoInt', $videoInt);
            }else{
                $video =
                    $cm->find_by_category_name('Video',
                                                $actual_category,
                                                'contents.content_status=1',
                                                'ORDER BY created DESC LIMIT 0 , 1');
                if(isset($video[0])){ $tpl->assign('videoInt', $video[0]); }
            }

            /**************** PHOTOs ****************/

            /******* RELATED  CONTENT *******/
            $rel= new RelatedContent();

            $relationes =
                $rel->cache->get_relations_int($_REQUEST['id']);
            $relat = $cm->cache->getContents($relationes);

            // Filter by scheduled {{{
            $relat = $cm->getInTime($relat);
            // }}}
            //Filter availables and not inlitter.
            $relat = $cm->cache->getAvailable($relat);


            //Nombre categoria correcto.
            foreach($relat as $ril) {
                $ril->category_name = $ccm->get_category_name_by_content_id($ril->id);
            }
            $tpl->assign('relationed', $relat);

            /******* SUGGESTED CONTENTS *******/
            $objSearch = cSearch::Instance();
            $arrayResults=$objSearch->SearchSuggestedContents(
                $article->metadata,
                'Article',
                'pk_fk_content_category= '.$article->category.
                ' AND contents.available=1 AND pk_content = pk_fk_content',
                4
            );
            // $arrayResults= $cm->getInTime($arrayResults);
            $tpl->assign('suggested', $arrayResults);

        } break;

        default: {
            Application::forward301('index.php');
        } break;
    }

} else {
    Application::forward301('index.php');
}
$tpl->caching = 0;
$tpl->display('article/article.tpl');

