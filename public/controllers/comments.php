<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

use Symfony\Component\HttpFoundation\Response;

$tpl = new Template(TEMPLATE_USER);

if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {

        case 'get':

            // asdf
            //
            $contentID = $request->query->filter('content_id', null, FILTER_SANITIZE_NUMBER_INT);

            $content = new Content($contentID);

            if (!is_null($content->id)) {
                // Getting comments for current article
                $comment = new Comment();
                $comments = $comment->get_public_comments($contentID);
                $tpl->assign(array(
                    'num_comments' => count($comments),
                    'comments'     => $comments,
                    'contentId'    => $contentID,
                    'content'      => $contentID,
                ));

                $output = $tpl->fetch('comments/comments.tpl');

                Application::ajax_out($output);
            } else {
                $response = new Response('Content not available', 404, array('content-type' => 'text/html'));
                $response->send();
            }

            break;

        case 'vote': {

            $category_name = 'home';
            $subcategory_name = null;

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

        case 'paginate_comments':

            $comment = new Comment();
            $comments = $comment->get_public_comments($_REQUEST['id']);

            $tpl->assign('num_comments_total', count($comments));
            //  if(count($comments) >0) {
            $cm = new ContentManager();
            $comments = $cm->paginate_num_js($comments, 9, 1, 'get_paginate_comments',"'".$_REQUEST['id']."'");

            $tpl->assign('paginacion', $cm->pager);
            $tpl->assign('comments', $comments);

            $caching = $tpl->caching;
            $tpl->caching = 0;
            $output = $tpl->fetch('internal_widgets/module_print_comments.tpl');
            $tpl->caching = $caching;
            //}
            Application::ajax_out($output);
        break;

        case 'save_comment':

            if(isset($_POST['textareacomentario']) && !empty($_POST['textareacomentario'])) {
                if( isset($_POST['security_code']) && empty($_POST['security_code']) ) {

                    /*  Anonymous comment ************************* */
                    $data = array();
                    $data['body']     = $_POST['textareacomentario'];
                    $data['author']   = $_POST['nombre'];
                    $data['title']    = $_POST['title'];
                    $data['category'] = $_POST['category'];
                    $data['email']    = $_POST['email'];

                    echo saveComment($data);

                } else {

                    /* Check if user is facebook logged **************** */
                    require_once dirname(__FILE__) . '/fb/facebook.php';
                    // require_once dirname(__FILE__) . '/fb/config.php'; // deprecated, see section [Facebook API KEY] in config.inc.php
                    $fb = new Facebook(FB_APP_APIKEY, FB_APP_SECRET);
                    $fb_user = $fb->get_loggedin_user();

                    if($fb_user) {
                        $user_details = $fb->api_client->users_getInfo($fb_user, array('name', 'proxied_email'));

                        $data = array();
                        $data['body']     = $_POST['textareacomentario'];
                        $data['author']   = $user_details[0]['name'];
                        $data['title']    = $_POST['title'];
                        $data['category'] = $_POST['category'];
                        $data['email']    = $user_details[0]['proxied_email'];

                        echo saveComment($data);

                    } else {
                        echo("Su comentario no ha sido guardado.\nAsegúrese de cumplimentar correctamente todos los campos.");
                    }
                }

            } else {
                echo("Su comentario no ha sido guardado.\nAsegúrese de cumplimentar correctamente todos los campos.");
            }

        break;

    }
}

/**
 * Helper function to save comment into Comment
 *
 * @param array $data
 * @return string Message
 */
function saveComment($data)
{
    //Get $_SESSION values for userComment
    $_SESSION['username'] = $data['author'];
    $_SESSION['userid'] = 'comment #'.$_POST['id'];
    $comment = new Comment();

    // Check it's clone article {{{
    $article = new Article($_POST['id']);
    if($article->isClone()) {
        $_POST['id'] = Article::getOriginalPk($_POST['id']);
    }
    // }}}

    // Prevent XSS attack
    $data = array_map('strip_tags', $data);

    if($comment->hasBadWorsComment($data)) {
        return "Su comentario fue rechazado debido al uso de palabras malsonantes.";
    }

    $ip = Application::getRealIP();
    if($comment->create( array( 'id' => $_POST['id'], 'data' => $data, 'ip' => $ip) ) ) {
        return "Su comentario ha sido guardado y está pendiente de publicación.";
    }

    return "Su comentario no ha sido guardado.\nAsegúrese de cumplimentar correctamente todos los campos.";
}
