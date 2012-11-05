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

// Setup view
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('opinion');

// Setup Content Manager
$cm = new ContentManager();

// Fetch HTTP variables
$category_name    = $request->query->filter('category_name', 'extopinion', FILTER_SANITIZE_STRING);
$subcategory_name = $request->query->filter('subcategory_name', null, FILTER_SANITIZE_STRING);
$action           = $request->query->filter('action', null, FILTER_SANITIZE_STRING);

$dirtyID          = $request->query->filter('opinion_id', '', FILTER_SANITIZE_STRING);
$slug             = $request->query->filter('opinion_title', '', FILTER_SANITIZE_STRING);
$author_name      = $request->query->filter('author_name', '', FILTER_SANITIZE_STRING);

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
if (empty($action)) {
    Application::forward301('/404.html');
}
switch ($action) {
    case 'read':

        $cache_id = $tpl->generateCacheId('sync'.$category_name, $subcategory_name, $dirtyID);

        if (($tpl->caching == 0) || !$tpl->isCached('opinion.tpl', $cache_id)) {

            // Get full opinion
            $opinion = $cm->getUrlContent($wsUrl.'/ws/opinions/complete/'.$dirtyID, true);
            $opinion = unserialize($opinion);

            //Fetch information for Advertisements
            require_once 'opinion_inner_advertisement.php';

            if (($opinion->available==1) && ($opinion->in_litter == 0)) {

                // Please SACAR esta broza de aqui {
                $str = new StringUtils();
                $title = $str->get_title($opinion->title);
                $print_url = '/extimprimir/'.$title.'/'.$dirtyID.'.html';
                $tpl->assign('print_url', $print_url);
                $tpl->assign(
                    'sendform_url',
                    '/controllers/opinion_inner.php?action=sendform&opinion_id=' . $dirtyID
                );
                // } Sacar broza

                $tpl->assign(
                    array(
                        'other_opinions'  => $opinion->otherOpinions,
                        'suggested'       => $opinion->machineRelated,
                        'opinion'         => $opinion,
                        'actual_category' => 'opinion',
                        'media_url'       => $opinion->externalMediaUrl,
                        'contentId'       => $opinion->id, // Used on module_comments.tpl
                        'ext'             => 1 //Used on widget_opinions_authors
                    )
                );

            } else {
                Application::forward301('/404.html');
            }
        }

        // Show in Frontpage
        $tpl->display('opinion/opinion.tpl', $cache_id);

        break;
    case 'captcha':

        $width  = isset($_GET['width']) ? $_GET['width'] : '176';
        $height = isset($_GET['height']) ? $_GET['height'] : '49';
        $characters = isset($_GET['characters']) && $_GET['characters'] > 1 ? $_GET['characters'] : '5';
        $captcha    = new CaptchaSecurityImages(
            $width,
            $height,
            $characters,
            realpath(dirname(__FILE__).'/media/fonts/monofont.ttf')
        );
        exit(0);

        break;
    case 'print':

        $opinion = new Opinion($dirtyID);
        $opinion->category_name = 'opinion';
        $opinion->author_name_slug = StringUtils::get_title($opinion->name);

        $author = new Author($opinion->fk_author);

        $tpl->assign('author', $author->name);

        $tpl->assign('opinion', $opinion);

        $tpl->caching = 0;
        $tpl->display('opinion/opinion_printer.tpl');
        exit(0);

        break;
    case 'sendform':

        require_once 'session_bootstrap.php';
        $token = $_SESSION['sendformtoken'] = md5(uniqid('sendform'));

        $opinion = new Opinion($opinionID);
        $tpl->assign('opinion', $opinion);

        $tpl->assign('token', $token);

        $tpl->caching = 0;
        $tpl->display('opinion/partials/_opinion_sendform.tpl'); // Don't disturb cache
        exit(0);

        break;
    case 'send':

        require_once 'session_bootstrap.php';

        // Check if magic_quotes is enabled and clear globals arrays
        StringUtils::disabled_magic_quotes();

        // Check direct access
        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);
        if ($_SESSION['sendformtoken'] != $token) {
            Application::forward('/');
        }

        // Send opinion to friend
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

        $mail->From     = $request->query->filter('sender', null, FILTER_SANITIZE_STRING);
        $mail->FromName = $request->query->filter('name_sender', null, FILTER_SANITIZE_STRING);
        $mail->Subject  = $request->query->filter('name_sender', null, FILTER_SANITIZE_STRING).
                ' ha compartido contigo un contenido de '.s::get('site_name');
                //substr(strip_tags($_REQUEST['body']), 0, 100);

        $tplMail->assign('destination', 'amig@,');

        // Load permalink to embed into content
        $opinion = new Opinion($dirtyID);
        $opinion->author_name_slug = StringUtils::get_title($opinion->name);
        $opinionType = '';
        if ($opinion->type_opinion == 1) {
            $opinionType = 'editorial';
        } elseif ($opinion->type_opinion == 2) {
            $opinionType = 'director';
        } else {
            $opinionType = $opinion->author_name_slug;
        }
        $tplMail->assign('opinionType', $opinionType);
        $tplMail->assign('mail', $mail);
        $tplMail->assign('opinion', $opinion);

        // Filter tags before send
        $message = $request->query->filter('body', null, FILTER_SANITIZE_STRING);
        $tplMail->assign('body', $message);
        $agency = s::get('site_name');
        $tplMail->assign('agency', $agency);
        $summary = substr(strip_tags(stripslashes($opinion->body)), 0, 300)."...";
        $tplMail->assign('summary', $summary);
        $tplMail->assign('author', $opinion->author);

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
        $params['created'] = $opinion->created;
        $params['updated'] = $opinion->updated;
        $params['opinion'] = $opinion;
        $date = smarty_function_articledate($params, $tpl);
        $tplMail->assign('date', $date);

        $tplMail->caching = 0;
        $mail->Body = $tplMail->fetch('opinion/email_send_to_friend.tpl');

        $mail->AltBody = $tplMail->fetch('opinion/email_send_to_friend_just_text.tpl');

        /*
            * Implementacion para enviar a multiples destinatarios separados por coma
            */
        $destinatarios = explode(',', $request->query->filter('destination', null, FILTER_SANITIZE_STRING));

        foreach ($destinatarios as $dest) {
            //$mail->AddAddress(trim($dest));
            $mail->AddBCC(trim($dest));
        }

        if ($mail->Send()) {
            $tpl->assign('message', 'Opinión enviada correctamente.');
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
                && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
            ) {
                header("HTTP/1.0 404 Not Found");
            }

            $tpl->assign(
                'message',
                'El artículo de opinión no pudo ser enviado, inténtelo de '
                .'nuevo más tarde. <br /> Disculpe las molestias.'
            );
        }

        $tpl->caching = 0;
        $tpl->display('opinion/partials/_opinion_sendform.tpl'); // Don't disturb cache
        exit(0);

        break;
    case 'get_plus':

        $cm = new ContentManager();
        $articles_viewed = $cm->cache->getMostViewedContent(
            $_REQUEST['content'],
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
                .$article->permalink.'">'.stripslashes($article->title).'</a></div>';
            $html_out .= '</div>';
            $html_out .= '<div class="fileteNoticiaMas"><img src="'
                .TEMPLATE_USER_PATH.MEDIA_IMG_DIR.'/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>';
            $html_out .= '</div>';
        }

        Application::ajax_out($html_out);

        break;
    default:
        Application::forward301('index.php');
        break;
}

