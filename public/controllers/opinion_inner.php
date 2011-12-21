<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');
use Onm\Settings as s;
/**
 * Redirect Mobile browsers to mobile site unless a cookie exists.
*/
$app->mobileRouter();

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('opinion');
$cm = new ContentManager();

/**
 * Fetch HTTP variables
*/
$category_name = $_GET['category_name'] = 'opinion';

$dirtyID = filter_input(INPUT_GET,'opinion_id',FILTER_SANITIZE_STRING);

if(empty($dirtyID)) {
    $dirtyID = filter_input(INPUT_POST,'opinion_id',FILTER_SANITIZE_STRING);
}

$opinionID = Content::resolveID($dirtyID);



$tpl->assign('contentId',$opinionID); // Used on module_comments.tpl


$tpl->assign('action', $_REQUEST['action']);

/**
 * Fetch informatino for uncached sections
*/
require_once ("opinion_inner_advertisement.php");

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'read': { //Opinion de un autor
             /**
             * Redirect to album frontpage if id_album wasn't provided
             */
            if (is_null($opinionID)) { Application::forward301('/opinion/'); }

            $opinion = new Opinion($opinionID );

            Content::setNumViews($opinionID);

            $ccm = ContentCategoryManager::get_instance();
            require_once ("index_sections.php");
            require_once("widget_static_pages.php");

            /**
             * Fetch comments for this opinion
            */
            $com = new Comment();
            $comments = $com->get_public_comments($opinionID);
            $tpl->assign('num_comments', count($comments));

            if(($opinion->available==1) and ($opinion->in_litter==0 )){

                $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, $opinionID);

                if( ($tpl->caching == 0) || !$tpl->isCached('opinion.tpl', $cache_id) ) {

                    // Please SACAR esta broza de aqui {
                    $str = new String_Utils();
                    $title = $str->get_title($opinion->title);
                    $print_url = '/imprimir/' . $title. '/'. $opinion->pk_content . '.html';
                    $tpl->assign('print_url', $print_url);
                    $tpl->assign('sendform_url', '/controllers/opinion_inner.php?action=sendform&opinion_id=' . $dirtyID );
                    // } Sacar broza



                    $opinion->author_name_slug = String_Utils::get_title($opinion->name);


                    // Fetch rating for this opinion
                    $rating = new Rating($opinionID);
                    $tpl->assign('rating_bar', $rating->render('article','vote'));


                    // Fetch suggested contents
                    $objSearch = cSearch::Instance();
                    $suggestedContents =
                        $objSearch->SearchSuggestedContents($opinion->metadata,
                                                            'Opinion',
                                                            " contents.available=1 AND pk_content = pk_fk_content",
                                                            4);

                    $suggestedContents= $cm->getInTime($suggestedContents);
                    $tpl->assign('suggested', $suggestedContents);

                    /**
                     * Fetch the other opinions for this author
                    */
                    if($opinion->type_opinion == 1){
                         $where=' opinions.type_opinion = 1';
                         $opinion->name ='Editorial';
                    }elseif($opinion->type_opinion == 2){
                         $where=' opinions.type_opinion = 2';
                         $opinion->name ='Director';
                    }else{
                        $where=' opinions.fk_author='.($opinion->fk_author);
                    }
                    $otherOpinions = $cm->cache->find( 'Opinion',
                                                        $where
                                                        .' AND `pk_opinion` <>' .$opinionID
                                                        .' AND available = 1  AND content_status=1'
                                                        ,' ORDER BY created DESC '
                                                        .' LIMIT 0,9');
                    foreach($otherOpinions as &$otherOpinion) {
                        $otherOpinion->author_name_slug  = $opinion->author_name_slug;
                    }

                    $tpl->assign('other_opinions', $otherOpinions);
                    $tpl->assign('opinion', $opinion);

                }

                // Show in Frontpage
                $tpl->display('opinion/opinion.tpl', $cache_id);

            } else {
                Application::forward301('/404.html');
            }
        } break;

        case 'captcha': {
            $width  = isset($_GET['width']) ? $_GET['width'] : '176';
            $height = isset($_GET['height']) ? $_GET['height'] : '49';
            $characters = isset($_GET['characters']) && $_GET['characters'] > 1 ? $_GET['characters'] : '5';
            $captcha    = new CaptchaSecurityImages($width, $height, $characters,
                                                    realpath(dirname(__FILE__).'/media/fonts/monofont.ttf') );

            exit(0);
        } break;

        case 'print': {

            // Article
            $opinion = new Opinion($dirtyID);
            $opinion->category_name = 'opinion';
            $opinion->author_name_slug = String_Utils::get_title($opinion->name);

            $author = new Author($opinion->fk_author);

            $tpl->assign('author', $author->name);

            $tpl->assign('opinion', $opinion);

            $tpl->caching = 0;
            $tpl->display('opinion/opinion_printer.tpl');
            exit(0);

        } break;


        case 'sendform': {

            require_once('session_bootstrap.php');
            $token = $_SESSION['sendformtoken'] = md5(uniqid('sendform'));

            $opinion = new Opinion($opinionID);
            $tpl->assign('opinion', $opinion);

            $tpl->assign('token', $token);

            $tpl->caching = 0;
            $tpl->display('opinion/partials/_opinion_sendform.tpl'); // Don't disturb cache
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

            // Send opinion to friend
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
            $mail->Subject  = $_REQUEST['name_sender'].' ha compartido contigo un contenido de '.s::get('site_name');  //substr(strip_tags($_REQUEST['body']), 0, 100);

            $tplMail->assign('destination', 'amig@,');

            // Load permalink to embed into content
            $opinion = new Opinion($dirtyID);
            $opinion->author_name_slug = String_Utils::get_title($opinion->name);
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
            $message = $_REQUEST['body'];
            $tplMail->assign('body', $message);
            $agency = SITE_FULLNAME;
            $tplMail->assign('agency',$agency);
            $summary = substr(strip_tags(stripslashes($opinion->body)), 0, 300)."...";
            $tplMail->assign('summary', $summary);
            $tplMail->assign('author', $opinion->author);

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
            $params['created'] = $opinion->created;
            $params['updated'] = $opinion->updated;
            $params['opinion'] = $opinion;
            $date = smarty_function_articledate($params,$tpl);
            $tplMail->assign('date', $date);

            $tplMail->caching = 0;
            $mail->Body = $tplMail->fetch('opinion/email_send_to_friend.tpl');

            $mail->AltBody = $tplMail->fetch('opinion/email_send_to_friend_just_text.tpl');

            /*
             * Implementacion para enviar a multiples destinatarios separados por coma
             */
            $destinatarios = explode(',', $_REQUEST['destination']);

            foreach ($destinatarios as $dest) {
                //$mail->AddAddress(trim($dest));
                $mail->AddBCC(trim($dest));
            }

            if( $mail->Send() ) {
                $tpl->assign('message', 'Opinión enviada correctamente.');
            } else {
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    header("HTTP/1.0 404 Not Found");
            }
                $tpl->assign('message', 'El artículo de opinión no pudo ser enviado, inténtelo de nuevo más tarde. <br /> Disculpe las molestias.');
            }

            $tpl->caching = 0;
            $tpl->display('opinion/partials/_opinion_sendform.tpl'); // Don't disturb cache
            exit(0);
        } break;

        case 'get_plus': {
            $cm = new ContentManager();
            $articles_viewed = $cm->cache->getMostViewedContent($_REQUEST['content'], true, $_REQUEST['category'], $_REQUEST['author'], $_REQUEST['days']);

            $html_out = "";
            foreach ($articles_viewed as $article) {
                $html_out .= '<div class="CNoticiaMas">';
                $html_out .= '<div class="CContainerIconoTextoNoticiaMas">';
                $html_out .= '<div class="iconoNoticiaMas"></div>';
                $html_out .= '<div class="textoNoticiaMas"><a href="'.$article->permalink.'">'.stripslashes($article->title).'</a></div>';
                $html_out .= '</div>';
                $html_out .= '<div class="fileteNoticiaMas"><img src="'.TEMPLATE_USER_PATH.MEDIA_IMG_DIR.'/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>';
                $html_out .= '</div>';
            }

            Application::ajax_out($html_out);
        } break;

        default: {
            Application::forward301('index.php');
        } break;
    }

} else {
    Application::forward301('index.php');
}
