<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
// Start and setup the app
require_once '../bootstrap.php';
use Onm\Settings as s;

// Redirect Mobile browsers to mobile site unless a cookie exists.
// $app->mobileRouter();

// Setup view
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('opinion');

// Setup Content Manager
$cm = new ContentManager();

// Fetch HTTP variables
$category_name    = $request->query->filter('category_name', 'opinion', FILTER_SANITIZE_STRING);
$subcategory_name = $request->query->filter('subcategory_name', '', FILTER_SANITIZE_STRING);
$action           = $request->query->filter('action', null, FILTER_SANITIZE_STRING);

$dirtyID     = $request->query->filter('opinion_id', '', FILTER_SANITIZE_STRING);
$slug        = $request->query->filter('opinion_title', '', FILTER_SANITIZE_STRING);
$author_name = $request->query->filter('author_name', '', FILTER_SANITIZE_STRING);
$opinionID   = Content::resolveID($dirtyID);

$tpl->assign(
    array(
        'contentId' => $opinionID,
        'action'    => $action
    )
); // Used on module_comments.tpl

// Fetch information for uncached sections
require_once 'opinion_inner_advertisement.php';

switch ($action) {
    case 'read': //Opinion de un autor
        // Redirect to album frontpage if id_album wasn't provided
        if (is_null($opinionID)) {
            Application::forward301('/opinion/');
        }

        $opinion = new Opinion($opinionID);

        Content::setNumViews($opinionID);

        // Fetch comments for this opinion
        $com = new Comment();
        $comments = $com->get_public_comments($opinionID);
        $tpl->assign('num_comments', count($comments));

        $cacheID = $tpl->generateCacheId($category_name, $subcategory_name, $opinionID);

        if (($opinion->available == 1) && ($opinion->in_litter == 0)) {

            if (($tpl->caching == 0) || !$tpl->isCached('opinion.tpl', $cacheID) ) {

                $author = new \Author($opinion->fk_author);
                $author->get_author_photos();
                $opinion->author = $author;

                // Please SACAR esta broza de aqui {
                $title = \StringUtils::get_title($opinion->title);
                $print_url = '/imprimir/' . $title. '/'. $opinion->pk_content . '.html';
                $tpl->assign('print_url', $print_url);
                $tpl->assign(
                    'sendform_url',
                    '/controllers/opinion_inner.php?action=sendform&opinion_id=' . $dirtyID
                );
                // } Sacar broza
                /*
                $opinion->author_name_slug = StringUtils::get_title($opinion->name);
                //Check slug
                if (empty($slug) || ($opinion->slug != $slug)
                    || ($opinion->author_name_slug != $author_name)) {
                    Application::forward301(SITE_URL.$opinion->uri);
                }
                */


                // Fetch rating for this opinion
                $rating = new Rating($opinionID);
                $tpl->assign('rating_bar', $rating->render('article', 'vote'));

                // Fetch suggested contents
                $objSearch = cSearch::getInstance();
                $suggestedContents = $objSearch->searchSuggestedContents(
                    $opinion->metadata,
                    'Opinion',
                    " contents.available=1 AND pk_content = pk_fk_content",
                    4
                );

                // Get author slug for suggested opinions
                foreach ($suggestedContents as &$suggest) {
                    $element = new Opinion($suggest['pk_content']);
                    if (!empty($element->author)) {
                        $suggest['author_name'] = $element->author;
                        $suggest['author_name_slug'] = StringUtils::get_title($element->author);
                    } else {
                        $suggest['author_name_slug'] = "author";
                    }
                }

                $suggestedContents= $cm->getInTime($suggestedContents);
                $tpl->assign('suggested', $suggestedContents);

                // Fetch the other opinions for this author
                if ($opinion->type_opinion == 1) {
                    $where=' opinions.type_opinion = 1';
                    $opinion->name = 'Editorial';
                } elseif ($opinion->type_opinion == 2) {
                    $where=' opinions.type_opinion = 2';
                    $opinion->name = 'Director';
                } else {
                    $where=' opinions.fk_author='.($opinion->fk_author);
                }

                $otherOpinions = $cm->cache->find(
                    'Opinion',
                    $where.' AND `pk_opinion` <>' .$opinionID
                    .' AND available = 1  AND content_status=1',
                    ' ORDER BY created DESC LIMIT 0,9'
                );


                $author = new \Author($opinion->fk_author);
                $author->get_author_photos();

                foreach ($otherOpinions as &$otOpinion) {
                    $otOpinion->author = $author;
                    $otOpinion->author_name_slug  = $opinion->author_name_slug;
                }

                $tpl->assign(
                    array(
                        'other_opinions'  => $otherOpinions,
                        'opinion'         => $opinion,
                        'actual_category' => 'opinion',
                        'author'          => $author,
                    )
                );

            }

            // Show in Frontpage
            $tpl->display('opinion/opinion.tpl', $cacheID);

        } else {
            Application::forward301('/404.html');
        }

        break;
    case 'print':
        // Article
        $opinion = new Opinion($dirtyID);
        $opinion->category_name = 'opinion';
        $opinion->author_name_slug = StringUtils::get_title($opinion->name);

        $author = new Author($opinion->fk_author);
        $author->get_author_photos();
        $opinion->author = $author;

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
        $tpl->display('opinion/partials/_opinion_sendform.tpl');
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

        $mail->Host       = "localhost";
        $mail->Mailer     = "smtp";
        /*$mail->Username = '';
        $mail->Password   = '';*/

        $mail->CharSet    = 'UTF-8';
        $mail->Priority   = 5; // Low priority
        $mail->IsHTML(true);


        $mail->From     =
            $request->query->filter('sender', null, FILTER_SANITIZE_STRING);
        $mail->FromName =
            $request->query->filter('name_sender', null, FILTER_SANITIZE_STRING);
        $mail->Subject = _(
            sprintf(
                '%s ha compartido contigo un contenido de %s',
                $mail->FromName,
                s::get('site_name')
            )
        );

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
        $agency  = s::get('site_name');
        $tplMail->assign('agency', $agency);
        $summary = substr(strip_tags(stripslashes($opinion->body)), 0, 300)."...";
        $tplMail->assign('summary', $summary);
        $tplMail->assign('author', $opinion->author);

        foreach ($tpl->plugins_dir as $value) {
            $filepath = $value ."/function.articledate.php";
            if (file_exists($filepath)) {
                require_once $filepath;
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

        $mail->AltBody =
            $tplMail->fetch('opinion/email_send_to_friend_just_text.tpl');

        /*
            * Implementacion para enviar a multiples destinatarios separados por coma
            */
        $destinatarios = explode(
            ',',
            $request->query->filter('destination', null, FILTER_SANITIZE_STRING)
        );

        foreach ($destinatarios as $dest) {
            //$mail->AddAddress(trim($dest));
            $mail->AddBCC(trim($dest));
        }

        if ( $mail->Send() ) {
            $tpl->assign('message', 'Correo enviado correctamente.');
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
        $tpl->display('opinion/partials/_opinion_sendform.tpl');
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

        $output = "";
        foreach ($articles_viewed as $article) {
            $output .= '<div class="CNoticiaMas">'
                . '<div class="CContainerIconoTextoNoticiaMas">'
                . '<div class="iconoNoticiaMas"></div>'
                . '<div class="textoNoticiaMas"><a href="'.$article->permalink
                . '">'.stripslashes($article->title).'</a></div>'
                . '</div>'
                . '<div class="fileteNoticiaMas"><img src="'
                . TEMPLATE_USER_PATH.MEDIA_IMG_DIR
                . '/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>'
                . '</div>';
        }

        Application::ajaxOut($output);

        break;
    default:
        Application::forward301('index.php');
        break;
}

