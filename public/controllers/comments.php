<?php

/**
 * Start up and setup the app
*/
require_once '../bootstrap.php';
require_once './session_bootstrap.php';

use Symfony\Component\HttpFoundation\Response;

$tpl = new Template(TEMPLATE_USER);

$action = $request->query->filter('action', null, FILTER_SANITIZE_STRING);
if (is_null($action)) {
    $action = $request->request('action', null, FILTER_SANITIZE_STRING);
}

switch ($action) {

    case 'get':
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

            $response = new Response($output, 200);
        } else {
            $response = new Response('Content not available', 404, array('content-type' => 'text/html'));
        }
        $response->send();

        break;

    case 'vote':

        $category_name    = 'home';
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
        $update = $vote->update($vote_value,$ip);

        if ($update) {
            $html_out = $vote->render($page,'result',1);
        } else {
            $html_out = "Ya ha votado anteriormente este comentario.";
        }

        Application::ajax_out($html_out);
        break;

    case 'paginate_comments':

        $comment = new Comment();
        $comments = $comment->get_public_comments($_REQUEST['id']);

        $tpl->assign('num_comments_total', count($comments));
        //  if (count($comments) >0) {
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

        $text     = $request->request->filter('textareacomentario', '', FILTER_SANITIZE_STRING);
        $secCode  = $request->request->filter('security_code', '', FILTER_SANITIZE_STRING);
        $author   = $request->request->filter('nombre', '', FILTER_SANITIZE_STRING);
        $title    = $request->request->filter('title', '', FILTER_SANITIZE_STRING);
        $category = $request->request->filter('category', '', FILTER_SANITIZE_STRING);
        $email    = $request->request->filter('email', '', FILTER_SANITIZE_STRING);
        $id       = $request->request->filter('id', '', FILTER_SANITIZE_STRING);

        if (!empty($text)) {
            // Normal comment
            if (empty($secCode) ) {
                $data = array(
                    'body'     => $text,
                    'author'   => $author,
                    'title'    => $title,
                    'category' => $category,
                    'email'    => $email,
                );
            } else { // Facebook comment

                require_once dirname(__FILE__) . '/fb/facebook.php';
                $fb = new Facebook(FB_APP_APIKEY, FB_APP_SECRET);
                $facebookUser = $fb->get_loggedin_user();

                // If user is logged
                if ($facebookUser) {
                    $userInformation = $fb->api_client->users_getInfo($facebookUser, array('name', 'proxied_email'));

                    $data = array(
                        'body'     => $text,
                        'author'   => $userInformation[0]['name'],
                        'title'    => $title,
                        'category' => $category,
                        'email'    => $userInformation[0]['proxied_email'],
                    );

                } else {
                    if (preg_match('@territoris@', $_SERVER['SERVER_NAME'])) {
                        $message = "El seu comentari no ha estat guardat.\nSembla que està no connectat a Facebook.";
                    } else {
                        $message = "Su comentario no ha sido guardado.\nParece que está no conectado a Facebook.";
                    }
                }
            }

            //Get $_SESSION values for userComment
            $_SESSION['username'] = $data['author'];
            $_SESSION['userid'] = 'comment #'.$_POST['id'];
            $comment = new Comment();

            // Prevent XSS attack
            $data = array_map('strip_tags', $data);

            if ($comment->hasBadWorsComment($data)) {
                if (preg_match('@territoris@', $_SERVER['SERVER_NAME'])) {
                    $message = "El seu comentari va ser rebutjat per l'ús de paraules malsonants.";
                } else {
                    $message = "Su comentario fue rechazado debido al uso de palabras malsonantes.";
                }
            } else {
                $ip = Application::getRealIP();
                if ($comment->create( array( 'id' => $_POST['id'], 'data' => $data, 'ip' => $ip) ) ) {
                    if (preg_match('@territoris@', $_SERVER['SERVER_NAME'])) {
                        $message = "El seu comentari ha estat emmagatzemat i està pendent de publicar-se.";
                    } else {
                        $message = "Su comentario ha sido guardado y está pendiente de publicación.";
                    }
                }
            }

        } else {
            if (preg_match('@territoris@', $_SERVER['SERVER_NAME'])) {
                $message = "El seu comentari no ha estat guardat.\nAssegureu-vos emplenar correctament tots els camps";
            } else {
                $message = "Su comentario no ha sido guardado.\nAsegúrese de cumplimentar correctamente todos los campos.";
            }
        }
        $response = new Response($message, 200);
        $response->send();
        break;

}
