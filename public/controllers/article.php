<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
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
$articleID        = Content::resolveID($dirtyID);

$tpl->assign('contentId', $articleID); // Used on module_comments.tpl

$category_name    = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
$subcategory_name = null;
$action           = $request->query->filter('action', '', FILTER_SANITIZE_STRING);

if (isset($category_name) && !empty($category_name)) {
    $category = $ccm->get_id($category_name);
} elseif (isset($_REQUEST["action"])
    && ( $_REQUEST["action"]!="vote" && $_REQUEST["action"]!="get_plus")
) {
    Application::forward301('/');
}

switch ($action) {
    case 'read':

        if (empty($articleID) ) {
            Application::forward301('/404.html');
        }
        // Increment numviews if it's accesible
        Content::setNumViews($articleID);

        // Load config
        $tpl->setConfig('articles');

        $tpl->assign('category_name', $category_name);

        $cm = new ContentManager();

        // Advertisements for single article NO CACHE
        require_once 'article_advertisement.php';

        $cacheID = $tpl->generateCacheId($category_name, $subcategory_name, $articleID);

        if (($tpl->caching == 0) || !$tpl->isCached('article/article.tpl', $cacheID) ) {

            $article = new Article($articleID);

            if (($article->available==1) && ($article->in_litter==0)
                && ($article->isStarted())
            ) {

                // Print url, breadcrumb code ----------------------------------
                // TODO: Seems that this is trash, evaluate its removal

                $title = StringUtils::get_title($article->title);

                $print_url = '/imprimir/' . $title. '/' . $category_name . '/';

                $breadcrub   = array();
                $breadcrub[] = array('text' => $ccm->get_title($category_name),
                                        'link' => '/seccion/' . $category_name . '/' );

                $print_url .= $dirtyID . '.html';
                $tpl->assign('print_url', $print_url);
                $tpl->assign('sendform_url',
                    '/controllers/article.php?action=sendform&article_id='
                    . $_GET['article_id'] . '&category_name=' .
                    $category_name . '&subcategory_name=' . $subcategory_name
                );

                // Check if $section is "in menu" then show breadcrub
                $cat = $ccm->getByName($category_name);
                if (!is_null($cat) && $cat->inmenu) {
                    $tpl->assign('breadcrub', $breadcrub);
                }

                // Categories code -------------------------------------------
                // TODO: Seems that this is trash, evaluate its removal

                $actual_category       =$category_name;
                $actual_category_id    = $ccm->get_id($actual_category);
                $actual_category_title = $ccm->get_title($actual_category);

                $tpl->assign(array(
                    'category_name'         => $actual_category ,
                    'actual_category_title' => $actual_category_title,
                    'actual_category'       => $actual_category,
                    'actual_category_id'    =>$actual_category_id,
                ));

                $tpl->assign('article', $article);

                // Associated media code --------------------------------------
                if (isset($article->img2) && ($article->img2 != 0)) {
                    $photoInt = new Photo($article->img2);
                    $tpl->assign('photoInt', $photoInt);
                }

                if (isset($article->fk_video2)) {
                    $videoInt = new Video($article->fk_video2);
                    $tpl->assign('videoInt', $videoInt);
                } else {
                    $video =  $cm->find_by_category_name('Video',
                        $actual_category,
                        'contents.content_status=1',
                        'ORDER BY created DESC LIMIT 0 , 1');
                    if (isset($video[0])) {
                        $tpl->assign('videoInt', $video[0]);
                    }
                }

                // Related contents code ---------------------------------------
                $relContent      = new RelatedContent();
                $relatedContents = array();

                $relationIDs     = $relContent->cache->get_relations_int($articleID);
                if (count($relationIDs) > 0) {
                    $relatedContents = $cm->cache->getContents($relationIDs);

                    // Drop contents that are not available or not in time
                    $relatedContents = $cm->getInTime($relatedContents);
                    $relatedContents = $cm->cache->getAvailable($relatedContents);


                    // Add category name
                    foreach ($relatedContents as &$content) {
                        $content->category_name =
                            $ccm->get_category_name_by_content_id($content->id);
                    }
                }
                $tpl->assign('relationed', $relatedContents);

                // Machine suggested contents code -----------------------------
                $machineSuggestedContents = array();
                if (!empty($article->metadata)) {
                    $objSearch    = cSearch::getInstance();
                    $machineSuggestedContents =
                        $objSearch->searchSuggestedContents($article->metadata,
                            'Article',
                            "pk_fk_content_category= ".$article->category.
                            " AND contents.available=1 AND pk_content = pk_fk_content",
                            4
                        );
                    $machineSuggestedContents =
                        $cm->getInTime($machineSuggestedContents);
                }
                $tpl->assign('suggested', $machineSuggestedContents);

            } else {
                Application::forward301('/404.html');
            }

        } // end if $tpl->is_cached

        $tpl->display('article/article.tpl', $cacheID);
        break;

    case 'vote':

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
        break;

    case 'get_plus':
        $output = "";
        if ($_GET["content"]=="Comment") {
            $cm = new ContentManager();
            $articles = $cm->cache->getMostComentedContent(
                'Article',
                true,
                $_REQUEST['category'],
                $_REQUEST['days']
            );

            //$tpl->
            foreach ($articles as $article) {
                $output =
                    '<div class="CNoticiaMas">'
                    . '<div class="CContainerIconoTextoNoticiaMas">'
                    . '<div class="iconoNoticiaMas"></div>'
                    . '<div class="textoNoticiaMas"><a href="'
                    . $article["uri"].'">'.stripslashes($article["title"])
                    . '</a> ('.$article["num"].' comentarios)</div>'
                    . '</div>'
                    . '<div class="fileteNoticiaMas"><img src="'
                    . TEMPLATE_USER_PATH.MEDIA_IMG_DIR
                    . '/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>'
                    . '</div>';
            }
        } else {
            $cm = new ContentManager();

            $articles = $cm->cache->getMostViewedContent(
                'Article',
                true,
                $_REQUEST['category'],
                $_REQUEST['author'],
                $_REQUEST['days']
            );

            foreach ($articles as $article) {
                $output = '<div class="CNoticiaMas">'
                    . '<div class="CContainerIconoTextoNoticiaMas">'
                    . '<div class="iconoNoticiaMas"></div>'
                    . '<div class="textoNoticiaMas"><a href="'
                    . $article->uri.'">'.stripslashes($article->title)
                    . '</a></div>'
                    . '</div>'
                    . '<div class="fileteNoticiaMas"><img src="'
                    . TEMPLATE_USER_PATH.MEDIA_IMG_DIR
                    . '/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>'
                    . '</div>';
            }
        }

        Application::ajax_out($output);
        break;

    case 'print':

        $cacheID = $tpl->generateCacheId($category_name, $subcategory_name, $articleID);

        if (!$tpl->isCached('article/article_printer.tpl', $cacheID)) {
            // Article
            $article = new Article($articleID);

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

            // Foto interior
            if (isset($article->img2) and ($article->img2 != 0)) {
                $photoInt = new Photo($article->img2);
                $tpl->assign('photoInt', $photoInt);
            }

            $tpl->caching = 0;
            $tpl->assign('article', $article);
        }

        $tpl->display('article/article_printer.tpl');
        break;


    case 'sendform':
        require_once('session_bootstrap.php');
        $token = $_SESSION['sendformtoken'] = md5(uniqid('sendform'));

        $article = new Article($_REQUEST['article_id']);
        $tpl->assign('article', $article);
        $tpl->assign('article_id', $dirtyID);

        $tpl->assign('token', $token);
        $tpl->assign('category_name', $category_name);
        $tpl->assign('subcategory_name', $subcategory_name);

        $tpl->caching = 0;
        $tpl->display('article/article_sendform.tpl'); // Don't disturb cache
        break;

    case 'send':
        require_once('session_bootstrap.php');

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

        $mail->AltBody =
            $tplMail->fetch('article/email_send_to_friend_just_text.tpl');

        /**
         * Implementacion para enviar a multiples destinatarios
         * separados por coma
         */
        $destinatarios = explode(',', $_REQUEST['destination']);

        foreach ($destinatarios as $dest) {
            //$mail->AddAddress(trim($dest));
            $mail->AddBCC(trim($dest));
        }

        if ( $mail->Send() ) {
            $tpl->assign('message', 'Noticia enviada correctamente.');
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
                && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
            ) {
                header("HTTP/1.0 404 Not Found");
            }
            $tpl->assign('message',
                'La noticia no pudo ser enviada, inténtelo de nuevo más tarde.'
                .'<br /> Disculpe las molestias.'
            );
        }

        $tpl->caching = 0;
        $tpl->display('article/article_sendform.tpl'); // Don't disturb cache
        break;

    default:
        Application::forward301('index.php');
        break;
}
