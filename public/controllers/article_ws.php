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
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$cm  = new ContentManager();
$tpl->setConfig('articles');

/**
 * Getting request params
 **/
$dirtyID          = $request->query->filter('article_id', '', FILTER_SANITIZE_STRING);
$category_name    = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
$subcategory_name = null;
$action           = $request->query->filter('action', '', FILTER_SANITIZE_STRING);

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

// Redirect if no action
if ( empty($action) ) {
    Application::forward301('/404.html');
}

switch ($action) {
    case 'read':

        $cacheID = $tpl->generateCacheId('sync'.$category_name, $subcategory_name, $dirtyID);

        if (($tpl->caching == 0) || !$tpl->isCached('article/article.tpl', $cacheID)) {

            // Fetch information for Advertisements
            include_once 'article_advertisement.php';

            // Get full article
            $article = $cm->getUrlContent($wsUrl.'/ws/articles/complete/'.$dirtyID, true);
            $article = unserialize($article);

            if (($article->available==1) && ($article->in_litter==0)
                && ($article->isStarted())
            ) {

                // Logic for print
                $title = StringUtils::get_title($article->title);
                $printUrl = '/extimprimir/'.$title.'/'.$category_name.'/'.$dirtyID.'.html';
                $sendMailUrl ='/controllers/article.php?action=sendform
                                             &article_id='.$dirtyID.
                                            '&category_name='.$category_name .
                                            '&subcategory_name=' . $subcategory_name;

                $tpl->assign('print_url', $printUrl);
                $tpl->assign('sendform_url', $sendMailUrl);

                // Template vars
                $tpl->assign(
                    array(
                        'article'               => $article,
                        'photoInt'              => $article->photoInt,
                        'videoInt'              => $article->videoInt,
                        'relationed'            => $article->relatedContents,
                        'contentId'             => $article->id,// Used on module_comments.tpl
                        'actual_category_title' => $article->category_title,
                        'suggested'             => $article->suggested,
                        'ext'                   => 1,
                    )
                );

            } else {
                Application::forward301('/404.html');
            }

        } // end if $tpl->is_cached

        // Assign category name
        $tpl->assign('category_name', $category_name);

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

        break;
    case 'print':

        // Get cleaned article Id
        $articleID = $cm->getUrlContent($wsUrl.'/ws/contents/resolve/'.$dirtyID, true);

        $ccm = ContentCategoryManager::get_instance();

        $cacheID = $tpl->generateCacheId($category_name, $subcategory_name, $articleID);

        if (!$tpl->isCached('article/article_printer.tpl', $cacheID)) {
            // Get article
            $articleWs = $cm->getUrlContent($wsUrl.'/ws/articles/'.$articleID, true);
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
                $photoWs = $cm->getUrlContent($wsUrl.'/ws/images/id/'.$article->img2, true);
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

        break;
    case 'sendform':

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

        break;
    case 'send':

        require_once 'session_bootstrap.php';

        // Check if magic_quotes is enabled and clear globals arrays
        StringUtils::disabled_magic_quotes();

        // Check direct access
        if ($_SESSION['sendformtoken'] != $_REQUEST['token']) {
            Application::forward('/');
        }

        // Send article to friend
        require_once SITE_VENDOR_PATH."/phpmailer/class.phpmailer.php";

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

        break;
    default:
        Application::forward301('index.php');
        break;
}

