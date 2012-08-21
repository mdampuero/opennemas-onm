<?php

/**
 * Start up and setup the app
*/
require_once '../bootstrap.php';
use Onm\Settings as s;

/**
 * Redirect Mobile browsers to mobile site unless a cookie exists.
*/
// $app->mobileRouter();

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

/**
 * Getting request params
 **/
$dirtyID          = $request->query->filter('article_id', '', FILTER_SANITIZE_STRING);
$category_name    = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
$subcategory_name = null;
$action           = $request->query->filter('action', '', FILTER_SANITIZE_STRING);
$ext              = $request->query->filter('ext', 0, FILTER_VALIDATE_INT);
$tpl->assign('ext', $ext); // Used on _other-contents.tpl

/**
 * Getting Synchronize setting params
 **/
$wsUrl = '';
$syncParams = s::get('sync_params');
foreach ($syncParams as $siteUrl => $categoriesToSync) {
    foreach ($categoriesToSync as $value) {
        if (preg_match('/'.$category_name.'/i', $value)) {
            $wsUrl = $siteUrl;
        }
    }
}

/**
 * Getting resolved Id and category title
 */
$articleID = json_decode(file_get_contents($wsUrl.'/ws/contents/resolve/'.$dirtyID));
$actualCategoryTitle = json_decode(file_get_contents($wsUrl.'/ws/categories/title/'.$category_name));
$tpl->assign('contentId', $articleID); // Used on module_comments.tpl
$tpl->assign('actual_category_title', $actualCategoryTitle); // Used on module_comments.tpl

if (isset($category_name) && !empty($category_name)) {
        $category = $ccm->get_id($category_name);
} elseif (isset($_REQUEST["action"]) && ( $_REQUEST["action"]!="vote" && $_REQUEST["action"]!="get_plus")) {
    Application::forward301('/');
}

// Redirect if no action
if ( empty($action) ) {
    Application::forward301('/404.html');
}
switch ($action) {
    case 'read': {

        // Load config
        $tpl->setConfig('articles');

        // Assign category name
        $tpl->assign('category_name', $category_name);

        // Increment numviews if it's accesible
        //Content::setNumViews($articleID);

        // Get category id correspondence with ws
        $wsActualCategoryId = file_get_contents($wsUrl.'/ws/categories/id/'.$category_name);

        // Fetch information for Advertisements
        include_once 'article_advertisement.php';
        /*
        $ads = json_decode(file_get_contents($wsUrl.'/ws/ads/article/'.$wsActualCategoryId));

        $intersticial = $ads[0];
        $banners = $ads[1];

        //Render ads
        $advertisement = Advertisement::getInstance();
        $advertisement->render($banners, $advertisement, $wsUrl);
        */

        // Render intersticial banner
        if (!empty($intersticial)) {
            $advertisement->render(array($intersticial), $advertisement, $wsUrl);
        }

        $cacheID = $tpl->generateCacheId($category_name, $subcategory_name, $articleID);

        if (($tpl->caching == 0) || !$tpl->isCached('article/article.tpl', $cacheID)) {

            // Fetch and load article information
            $ext = json_decode(file_get_contents($wsUrl.'/ws/articles/'.(int) $articleID));
            $article = new Article();
            $article->load($ext);
            $article->category_name = $category_name;

            if (($article->available==1) && ($article->in_litter==0)
                && ($article->isStarted())
            ) {

                $title = StringUtils::get_title($article->title);
                $printUrl = '/extimprimir/'.$title.'/'.$category_name.'/'.$dirtyID.'.html';
                $sendMailUrl ='/controllers/article.php?action=sendform
                                             &article_id='.$dirtyID.
                                            '&category_name='.$category_name .
                                            '&subcategory_name=' . $subcategory_name;

                $tpl->assign('print_url', $printUrl);
                $tpl->assign('sendform_url', $sendMailUrl);

                $tpl->assign('article', $article);

                // Get associated media code from Web service
                if (isset($article->img2) && ($article->img2 != 0)) {
                    $photoWs = json_decode(file_get_contents($wsUrl.'/ws/images/id/'.$article->img2));
                    $photoInt = new Photo();
                    $photoInt->load($photoWs);
                    $photoInt->media_url = json_decode(file_get_contents($wsUrl.'/ws/instances/mediaurl/'));
                    $tpl->assign('photoInt', $photoInt);
                }

                if (isset($article->fk_video2) && ($article->fk_video2 != 0)) {
                    $videoWs = json_decode(file_get_contents($wsUrl.'/ws/videos/id/'.$article->fk_video2));
                    $videoInt = new Video();
                    $videoInt->load($videoWs);
                    $tpl->assign('videoInt', $videoInt);
                } else {
                    $videoWs = json_decode(file_get_contents($wsUrl.'/ws/videos/category/'.$wsActualCategoryId));
                    $videoInt = new Video();
                    $videoInt->load($videoWs);
                    $tpl->assign('videoInt', $videoInt);
                }

                // Get inner Related contents
                $relatedContentsWs = json_decode(file_get_contents(
                    $wsUrl.'/ws/articles/lists/related-inner/'.$articleID
                ));

                $relatedContents = array();
                foreach ($relatedContentsWs as $item) {
                    $getContentUrl = $wsUrl.'/ws/contents/contenttype/'.(int) $item->fk_content_type;
                    $contentType = file_get_contents($getContentUrl);
                    $contentType = str_replace('"', '', $contentType);
                    $content = new $contentType();
                    $content->load($item);
                    // Load category related information
                    $content->category_name  = $category_name;
                    $content->category_title = $content->loadCategoryTitle($content->id);
                    $content->uri = 'ext'.$content->uri;
                    $relatedContents[] = $content;
                }

                $tpl->assign('relationed', $relatedContents);

                // Get Machine suggested contents
                $machineRelated = json_decode(file_get_contents(
                    $wsUrl.'/ws/articles/lists/machine-related/'.$articleID
                ));

                $machineSuggestedContents = array();
                foreach ($machineRelated as $content) {
                    $machineSuggestedContents[] = object_to_array($content);
                }

                $tpl->assign('suggested', $machineSuggestedContents);

            } else {
                Application::forward301('/404.html');
            }

        } // end if $tpl->is_cached

        $tpl->display('article/article.tpl', $cacheID);

    } break;

    case 'vote': {

        $category_name = 'home';
        $subcategory_name = null;

        $ip = $_SERVER['REMOTE_ADDR'];
        $ip_from = $_GET['i'];
        $vote_value = intval($_GET['v']); // 1 A favor o 2 en contra
        $page = (!isset($_GET['p']))? 0: intval($_GET['p']);

        $comment_id = $_GET['a'];

        if ($ip != $ip_from) {
            Application::ajax_out("Error no ip vote!");
        }

        $vote = new Vote($comment_id);
        if (is_null($vote)) {
            Application::ajax_out("Error no  vote value!");
        }
        $update = $vote->update($vote_value, $ip);

        if ($update) {
            $html_out = $vote->render($page, 'result', 1);
        } else {
            $html_out = "Ya ha votado anteriormente este comentario.";
        }

        Application::ajax_out($html_out);
    } break;

    case 'get_plus': {
        if ($_GET["content"]=="Comment") {

            $cm = new ContentManager();
            $articles = $cm->cache->getMostComentedContent('Article', true, $_REQUEST['category'], $_REQUEST['days']);

            //$tpl->
            $html_out = "";
            foreach ($articles as $article) {
                $html_out .= '<div class="CNoticiaMas">';
                $html_out .= '<div class="CContainerIconoTextoNoticiaMas">';
                $html_out .= '<div class="iconoNoticiaMas"></div>';
                $html_out .= '<div class="textoNoticiaMas"><a href="'
                    .$article["uri"].'">'.stripslashes($article["title"]).'</a> ('
                    .$article["num"].' comentarios)</div>';
                $html_out .= '</div>';
                $html_out .= '<div class="fileteNoticiaMas"><img src="'
                    .TEMPLATE_USER_PATH.MEDIA_IMG_DIR
                    .'/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>';
                $html_out .= '</div>';
            }
        } else {

            $cm = new ContentManager();

            $articles_viewed = $cm->cache->getMostViewedContent(
                'Article',
                true,
                $_REQUEST['category'],
                $_REQUEST['author'],
                $_REQUEST['days']
            );

            $html_out = "";
            foreach ($articles_viewed as $article) {
                $html_out .= '<div class="CNoticiaMas">';
                $html_out .= '<div class="CContainerIconoTextoNoticiaMas">';
                $html_out .= '<div class="iconoNoticiaMas"></div>';
                $html_out .= '<div class="textoNoticiaMas"><a href="'
                    .$article->uri.'">'.stripslashes($article->title)
                    .'</a></div>';
                $html_out .= '</div>';
                $html_out .= '<div class="fileteNoticiaMas"><img src="'
                    .TEMPLATE_USER_PATH.MEDIA_IMG_DIR
                    .'/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>';
                $html_out .= '</div>';
            }
        }

        Application::ajax_out($html_out);

    } break;

    case 'print': {

        $cacheID = $tpl->generateCacheId($category_name, $subcategory_name, $articleID);

        if (!$tpl->isCached('article/article_printer.tpl', $cacheID)) {

            // Get cleaned article Id
            $articleID = json_decode(file_get_contents($wsUrl.'/ws/contents/resolve/'.$dirtyID));
            // Get article
            $articleWs  = json_decode(file_get_contents($wsUrl.'/ws/articles/'.$articleID));
            $article = new Article();
            $article->load($articleWs);

            // Breadcrub/Pathway
            $breadcrub   = array();
            $breadcrub[] = array(
                'text' => $ccm->get_title($category_name),
                'link' => '/seccion/' . $category_name . '/'
            );

            // URL impresión

            $title = StringUtils::get_title($article->title);
            $print_url = '/imprimir/' . $title. '/' . $category_name . '/';

            if (!empty($subcategory_name)) {
                $breadcrub[] = array(
                    'text' => $ccm->get_title($subcategory_name),
                    'link' => '/seccion/' . $category_name . '/' . $subcategory_name . '/'
                );

                $print_url .= $subcategory_name . '/';
            }

            $print_url .= $dirtyID . '.html';
            $tpl->assign('print_url', $print_url);

            $cat = $ccm->getByName($category_name);
            if (!is_null($cat) && $cat->inmenu) {
                $tpl->assign('breadcrub', $breadcrub);
            }

            // Inner Photo
            if (isset($article->img2) && ($article->img2 != 0)) {
                $photoWs = json_decode(file_get_contents($wsUrl.'/ws/images/id/'.$article->img2));
                $photoInt = new Photo();
                $photoInt->load($photoWs);
                $tpl->assign('photoInt', $photoInt);
            }

            // Load category related information
            $article->category_name  = $category_name;
            $article->category_title = $article->loadCategoryTitle($article->id);

            $tpl->caching = 0;
            $tpl->assign('article', $article);
        }

        $tpl->display('article/article_printer.tpl');
        exit(0);

    } break;


    case 'sendform': {
        require_once 'session_bootstrap.php';
        $token = $_SESSION['sendformtoken'] = md5(uniqid('sendform'));

        $article = new Article($_REQUEST['article_id']);
        $tpl->assign('article', $article);
        $tpl->assign('article_id', $dirtyID);

        $tpl->assign('token', $token);
        $tpl->assign('category_name', $category_name);
        $tpl->assign('subcategory_name', $subcategory_name);

        $tpl->caching = 0;
        $tpl->display('article/article_sendform.tpl'); // Don't disturb cache
        exit(0);
    } break;

    case 'send': {
        require_once 'session_bootstrap.php';

        // Check if magic_quotes is enabled and clear globals arrays
        StringUtils::disabled_magic_quotes();

        // Check direct access
        if ($_SESSION['sendformtoken'] != $_REQUEST['token']) {
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
        $mail->Subject  = $_REQUEST['name_sender']
            .' ha compartido contigo un contenido de '.s::get('site_name');
              //substr(strip_tags($_REQUEST['body']), 0, 100);

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
            $agency = s::get('site_name');
        }
        $tplMail->assign('agency', $agency);

        if (empty($article->summary)) {
            $summary = substr(strip_tags(stripslashes($article->body)), 0, 300)."...";
        } else {
            $summary = stripslashes($article->summary);
        }
        $tplMail->assign('summary', $summary);


        if (method_exists($tpl, '_get_plugin_filepath')) {
            //handle with Smarty version 2
            require_once $tpl->_get_plugin_filepath('function', 'articledate');
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
        $date = smarty_function_articledate($params, $tpl);
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

        if ( $mail->Send() ) {
            $tpl->assign('message', 'Noticia enviada correctamente.');
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                header("HTTP/1.0 404 Not Found");
            }
            $tpl->assign(
                'message',
                'La noticia no pudo ser enviada, inténtelo de nuevo '
                .'más tarde. <br /> Disculpe las molestias.'
            );
        }

        $tpl->caching = 0;
        $tpl->display('article/article_sendform.tpl'); // Don't disturb cache
        exit(0);
    } break;

    default: {
        Application::forward301('index.php');
    } break;
}
