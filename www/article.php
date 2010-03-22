<?php
/**
 * Este ficheiro é o que xera a noticia interior e os RSS de cada sección.
 * No case "rss" faise todo o relacionado cos RSS, OLLO remata a execución xusto nese break
 */

//error_reporting(E_ALL);
require('./config.inc.php');
require_once('./core/application.class.php');

Application::import_libs('*');
$app = Application::load();

// redirect to /mobile/ if it's mobile device request
$app->mobileRouter();

require_once('./core/content_manager.class.php');
require_once('./core/content.class.php');
require_once('./core/article.class.php');
require_once('./core/author.class.php');
require_once('./core/advertisement.class.php');
require_once('./core/related_content.class.php');
require_once('./core/content_category.class.php');
require_once('./core/application.class.php');
require_once('./core/attachment.class.php');
require_once('./core/attach_content.class.php');
require_once('./core/rating.class.php');
require_once('./core/vote.class.php');
require_once('./core/captcha.class.php');
require_once('./core/search.class.php');

// For performance
require_once('./core/method_cache_manager.class.php');

require_once('./core/img_galery.class.php');
require_once('./core/photo.class.php');
require_once('./core/video.class.php');
require_once('./core/comment.class.php');

require_once('./core/content_category.class.php');
require_once('./core/content_manager.class.php');
require_once('./core/content_category_manager.class.php');

$tpl = new Template(TEMPLATE_USER);

$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();
/*********************************  CLASSIC.XORNAL.COM  **************************************/
if ($_REQUEST["sid"]) {
    Application::forward301('http://clasica.xornal.com/article.php?sid='.$_REQUEST["sid"]);
}
/*********************************  CLASSIC.XORNAL.COM  **************************************/


/**************************************  SECURITY  *******************************************/
//Is category initialized redirect the user to /
/* $category_name = $_GET['category_name'];
$subcategory_name = $_GET['subcategory_name']; */

// If $action == 'rss' desnormalize process
if($_REQUEST['action']=='rss') {
    $category_name = $_REQUEST['category_name'];
    $subcategory_name = $_REQUEST['subcategory_name'];
} else {
    $article = new Article($_REQUEST['article_id']);
    $article->category_name = $article->loadCategoryName($_REQUEST['article_id']);

    $category_name = $article->category_name;
    $subcategory_name = null;
}

// Normalizar os nomes 
list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);
$_GET['category_name'] = $category_name;
$_GET['subcategory_name'] = $subcategory_name;

$section = (!empty($subcategory_name))? $subcategory_name: $category_name;
$section = (is_null($section))? 'home': $section;


if (isset($category_name) && !empty($category_name)) {
    if ($category_name == 'politica') {
        $category_name = 'polItica';
    }

    if (!$ccm->exists($category_name)) {
        Application::forward301('/');
    } else {
        $category = $ccm->get_id($category_name);
    }

    if (isset($subcategory_name) && !empty($subcategory_name)) {
        if (!$ccm->exists($subcategory_name)) {
            Application::forward301('/');
        } else {
            $subcategory = $ccm->get_id($subcategory_name);
        }
    }    
} elseif(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="rss")) {
    $_GET['category_name'] = $category_name = 'home';
} elseif(isset($_REQUEST["action"]) && ($_REQUEST["action"]!="rating" && $_REQUEST["action"]!="vote" && $_REQUEST["action"]!="rss" && $_REQUEST["action"]!="get_plus")) {
    Application::forward301('/');
}

$tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);

/**************************************  SECURITY  *******************************************/

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {        
        case 'read': {            
            // Load config
            $tpl->setConfig('articles');

             /******************************  BREADCRUB *********************************/
            /*$article = new Article($_REQUEST['article_id']);*/                        
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
            
            $print_url .= $article->pk_content . '.html';
            $tpl->assign('print_url', $print_url);
            $tpl->assign('sendform_url', '/article.php?action=sendform&article_id=' . $_GET['article_id'] . '&category_name=' .
                                        $category_name . '&subcategory_name=' . $subcategory_name);
            
            // Check if $section is "in menu" then show breadcrub
            $cat = $ccm->getByName($section);
            if(!is_null($cat) && $cat->inmenu) {
                $tpl->assign('breadcrub', $breadcrub);
            }
            
            /******************************  CATEGORIES & SUBCATEGORIES  *********************************/
            require_once ("index_sections.php");
            /******************************  CATEGORIES & SUBCATEGORIES  *********************************/

            $tpl->assign('category_name', $_GET['category_name']);

            $cm = new ContentManager();
            
            //checking if the article is available for INTERNET
            //Otherwise it won't be showed
            //if(($article->available==1) && ($article->in_litter==0) && ($article->isInTime())) {
            if(($article->available==1) && ($article->in_litter==0) && ($article->isStarted())) {
                
                // Increment numviews if it's accesible
                Content::set_numviews($_GET['article_id']);
                
                $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, $_GET['article_id']);
                
                // MUTEXT CODE, DON'T use for performance
                // Application::getMutex($cache_id);                
        
                if( true || ($tpl->caching == 0) || !$tpl->is_cached('article.tpl', $cache_id) ) {
                    
                    $tpl->assign('article', $article);
                    /**************** PHOTOs ****************/
                 /*   if(isset($article->img1) and ($article->img1 != 0)){
                        $photoExt = new Photo($article->img1);
                        $tpl->assign('photoExt', $photoExt);

                    } */
 
                    if(isset($article->img2) and ($article->img2 != 0) ) {
                        $photoInt = new Photo($article->img2);
                        $tpl->assign('photoInt', $photoInt);

                    }
                    
                    if(isset($article->fk_video2) and ($article->fk_video2 != 09)){
                        $videoInt = new Video($article->fk_video2);
                        $tpl->assign('videoInt', $videoInt);

                    }else{
                        $video = $cm->find_by_category_name('Video',  $category_name, 'contents.content_status=1', 'ORDER BY created DESC LIMIT 0 , 1');
                        $tpl->assign('videoInt', $video[0]);
                      
                    }
                       
                    /**************** PHOTOs ****************/
                    
                    /******* RELATED  CONTENT *******/
                    $rel= new Related_content();
                    
                    $relationes = $rel->cache->get_relations_int($_REQUEST['article_id']);
                    $relat = $cm->cache->getContents($relationes);
              
                    // Filter by scheduled {{{
                    $relat = $cm->getInTime($relat);
                    // }}}
                    //Filter availables and not inlitter.
                    $relat = $cm->cache->getAvailable($relat);

                    
                    //Nombre categoria correcto.
                    foreach($relat as $ril) {
                        $ril->category_name=$ccm->get_title($ril->category_name);
                    }
                    $comment = new Comment();
                    $comments = $comment->get_public_comments($_REQUEST['article_id']);
                    
                    $tpl->assign('num_comments', count($comments));
                    $tpl->assign('relationed', $relat);
             
                    /******* SUGGESTED CONTENTS *******/
                    $objSearch = cSearch::Instance();                   
                    $arrayResults=$objSearch->SearchSuggestedContents($article->metadata, 'Article', "pk_fk_content_category= ".$article->category." AND contents.available=1 AND pk_content = pk_fk_content", 4);
                   // $arrayResults= $cm->getInTime($arrayResults);
                    $tpl->assign('suggested', $arrayResults);
                  
                } // end if $tpl->is_cached
                
                // END MUTEXT
                // Application::releaseMutex();

                /************* COLUMN-LAST *******************************/
                $relia  = new Related_content();
                $other_news = $cm->find_by_category_name('Article', $category_name, 'contents.frontpage=1 AND contents.content_status=1 AND contents.available=1  AND contents.fk_content_type=1  AND contents.pk_content != '.$_REQUEST['article_id'].'', 'ORDER BY views DESC, placeholder ASC, position ASC, created DESC LIMIT 1,3');
                /*foreach($other_news as $art) {

                    $image = new Photo($art->img1);
                    $art->img1_path = $image->path_file.$image->name;
                    $relationes = $relia->get_relations($articles_home[$aux]->id);
                    $relats = array();
                    foreach($relationes as $i => $id_rel) { //Se recorre el array para sacar todos los campos.
                        $obj = new Content($id_rel);
                        // Filter by scheduled {{{
                        if($obj->isInTime() && $obj->available==1 && $obj->in_litter==0) {
                           $relats[] =$obj;
                        }
                        // }}}
                    }
                    $art->related_contents = $relats;
                } */
                $tpl->assign('other_news', $other_news);

                require_once('widget_headlines_past.php');
               // require_once('widget_media.php');
                
                
                 /************* END COLUMN-LAST *******************************/

                // Advertisements for single article NO CACHE
                require_once('article_advertisement.php');
                
            } else {
                Application::forward301('/404.html');
            }
            
            //******** Modules and containers of Column3
             /************************************ ARTICLES EXPRESS **************************************
                $now= date('Y-m-d H:m:s',time()); //2009-02-28 21:00:13
                $articles_home_express = $cm->find('Article', 'content_status=1 AND available=1 AND fk_content_type=1 AND (starttime="0000-00-00 00:00:00" OR (starttime != "0000-00-00 00:00:00"  AND starttime<"'.$now.'")) AND (endtime="0000-00-00 00:00:00" OR (endtime != "0000-00-00 00:00:00"  AND endtime>"'.$now.'")) ', 'ORDER BY created DESC LIMIT 0 , 5 ');

                $tpl->assign('articles_home_express', $articles_home_express);

                /************************************ TITULARES DEL DIA  ************************************/
              //  require_once ("module_other_headlines.php");



                /***********************************PHOTO-ALBUM  ********************************************

                $album_photo = array();
                $lastAlbum = $cm->find('Album', ' contents.fk_content_type=7 and contents.available=1', 'ORDER BY favorite DESC, created DESC LIMIT 0 , 4');
                /* Don't need. Get crop /album/crops/album_id.jpg
               foreach($lastAlbum as $album){
                    $album->photo = $album->get_firstfoto_album($album->id);
                } */
                $tpl->assign('lastAlbum', $lastAlbum);
 

                /***********************************  PHOTO-ALBUM  *********************************************


        // ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
        $videos = $cm->find('Video', 'contents.content_status=1', 'ORDER BY created DESC LIMIT 0 , 4');

        foreach($videos as $video){
            $videos_authors[] = new Author($video->fk_user);
        }

        $tpl->assign('videos', $videos);
        $tpl->assign('videos_authors', $videos_authors);
            */
        } break;
 
        case 'rss': {
            // Load config
            $tpl->setConfig('rss');
            
            $title_rss = "";
            $rss_url = SITE_URL;
            
            if ((strtolower($category_name)=="opinion") && isset($_GET["author"])) {
                $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, "RSS".$_GET["author"]);
            } else {
                $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, "RSS");
            }                        
            
            //if (!$tpl->is_cached('rss.tpl', $cache_id) ) { // (1)
            // BEGIN MUTEX
            Application::getMutex($cache_id);                                    
            if (!$tpl->is_cached('rss.tpl', $cache_id) ) { // (2)
                if (isset($category_name) && !empty($category_name)) {
                    $category = $ccm->get_id($category_name);
                    $rss_url .= $category_name.SS;
                    $title_rss .= $category_name;
                    
                    if (isset($subcategory_name) && !empty($subcategory_name)) {
                        $subcategory = $ccm->get_id($subcategory_name);
                        $rss_url .= $subcategory_name.SS;
                        $title_rss .= " > ".$subcategory_name;
                    }
                } else {
                    $rss_url .= "home".SS;
                    $title_rss .= "PORTADA";
                }

                $photos = array();
                if ($category_name == 'home') {
                    $articles_home = $cm->find('Article',
                                        'contents.in_home=1 AND contents.frontpage=1 AND contents.fk_content_type=1 AND contents.content_status=1 AND  contents.available=1',
                                        'ORDER BY created DESC');
                    
                    $i = 0;
                    while ($i < count($articles_home)) {
                        if (isset($articles_home[$i]->img1) && $articles_home[$i]->img1!=0) {
                            $photos[$articles_home[$i]->id] = new Photo($articles_home[$i]->img1);
                        }
                        $i++;
                    }
                } elseif ($category_name == 'opinion') {
                    if (!isset ($_GET['author'])) {
                        $articles_home = $cm->find_listAuthors('contents.available=1 and contents.content_status=1', 'ORDER BY created DESC LIMIT 0,50');
                    } else {
                        $articles_home = $cm->find_listAuthors('opinions.fk_author='.($_GET['author']).' and  contents.available=1  and contents.content_status=1','ORDER BY created DESC  LIMIT 0,50');
                        $title_rss = strtoupper('OPINION > '.$articles_home[0]['name']);
                    }
                    
                } else {
                    //If frontpage contains a SUBCATEGORY the SQL request will be diferent
                    if (!isset ($subcategory_name)) {
                        if ($category_name=='cxg') {
                            $articles_home = $cm->find_by_category_name('Article',
                                                    $category_name, 'contents.available=1 AND contents.fk_content_type=1',
                                                    'ORDER BY created DESC LIMIT 0,50');
                        } else {
                            $articles_home = $cm->find_by_category_name('Article',
                                                    $category_name, 'contents.content_status=1 AND contents.frontpage=1 AND contents.available=1 AND contents.fk_content_type=1',
                                                    'ORDER BY created DESC LIMIT 0,50');
                        }                        
                        
                        $i=0;
                        while ($i < count($articles_home)) {
                            if (isset($articles_home[$i]->img1) && $articles_home[$i]->img1!=0) {
                                $photos[$articles_home[$i]->id] = new Photo($articles_home[$i]->img1);
                            }
                            $i++;
                        }
                        
                    } else {
                        $articles_home = $cm->find_by_category_name('Article',
                                                $subcategory_name, 'content_status=1 AND frontpage=1 AND available=1 AND fk_content_type=1',
                                                'ORDER BY created DESC');
                        
                        $i = 0;
                        while ($i < count($articles_home)) {
                            if (isset($articles_home[$i]->img1) && $articles_home[$i]->img1!=0) {
                                $aux = new Photo($articles_home[$i]->img1);
                            }
                            $i++;
                        }
                    }
                }
                
                // Filter by scheduled {{{
                $articles_home = $cm->getInTime($articles_home);
                // }}}
                
                $tpl->assign('title_rss', strtoupper($title_rss));
                $tpl->assign('rss', $articles_home);
                
                // FIXME: correxir isto cando se garda o artigo
                for($i=0, $total= count($articles_home); $i<$total; $i++) {
                    if(is_object($articles_home[$i])) { 
                        $str = $articles_home[$i]->permalink;
                        $str = mb_strtolower($str, 'UTF-8');
                        $str = mb_ereg_replace('[^a-z0-9áéíóúñüç_\,\-:\?\/\&\. ]', '', $str);
                        $str = mb_ereg_replace('([^:])//', '$1/', $str);
                        
                        $articles_home[$i]->permalink = $str;
                    } else {
                        $str = $articles_home[$i]['permalink'];
                        $str = mb_strtolower($str, 'UTF-8');
                        $str = mb_ereg_replace('[^a-z0-9áéíóúñüç_\,\-:\?\/\&\. ]', '', $str);
                        $str = mb_ereg_replace('([^:])//', '$1/', $str);
                        
                        $articles_home[$i]['permalink'] = $str;
                    }
                }
               
                $tpl->assign('photos', $photos);
                $tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);
                
                $tpl->assign('SITE_URL', SITE_URL);
                $tpl->assign('RSS_URL', $rss_url);
            } // end if(!$tpl->is_cached('rss.tpl', $cache_id)) (1)
            
            // END MUTEXT
            Application::releaseMutex();
            // } // end if(!$tpl->is_cached('rss.tpl', $cache_id)) (2)
            
            header('Content-type: application/rss+xml; charset=utf-8');
            $tpl->display('rss.tpl', $cache_id);
            
                        
            
            exit(0); // finish execution for don't disturb cache
        } break;
        
        case 'captcha': {            
            $width  = isset($_GET['width'])  ? $_GET['width']  : '176';
            $height = isset($_GET['height']) ? $_GET['height'] :  '49';
            $characters = isset($_GET['characters']) && $_GET['characters'] > 1 ? $_GET['characters'] : '5';
            
            $captcha = new CaptchaSecurityImages($width, $height, $characters, dirname(__FILE__).'/media/fonts/monofont.ttf');
            exit(0);
        } break;
        
        case 'rating': {
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $ip_from = $_GET['i'];
            $vote_value = intval($_GET['v']);
            $page = $_GET['p'];
            $article_id = $_GET['a'];
            
            if($ip != $ip_from) {
                Application::ajax_out("Error!");
            }
            
            //Comprobamos que exista el artículo que se quiere votar
            $content = new Content($article_id);
            if(is_null($content->id)) {
                Application::ajax_out("Error!");
            }
            
            $rating = new Rating($content->id);
            $update = $rating->update($vote_value,$ip);
            
            if($update) {
                $html_out = $rating->render($page,'result',1);
            } else {
                $html_out = "Ya ha votado anteriormente esta noticia.";
            }
            
            Application::ajax_out($html_out);
        } break;

         case 'vote': {

            $ip = $_SERVER['REMOTE_ADDR'];
            $ip_from = $_GET['i'];
            $vote_value = intval($_GET['v']); // 1 A favor o 2 en contra
            $page = $_GET['p'];
            $comment_id = $_GET['a'];

            if($ip != $ip_from) {
                Application::ajax_out("Error no ip vote!");
            } 

            $vote = new Vote($comment_id);
            if(is_null($vote)) {
                Application::ajax_out("Error no  vote value!");
            }
            $update = $vote->update($vote_value,$ip);

            if($update) {
                $html_out = $vote->render($page,'result',1);
            } else {
                $html_out = "Ya ha votado anteriormente este comentario.";
            }
  
            Application::ajax_out($html_out);
        } break;


        case 'get_plus': {
            if($_GET["content"]=="Comment") {

                $cm = new ContentManager();
                $articles = $cm->cache->getMostComentedContent('Article', true, $_REQUEST['category'], $_REQUEST['days']);

                //$tpl->
                $html_out = "";
                foreach ($articles as $article) {
                    $html_out .= '<div class="CNoticiaMas">';
                    $html_out .= '<div class="CContainerIconoTextoNoticiaMas">';
                    $html_out .= '<div class="iconoNoticiaMas"></div>';
                    $html_out .= '<div class="textoNoticiaMas"><a href="'.$article["permalink"].'">'.stripslashes($article["title"]).'</a> ('.$article["num"].' comentarios)</div>';
                    $html_out .= '</div>';
                    $html_out .= '<div class="fileteNoticiaMas"><img src="'.TEMPLATE_USER_PATH_WEB.MEDIA_IMG_DIR.'/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>';
                    $html_out .= '</div>';
                }
            } else {

                $cm = new ContentManager();

                $articles_viewed = $cm->cache->getMostViewedContent('Article', true, $_REQUEST['category'], $_REQUEST['author'], $_REQUEST['days']);

                $html_out = "";
                foreach ($articles_viewed as $article) {
                    $html_out .= '<div class="CNoticiaMas">';
                    $html_out .= '<div class="CContainerIconoTextoNoticiaMas">';
                    $html_out .= '<div class="iconoNoticiaMas"></div>';
                    $html_out .= '<div class="textoNoticiaMas"><a href="'.$article->permalink.'">'.stripslashes($article->title).'</a></div>';
                    $html_out .= '</div>';
                    $html_out .= '<div class="fileteNoticiaMas"><img src="'.TEMPLATE_USER_PATH_WEB.MEDIA_IMG_DIR.'/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>';
                    $html_out .= '</div>';
                }
            }
            
            Application::ajax_out($html_out);

        } break;

        case 'print': {
            // Article
            $article = new Article($_REQUEST['article_id']);
            
            // Breadcrub/Pathway
            $breadcrub   = array();
            $breadcrub[] = array('text' => $ccm->get_title($category_name),
                                 'link' => '/seccion/' . $category_name . '/' );
            
            // URL impresión
            $print_url = '/imprimir/' . $title. '/' . $category_name . '/';
            
            if(!empty($subcategory_name)) {
                $breadcrub[] = array(
                    'text' => $ccm->get_title($subcategory_name),
                    'link' => '/seccion/' . $category_name . '/' . $subcategory_name . '/'
                );
                
                $print_url .= $subcategory_name . '/';
            }            
            
            $print_url .= $article->pk_content . '.html';
            $tpl->assign('print_url', $print_url);
            
            $cat = $ccm->getByName($section);
            if(!is_null($cat) && $cat->inmenu) {
                $tpl->assign('breadcrub', $breadcrub);
            }
            
            // Foto interior
            if(isset($article->img2) and ($article->img2 != 0)){
                $photoInt = new Photo($article->img2);
                $tpl->assign('photoInt', $photoInt);
            }            
            
            $tpl->caching = 0;
            $tpl->assign('article', $article);
            $tpl->display('article_printer.tpl');
            exit(0);
        } break;
        
        
        case 'sendform': {
            require_once('session_bootstrap.php');
            $token = $_SESSION['sendformtoken'] = md5(uniqid('sendform'));
            
            //Ya se iniciliza en la linea 50
            //$article = new Article($_REQUEST['article_id']);
            $tpl->assign('article', $article);
            
            $tpl->assign('token', $token);
            $tpl->assign('category_name', $category_name);
            $tpl->assign('subcategory_name', $subcategory_name);
            
            $tpl->caching = 0;
            $tpl->display('article_sendform.tpl'); // Don't disturb cache
            exit(0);
        } break;
        
        case 'send': {
            require_once('session_bootstrap.php');
            
            // Check if magic_quotes is enabled and clear globals arrays
            String_Utils::disabled_magic_quotes();
            
            // Check direct access
            if($_SESSION['sendformtoken'] != $_REQUEST['token']) {
                Application::forward('/');
            }                        
            
            // Send article to friend
            require(dirname(__FILE__)."/libs/phpmailer/class.phpmailer.php");  
            
            $mail = new PHPMailer();  
            
            $mail->Host     = "localhost";        
            $mail->Mailer   = "smtp";
            /*$mail->Username = '';
            $mail->Password = '';*/
            
            $mail->CharSet = 'UTF-8';
            $mail->Priority = 5; // Low priority
            $mail->IsHTML(true);
            
            
            $mail->From     = $_REQUEST['sender'];
            $mail->FromName = $_REQUEST['name_sender'];
            $mail->Subject  = substr(strip_tags($_REQUEST['body']), 0, 100);
            
            // Load permalink to embed into content
            $article = new Article($_REQUEST['article_id']);
            
            // Filter tags before send
            $permalink = preg_replace('@([^:])//@', '\1/', SITE_URL . $article->permalink);
            $message = $_REQUEST['body'];
            
            if (empty($article->agency)) {
                $agency = $article->agency;
            } else {
                $agency = 'xornal.com';
            }

            if (empty($article->summary)) {
                $summary = substr(strip_tags(stripslashes($article->body)), 0, 300)."...";
            } else {
                $summary = stripslashes($article->summary);
            }

            require_once $tpl->_get_plugin_filepath('function', 'articledate');
            $params['created'] = $article->created;
            $params['updated'] = $article->updated;
            $params['article'] = $article;
            $date = smarty_function_articledate($params,$tpl);

            $mail->Body = strip_tags('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">').
                ('<html xmlns="http://www.w3.org/1999/xhtml">').
                ('<head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /><meta name="language" content="es" />').
                ('<div style="font-size: 11px; font-family: Arial;"><table border="0" cellpadding="0" cellspacing="0" width="765"><tbody>').
                ('<tr><td bgcolor="014687"><a href="http://www.xornal.com/" border="0" target="_blank"><img src="http://www.xornal.com/themes/xornal/images/xornal-logo.jpg" alt="Xornal.Com" border="0"></a><br></td></tr>').
                ('<tr><td><div style="margin: 0px 0px 4px; padding-top: 10px; color:#014687; font-size: 18px; font-weight: bold; font-family: Arial;">ARTÍCULO RECOMENDADO<div></div></div></td></tr>').
                ('<tr><td><b>Hola '.$_REQUEST['destination'].',</b><br>').
                ('<b>'.$mail->FromName.' quiere compartir contigo la siguiente información: </b><br><br> '.$message.'<br><br></td></tr>').
                ('<tr><td><img src="http://www.xornal.com/themes/xornal/images/fileteFondoNota.gif" height="1" width="1"></td></tr>').
                ('<tr><td><div style="margin: 0px 0px 0px; padding: 0px; font-family: Arial; font-size:26px; color:#333333; font-weight: normal;  border-top: 1px solid #014687;">').
                ('<b>'.stripslashes($article->title).'</b></div><br><div style="margin: 0px 0px 0px; padding: 0px; color:#014687; font-size: 11px; font-weight: bold; text-align: left;">').
                ('<b>'.$agency.'</b> | '.$date.'</div></td></tr>').
                ('<tr><td><div style="margin: 0px; color:#333333; font-size: 12px; line-height: 15px; border-bottom: 1px solid #014687; padding-bottom: 5px; ">').
                ($summary.'</div></td></tr>').
                ('<tr><td><div style="color:#014687; text-align:right; text-decoration: underline;  font-size: 12px; line-height: 15px;">').
                ('<a href="'.$permalink.'" target="_blank">Ir al artículo completo</a></div>').
                ('</td></tr></tbody></table></div></body></html>');

                            
            $mail->AltBody = strip_tags($message) . "\n" . $permalink;  
            $mail->AddAddress( $_REQUEST['destination'] );    
              
            if( $mail->Send() ) {  
                $tpl->assign('message', 'Noticia enviada correctamente.');
            } else {
                $tpl->assign('message', 'La noticia no pudo ser enviada, inténtelo de nuevo más tarde. <br /> Disculpe las molestias.');
            }
            
            $tpl->caching = 0;
            $tpl->display('article_sendform.tpl'); // Don't disturb cache
            exit(0);
        } break;
        
        default: {
            Application::forward301('index.php');
        } break;
    }
    
} else {
    Application::forward301('index.php');
}

$tpl->display('article.tpl', $cache_id);

