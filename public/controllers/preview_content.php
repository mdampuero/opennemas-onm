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
/**
 * Getting request params
 **/
$articleID = filter_input(INPUT_GET,'id',FILTER_SANITIZE_STRING);
$tpl->assign('contentId', $articleID); // Used on module_comments.tpl
$category_name = $ccm->get_category_name_by_content_id(filter_input(INPUT_GET,'id',FILTER_SANITIZE_STRING));
$subcategory_name = filter_input(INPUT_GET,'subcategory_name',FILTER_SANITIZE_STRING);
// Normalizar os nomes
list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);

/**************************************  SECURITY  *******************************************/

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'article': {
            // Load config
            $tpl->setConfig('articles');

            $article = new Article($_REQUEST['id']);

            $str = new StringUtils();
            $title = $str->get_title($article->title);


            /******************************  CATEGORIES & SUBCATEGORIES  ******/
            require_once ("index_sections.php");
            /******************************  CATEGORIES & SUBCATEGORIES  ******/

            $tpl->assign('category_name', $category_name);

            $cm = new ContentManager();

            if(($article->available==1) && ($article->in_litter==0) && ($article->isStarted())) {

                // Increment numviews if it's accesible
                Content::setNumViews($article->pk_article);
                if(isset($subcategory_name) && !empty($category_name)){
                    $actual_category = $subcategory_name;
                }else{
                    $actual_category =$category_name;
                }
                $actual_category_id = $ccm->get_id($actual_category);
                $actual_category_title = $ccm->get_title($actual_category);
                $tpl->assign('actual_category_title',$actual_category_title);

                $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, $_GET['id']);

                // Advertisements for single article NO CACHE
                require_once('article_advertisement.php');

                if (($tpl->caching == 0)
                    || !$tpl->isCached('article/article.tpl', $cache_id) )
                {

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
                    $arrayResults=$objSearch->SearchSuggestedContents($article->metadata,
                                                                      'Article',
                                                                      "pk_fk_content_category= ".$article->category.
                                                                      " AND contents.available=1 AND pk_content = pk_fk_content",
                                                                      4);
                   // $arrayResults= $cm->getInTime($arrayResults);
                    $tpl->assign('suggested', $arrayResults);

                } // end if $tpl->is_cached




                /************* COLUMN-LAST *******************************/
                $relia  = new RelatedContent();
                $other_news =
                    $cm->find_by_category_name('Article',
                                               $actual_category,
                                                'contents.frontpage=1'
                                                .' AND contents.content_status=1'
                                                .' AND contents.available=1'
                                                .' AND contents.fk_content_type=1'
                                                .' AND contents.pk_content != '.$_REQUEST['id'].''
                                                ,'ORDER BY views DESC, placeholder ASC,'
                                                .'       position ASC, created DESC'
                                                .' LIMIT 1,3');
                $tpl->assign('other_news', $other_news);

                require_once('widget_headlines_past.php');
               // require_once('widget_media.php');


                /************* END COLUMN-LAST ***************************/


                require_once("widget_static_pages.php");

            } else {
                Application::forward301('/404.html');
            }


            if(!isset($lastAlbum)){ $lastAlbum=null; }
            $tpl->assign('lastAlbum', $lastAlbum);
        } break;



        default: {
            Application::forward301('index.php');
        } break;
    }

} else {
    Application::forward301('index.php');
}

$tpl->display('article/article.tpl',$cache_id);

