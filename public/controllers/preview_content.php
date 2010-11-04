<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

require_once('../admin/session_bootstrap.php');

////////////////////////////////////////////////////////////////////////////////
// Check admin session
if(!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    Application::forward('/'); // Send to home page
}

$tpl = new Template(TEMPLATE_USER);

$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

$category_name = $ccm->get_category_name_by_content_id($_REQUEST['id']);

// Normalizar os nomes 
list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);
 
/**************************************  SECURITY  *******************************************/

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {        
        case 'article': {
            //Generamos el id de cache
            $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, $_GET['id']);

            // Load config
            $tpl->setConfig('articles');
            
            $article = new Article($_REQUEST['id']);
            
            $str = new String_Utils();
            $title = $str->get_title($article->title);
            
            // print URL
            $print_url = '/imprimir/' . $title. '/' . $category_name . '/';
            
            $breadcrub   = array();
            $breadcrub[] = array('text' => $ccm->get_title($category_name),
                                 'link' => '/seccion/' . $category_name . '/' );
            if(!empty($subcategory_name)) {
                $breadcrub[] = array(
                    'text' => $ccm->get_title($subcategory_name),
                    'link' => '/seccion/' . $category_name . '/' . $subcategory_name . '/'
                );
                
                $print_url .= $subcategory_name . '/';
            }
            
            $tpl->assign('breadcrub', $breadcrub);
            
            $print_url .= $article->pk_content . '.html';
            $tpl->assign('print_url', $print_url);
            $tpl->assign('sendform_url', '/article.php?action=sendform&id=' . $_GET['id'] . '&category_name=' .
                                        $category_name . '&subcategory_name=' . $subcategory_name);
            
            /******************************  CATEGORIES & SUBCATEGORIES  *********************************/
            require_once ("index_sections.php");
            /******************************  CATEGORIES & SUBCATEGORIES  *********************************/
            
            $tpl->assign('category_name', $category_name);
            
            Content::setNumViews($_GET['id']);
            $cm = new ContentManager();
            
                                
            $tpl->assign('article', $article);
            /**************** PHOTOs ****************/
            if(isset($article->img1) and ($article->img1 != 0)){
                $photoExt = new Photo($article->img1);
                $tpl->assign('photoExt', $photoExt);
            }

            if(isset($article->img2) and ($article->img2 != 0) and ($article->img1 != $article->img2) ){
                $photoInt = new Photo($article->img2);
                $tpl->assign('photoInt', $photoInt);
            }

            if(isset($article->fk_video2) and ($article->fk_video2 != 0)){
                $videoInt = new Video($article->fk_video2);
                $tpl->assign('videoInt', $videoInt->videoid);
            }
            /**************** PHOTOs ****************/

            /******* RELATED  CONTENT *******/
            $rel= new Related_content();

            $relationes = $rel->cache->get_relations_int($_REQUEST['id']);
            $relat = $cm->cache->getContents($relationes);

            // Filter by scheduled {{{
            $relat = $cm->getInTime($relat);
            // }}}
            //Nombre categoria correcto.
            foreach($relat as $ril) {
                $ril->category_name=$ccm->get_title($ril->category_name);
            }
            $comment = new Comment();
            $comments = $comment->get_public_comments($_REQUEST['id']);

            $tpl->assign('num_comments', count($comments));
            $tpl->assign('relationed', $relat);

            /******* SUGGESTED CONTENTS *******/
            $objSearch = cSearch::Instance();
            $arrayResults=$objSearch->SearchSuggestedContents($article->metadata, 'Article', "pk_fk_content_category= ".$article->category." AND contents.available=1 AND pk_content = pk_fk_content", 4);

            $tpl->assign('suggested', $arrayResults);


            // Advertisements for single article
            require_once('article_advertisement.php');

            
            //******** Modules and containers of Column3
            //If $subcategory is empty articles_express are from $category
            $articles_express = $cm->find_by_category_name('Article', (empty($subcategory_name)?$category_name:$subcategory_name),
                                                           'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1',
                                                           'ORDER BY changed DESC LIMIT 0, 5');
            
            // Filter by scheduled {{{
            $articles_express = $cm->getInTime($articles_express);
            // }}}
            
            $articles_express = $cm->paginate_num($articles_express, 5);
            $pages = $cm->pager;
            $pages_express = $pages->_totalPages;
            $tpl->assign('articles_express', $articles_express);                        
            $tpl->assign('pages_express', $pages_express);            

            //If $subcategory is empty articles_viewed are from $category
            $articles_viewed = $cm->find_by_category_name('Article', (empty($subcategory_name)?$category_name:$subcategory_name),
                                                          'content_status=1  AND available=1 AND fk_content_type=1',
                                                          'ORDER BY views DESC LIMIT 0 , 10');
            
            // Filter by scheduled {{{
            $articles_viewed = $cm->getInTime($articles_viewed);
            // }}}
            
            $articles_viewed = $cm->paginate_num($articles_viewed, 8);
            $pages = $cm->pager;
            $pages_viewed = $pages->_totalPages;
            $tpl->assign('pages_viewed', $pages_viewed);
            $tpl->assign('articles_viewed', $articles_viewed);                        
        } break;


        
        default: {
            Application::forward301('index.php');
        } break;
    }
    
} else {
    Application::forward301('index.php');
}

$tpl->display('article.tpl',$cache_id);

