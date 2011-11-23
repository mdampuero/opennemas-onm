<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Redirect Mobile browsers to mobile site unless a cookie exists.
*/
$app->mobileRouter();

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

/**
 * Getting request params
 **/
$dirtyID = filter_input(INPUT_GET,'article_id',FILTER_SANITIZE_STRING);

if(empty($dirtyID)) {
    $dirtyID = filter_input(INPUT_POST,'article_id',FILTER_SANITIZE_STRING);
}

$articleID = Content::resolveID($dirtyID);
 

$tpl->assign('contentId', $articleID); // Used on module_comments.tpl

if($_REQUEST['action']=='vote' ||  $_REQUEST['action']=='rating' ) {
    $category_name = 'home';
    $subcategory_name = null;
// If $action == 'rss' desnormalize process
}else{
    if(preg_match('@rss@',$_REQUEST['action'])) {
        $category_name = ((isset($_REQUEST['category_name']) ? $_REQUEST['category_name'] : ''));
        $subcategory_name = ((isset($_REQUEST['subcategory_name']) ? $_REQUEST['subcategory_name'] : ''));
    } else {
        if(!empty($articleID)){
            $article = new Article($articleID);

             if ($_SERVER['REQUEST_URI'] != '/'.$article->uri) {
                 Application::forward301('/'.$article->uri);
             }
            $article->category_name = $article->loadCategoryName($articleID);
            $category_name = $article->category_name;
            $subcategory_name = null;
        }
    }

    // Normalizar os nomes
    list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);
    $_GET['category_name'] = $category_name;
    $_GET['subcategory_name'] = $subcategory_name;

    $section = (!empty($subcategory_name))? $subcategory_name: $category_name;
    $section = (is_null($section))? 'home': $section;


    if (isset($category_name) && !empty($category_name)) {
        
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
    } elseif(isset($_REQUEST["action"]) && (preg_match('@rss@',$_REQUEST['action']))) {
        $_GET['category_name'] = $category_name = 'home';
    } elseif(isset($_REQUEST["action"]) && ($_REQUEST["action"]!="rating" && $_REQUEST["action"]!="vote" && $_REQUEST["action"]!="rss" && $_REQUEST["action"]!="get_plus")) {
        Application::forward301('/');
    }
}

/**************************************  SECURITY  *******************************************/

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'read': {
            // Load config
            $tpl->setConfig('articles');

            /******************************  BREADCRUB *********************************/
            $str = new String_Utils();
            $title = $str->get_title($article->title);

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
            $tpl->assign('sendform_url', '/controllers/article.php?action=sendform&article_id=' . $_GET['article_id'] . '&category_name=' .
                                        $category_name . '&subcategory_name=' . $subcategory_name);

            // Check if $section is "in menu" then show breadcrub
            $cat = $ccm->getByName($section);
            if(!is_null($cat) && $cat->inmenu) {
                $tpl->assign('breadcrub', $breadcrub);
            }



            /******************************  CATEGORIES & SUBCATEGORIES  ******/
            require_once ("index_sections.php");
            /******************************  CATEGORIES & SUBCATEGORIES  ******/

            $tpl->assign('category_name', $_GET['category_name']);

            $cm = new ContentManager();

            if (($article->available==1) && ($article->in_litter==0)
                && ($article->isStarted())
            ) {

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

                /**
                 * Getting comments for current article
                 **/
                $comment = new Comment();
                $comments = $comment->get_public_comments($articleID);
                $tpl->assign('num_comments', count($comments));
                $tpl->assign('comments', $comments);


                $cache_id = $tpl->generateCacheId(
                    $category_name, $subcategory_name, $articleID
                );

                // Advertisements for single article NO CACHE
                require_once('article_advertisement.php');

                $tpl->assign('article', $article);

                if (($tpl->caching == 0)
                    || !$tpl->isCached('article/article.tpl', $cache_id) )
                {

                    

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
                            $cm->find_by_category_name(
                                'Video',
                                $actual_category,
                                'contents.content_status=1',
                                'ORDER BY created DESC LIMIT 0 , 1'
                            );
                        if(isset($video[0])){ $tpl->assign('videoInt', $video[0]); }
                    }

                    /**************** PHOTOs ****************/

                    /******* RELATED  CONTENT *******/
                    $rel= new Related_content();

                    $relationes =
                        $rel->cache->get_relations_int($articleID);
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
                    $arrayResults =null;
                    if(!empty($article->metadata)) {
                        $objSearch = cSearch::Instance();
                        $arrayResults=$objSearch->SearchSuggestedContents(
                            $article->metadata,
                            'Article',
                            "pk_fk_content_category= ".$article->category.
                            " AND contents.available=1 AND pk_content = pk_fk_content",
                            4
                        );
                    }
                   // $arrayResults= $cm->getInTime($arrayResults);
                    $tpl->assign('suggested', $arrayResults);

                } // end if $tpl->is_cached




                /************* COLUMN-LAST *******************************/
                $relia  = new Related_content();
                $other_news =
                    $cm->find_by_category_name('Article',
                                               $actual_category,
                                                'contents.frontpage=1'
                                                .' AND contents.content_status=1'
                                                .' AND contents.available=1'
                                                .' AND contents.fk_content_type=1'
                                                .' AND contents.pk_content != '.$articleID.''
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

        case 'index_rss': {

            /******************************  CATEGORIES & SUBCATEGORIES  ******/
            require_once ("index_sections.php");

            $cacheID = $tpl->generateCacheId('Index', '', "RSS");

            $ccm = ContentCategoryManager::get_instance();

            $categoriesTree = $ccm->getCategoriesTreeMenu();
            $opinionAuthors = Author::list_authors();

            $tpl->assign('categoriesTree', $categoriesTree);
            $tpl->assign('opinionAuthors', $opinionAuthors);
            $tpl->display('rss/index.tpl', $cacheID);
            exit(0);

        }

        case 'rss': {

            // Initialicing variables
            $tpl->setConfig('rss');
            $title_rss = "";
            $rss_url = SITE_URL;

            if ((strtolower($category_name)=="opinion")
                && isset($_GET["author"]))
            {

                $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, "RSS".$_GET["author"]);

            } else {

                $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, "RSS");

            }

            if (!$tpl->isCached('rss/rss.tpl', $cache_id))
            {

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

                    // Fetch articles in home
                    $articles_home = $cm->find( 'Article',
                                                'contents.in_home=1 AND contents.frontpage=1 AND '
                                                .'contents.fk_content_type=1 AND contents.content_status=1 '
                                                .'AND  contents.available=1',
                                                'ORDER BY created DESC');


                    // Fetch the photo and category name for this element
                    foreach ($articles_home as $i => $article) {

                        if (isset($article->img1) && $article->img1 != 0) {
                            $photos[$article->id] = new Photo($article->img1);
                        }

                        $article->category_name = $article->loadCategoryName($article->id);
                    }

                // If is opinion
                } elseif ($category_name == 'opinion') {

                    // get all the authors of opinions
                    if (!isset ($_GET['author'])) {

                        $articles_home = $cm->find_listAuthors('contents.available=1 and contents.content_status=1', 'ORDER BY created DESC LIMIT 0,50');

                    // get articles for the author in opinion
                    } else {

                        $articles_home = $cm->find_listAuthors('opinions.fk_author='.((int)$_GET['author']).' and  contents.available=1  and contents.content_status=1','ORDER BY created DESC  LIMIT 0,50');

                        if (count($articles_home)) {
                            $title_rss = 'Opiniones de «'.$articles_home[0]['name'].'»';
                        } else {
                            $title_rss = 'Este autor no tiene opiniones todavía.';
                        }
                    }

                // Get the RSS for the rest of categories
                } else {

                    // If frontpage contains a SUBCATEGORY the SQL request will be diferent

                    if (isset($subcategory_name) && !empty ($subcategory_name)) { $category_name = $subcategory_name; }

                    $articles_home = $cm->find_by_category_name('Article',
                                                                $category_name,
                                                                'contents.content_status=1 AND contents.frontpage=1 AND '
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

        case 'captcha': {
            $width  = isset($_GET['width'])  ? $_GET['width']  : '176';
            $height = isset($_GET['height']) ? $_GET['height'] :  '49';
            $characters = isset($_GET['characters']) && $_GET['characters'] > 1 ? $_GET['characters'] : '5';

            $captcha = new CaptchaSecurityImages($width, $height, $characters, dirname(__FILE__).'/media/fonts/monofont.ttf');
            exit(0);
        } break;



        case 'vote': {

            $ip = $_SERVER['REMOTE_ADDR'];
            $ip_from = $_GET['i'];
            $vote_value = intval($_GET['v']); // 1 A favor o 2 en contra
            $page = (!isset($_GET['p']))? 0: intval($_GET['p']);

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
                    $html_out .= '<div class="textoNoticiaMas"><a href="'.$article["uri"].'">'.stripslashes($article["title"]).'</a> ('.$article["num"].' comentarios)</div>';
                    $html_out .= '</div>';
                    $html_out .= '<div class="fileteNoticiaMas"><img src="'.TEMPLATE_USER_PATH.MEDIA_IMG_DIR.'/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>';
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
                    $html_out .= '<div class="textoNoticiaMas"><a href="'.$article->uri.'">'.stripslashes($article->title).'</a></div>';
                    $html_out .= '</div>';
                    $html_out .= '<div class="fileteNoticiaMas"><img src="'.TEMPLATE_USER_PATH.MEDIA_IMG_DIR.'/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>';
                    $html_out .= '</div>';
                }
            }

            Application::ajax_out($html_out);

        } break;

        case 'print': {
            // Article
            $article = new Article($articleID);

            // Breadcrub/Pathway
            $breadcrub   = array();
            $breadcrub[] = array('text' => $ccm->get_title($category_name),
                                 'link' => '/seccion/' . $category_name . '/' );

            // URL impresión
            $str = new String_Utils();
            $title = $str->get_title($article->title);
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
            $tpl->display('article/article_printer.tpl');
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
            $tpl->display('article/article_sendform.tpl'); // Don't disturb cache
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
            require(SITE_LIBS_PATH."/phpmailer/class.phpmailer.php");

            $tplMail = new Template(TEMPLATE_USER);

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
            $mail->Subject  = $_REQUEST['name_sender'].' ha compartido contigo un contenido de '.SITE_FULLNAME;  //substr(strip_tags($_REQUEST['body']), 0, 100);

            $tplMail->assign('destination', 'amig@,');

            // Load permalink to embed into content
            $article = new Article($articleID);

            $tplMail->assign('mail', $mail);
            $tplMail->assign('article', $article);


            // Filter tags before send
            $permalink = preg_replace('@([^:])//@', '\1/', SITE_URL . $article->permalink);
            $message = $_REQUEST['body'];
            $tplMail->assign('body', $message);
            if (!empty($article->agency)) {
                $agency = $article->agency;
            } else {
                $agency = SITE_FULLNAME;
            }
            $tplMail->assign('agency',$agency);

            if (empty($article->summary)) {
                $summary = substr(strip_tags(stripslashes($article->body)), 0, 300)."...";
            } else {
                $summary = stripslashes($article->summary);
            }
            $tplMail->assign('summary', $summary);


            if (method_exists($tpl, '_get_plugin_filepath')) {
                //handle with Smarty version 2
                require_once $tpl->_get_plugin_filepath('function','articledate');
            } else {
                //handle with Smarty version 3 beta 8
                foreach ($tpl->plugins_dir as $value) {
                    $filepath = $value ."/function.articledate.php";
                    if (file_exists($filepath)) {
                        require_once $filepath;
                    }
                }
            }


            //require_once $tpl->_get_plugin_filepath('function', 'articledate');
            $params['created'] = $article->created;
            $params['updated'] = $article->updated;
            $params['article'] = $article;
            $date = smarty_function_articledate($params,$tpl);
            $tplMail->assign('date', $date);

            $tplMail->caching = 0;
            $mail->Body = $tplMail->fetch('article/email_send_to_friend.tpl');

            $mail->AltBody = $tplMail->fetch('article/email_send_to_friend_just_text.tpl');

            /*
             * Implementacion para enviar a multiples destinatarios separados por coma
             */
            $destinatarios = explode(',', $_REQUEST['destination']);

            foreach ($destinatarios as $dest) {
                //$mail->AddAddress(trim($dest));
                $mail->AddBCC(trim($dest));
            }

            if( $mail->Send() ) {
                $tpl->assign('message', 'Noticia enviada correctamente.');
            } else {
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    header("HTTP/1.0 404 Not Found");
                }
                $tpl->assign('message', 'La noticia no pudo ser enviada, inténtelo de nuevo más tarde. <br /> Disculpe las molestias.');
            }

            $tpl->caching = 0;
            $tpl->display('article/article_sendform.tpl'); // Don't disturb cache
            exit(0);
        } break;

        default: {
            Application::forward301('index.php');
        } break;
    }

} else {
    Application::forward301('index.php');
}

$tpl->display('article/article.tpl', $cache_id);
