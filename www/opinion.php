<?php
//error_reporting(E_ALL);
require_once('config.inc.php');
require_once('core/application.class.php');

Application::import_libs('*');
$app = Application::load();

// redirect to /mobile/ if it's mobile device request
$app->mobileRouter();

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');

require_once('core/article.class.php');
require_once('core/author.class.php');
require_once('core/advertisement.class.php');
require_once('core/opinion.class.php');
require_once('core/photo.class.php');
require_once('core/comment.class.php');
require_once('core/rating.class.php');
require_once('core/captcha.class.php');

$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('opinion');

/******************** CATEGORIA ********************************************************/


// This page has category name opinion, always. Don't use redirection
$category_name = $_GET['category_name'] = 'opinion';


if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {        
        case 'read': { //Opinion de un autor

            $opinion = new Opinion( $_REQUEST['opinion_id'] );
            $tpl->assign('opinion', $opinion);

            $str = new String_Utils();
            $title = $str->get_title($opinion->title);

            // URL impresión
            $print_url = '/imprimir/' . $title. '/';

            $print_url .= $opinion->pk_content . '.html';
            $tpl->assign('print_url', $print_url);
            $tpl->assign('sendform_url', '/opinion.php?action=sendform&opinion_id=' . $_GET['opinion_id'] );

            /******************************  CATEGORIES & SUBCATEGORIES  *********************************/
            $ccm = new ContentCategoryManager();
            require_once ("index_sections.php");
            /******************************  CATEGORIES & SUBCATEGORIES  *********************************/
            
            //$opinion->set_numviews($_REQUEST['opinion_id']);
            Content::set_numviews($_GET['opinion_id']);                        
            
            if(($opinion->available==1) and ($opinion->in_litter==0 )){
                
                $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, $_GET['opinion_id']);
                
                // MUTEXT CODE
                // Application::getMutex($cache_id);
                
                if( ($tpl->caching == 0) || !$tpl->is_cached('opinion.tpl', $cache_id) ) {
                //    $aut = new Author($opinion->fk_author);
                   
               

                    $cm = new ContentManager();
                    $other_opinions = $cm->cache->find('Opinion', 'opinions.fk_author='.($opinion->fk_author).' and  `pk_opinion` <>' .
                                                $_REQUEST['opinion_id'].' and available=1  and content_status=1','ORDER BY created DESC  LIMIT 0,9');
                    $tpl->assign('other_opinions', $other_opinions);
                    
                    $tpl->assign('author_id', $opinion->fk_author);
                    $tpl->assign('author_name',$opinion->author);
                    
                    $articles_viewed = $cm->cache->getMostViewedContent('Opinion',true);
                    $tpl->assign('articles_viewed', $articles_viewed);

                    //Getting descriptcion and metadata from the article

                    //?????? new Opinion();
                 //   $contentOpinion = $cm->find('Opinion' , 'pk_content="'.$_REQUEST['opinion_id'].'" ' , 'ORDER BY position DESC');
                  //  $tpl->assign('contentOpinion', $contentOpinion[0]);
                    
                    //Rating
                    $rating = new Rating($_REQUEST['opinion_id']);
                    $tpl->assign('rating_bar', $rating->render('article','vote'));

                    $com = new Comment();
                    $comts = $com->get_public_comments($_REQUEST['opinion_id']);
                    $tpl->assign('num_comments', count($comts));

                    //List of authors in Right column
                    $aut = new Author();
                    $todos = $aut->cache->all_authors(NULL,'ORDER BY name');
                    $tpl->assign('todos', $todos);
                    //  $todos_pag=$cm->paginate_num($todos,9);
                    // $tpl->assign('todos_pag', $todos_pag);
                  //  $todos = $cm->paginate_num_js($todos, 9, 3, 'get_paginate_authors','NULL');
                    $tpl->assign('list_view', 'max');

                    $tpl->assign('todos_pag', $todos);
                   /* $tpl->assign('pag_authores', $cm->pager);
                    //	 $tpl->assign('todos', $todos);
                   
                    $pages = $cm->pager;
                    $pages_authors = $pages->_totalPages;
                    $tpl->assign('pages_authors', $pages_authors);                                        

                    */ 
                        
                    }
                    
                
                // END MUTEXT
                // Application::releaseMutex();
                
                // advertisement opinion NOCACHE
                require_once('opinion_advertisement.php');
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
            $opinion = new Opinion($_REQUEST['opinion_id']);
            
            $author = new Author($opinion->fk_author);
            $tpl->assign('author', $author->name);
            
            $tpl->assign('opinion', $opinion);
            
            $tpl->caching = 0;
            $tpl->display('opinion_printer.tpl');
            exit(0);
        } break;


        case 'sendform': {
            require_once('session_bootstrap.php');
            $token = $_SESSION['sendformtoken'] = md5(uniqid('sendform'));

            $opinion = new Opinion($_REQUEST['opinion_id']);
            $tpl->assign('opinion', $opinion);

            $tpl->assign('token', $token);

            $tpl->caching = 0;
            $tpl->display('opinion_sendform.tpl'); // Don't disturb cache
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
            $opinion = new Opinion($_REQUEST['opinion_id']);

            // Filter tags before send
            $permalink = preg_replace('@([^:])//@', '\1/', SITE_URL .  $opinion->permalink);
            $message = stripslashes($_REQUEST['body']);

            $aut = new Author($opinion->fk_author);

            //$aut->name
            $summary = substr(strip_tags(stripslashes($opinion->body)), 0, 300)."...";

            require_once $tpl->_get_plugin_filepath('function', 'articledate');
            $params['created'] = $opinion->created;
            $params['updated'] = $opinion->updated;
            $params['article'] = $opinion;
            $date = smarty_function_articledate($params,$tpl);
            $mail->Body = strip_tags('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">').
                ('<html xmlns="http://www.w3.org/1999/xhtml">').
                ('<head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /><meta name="language" content="es" />').
                ('<div style="font-size: 11px; font-family: Arial;"><table border="0" cellpadding="0" cellspacing="0" width="765"><tbody>').
                ('<tr><td bgcolor="014687"><a href="http://www.xornal.com/" border="0" target="_blank"><img src="http://www.xornal.com/themes/xornal/images/xornal-logo.jpg" alt="Xornal.Com" border="0"></a><br></td></tr>').
                ('<tr><td><div style="margin: 0px 0px 4px; padding-top: 10px; color:#014687; font-size: 18px; font-weight: bold; font-family: Arial;">ARTÍCULO RECOMENDADO<div></div></div></td></tr>').
                ('<tr><td><b>Hola '.$_REQUEST['destination'].',</b><br>').
                ('<b>'.$mail->FromName.' quiere compartir contigo la siguiente información: </b><br><br> '.$message.'<br><br></td></tr>').
                ('<tr><td><img src="http://webdev-xornal.openhost.es/themes/xornal/images/fileteFondoNota.gif" height="1" width="1"></td></tr>').
                ('<tr><td><div style="margin: 0px 0px 0px; padding: 0px; font-family: Arial; font-size:26px; color:#333333; font-weight: normal;  border-top: 1px solid #014687;">').
                ('<b>'.stripslashes($opinion->title).'</b></div><br><div style="margin: 0px 0px 0px; padding: 0px; color:#014687; font-size: 11px; font-weight: bold; text-align: left;">').
                ('<b>'.$aut->name.'</b> | '.$date.'</div></td></tr>').
                ('<tr><td><div style="margin: 0px; color:#333333; font-size: 12px; line-height: 15px; border-bottom: 1px solid #014687; padding-bottom: 5px; ">').
                ($summary.'</div></td></tr>').
                ('<tr><td><div style="color:#014687; text-align:right; text-decoration: underline;  font-size: 12px; line-height: 15px;">').
                ('<a href="'.$permalink.'" target="_blank">Ir al artículo completo</a></div>').
                ('</td></tr></tbody></table></div></body></html>');

            $mail->AltBody = strip_tags($message) . "\n" . $permalink;
            $mail->AddAddress( $_REQUEST['destination'] );

            if( $mail->Send() ) {
                $tpl->assign('message', 'Opinión enviada correctamente.');
            } else {
                $tpl->assign('message', 'El artículo de opinión no pudo ser enviado, inténtelo de nuevo más tarde. <br /> Disculpe las molestias.');
            }
            
            $tpl->caching = 0;
            $tpl->display('opinion_sendform.tpl'); // Don't disturb cache
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
                $html_out .= '<div class="fileteNoticiaMas"><img src="'.TEMPLATE_USER_PATH_WEB.MEDIA_IMG_DIR.'/noticiasRecomendadas/fileteRecomendacion.gif" alt=""/></div>';
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

$tpl->assign('action', $_REQUEST['action']);

/**********************************  CONECTA COLUMN3  ******************************************/
 require_once("conecta_cuadro.php");
/**********************************  CONECTA COLUMN3  ******************************************/
$tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);

// Show in Frontpage
$tpl->display('opinion.tpl', $cache_id);

