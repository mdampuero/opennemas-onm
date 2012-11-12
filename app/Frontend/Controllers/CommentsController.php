<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Backend_Controllers
 **/
class CommentsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
    }

    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function getAction(Request $request)
    {
        $contentID = $request->query->filter('content_id', null, FILTER_SANITIZE_NUMBER_INT);

        if (\Content::checkExists($contentID)) {
            // Getting comments for current article
            $comment = new \Comment();
            $comments = $comment->get_public_comments($contentID);

            $output = $this->renderView(
                'comments/comments.tpl',
                array(
                    'num_comments' => count($comments),
                    'comments'     => $comments,
                    'contentId'    => $contentID,
                    'content'      => $contentID,
                )
            );

            $response = new Response($output, 200);
        } else {
            $response = new Response(_('Content not available'), 404);
        }
        return $response;
    }

    /**
     * Votes a comment given the punctuation and comment id
     *
     * @return Response the response object
     **/
    public function voteAction(Request $request)
    {
        // Retrieve request data
        $ip         = $request->server->filter('REMOTE_ADDR', null, FILTER_SANITIZE_STRING);
        $ipFrom     = $request->query->filter('i', null, FILTER_SANITIZE_STRING);
        $vote_value = $request->query->getDigits('v', null); // 1 A favor o 2 en contra
        $page       = $request->query->getDigits('p', 0);
        $commentId  = $request->query->getDigits('a', 0);

        if ($ip != $ipFrom) {
            return new Response(_("Error no ip vote!"), 400);
        }

        $vote = new \Vote($commentId);
        if (is_null($vote)) {
            return new Response(_("Error no  vote value!", 400));
        }
        $update = $vote->update($vote_value, $ip);

        if ($update) {
            $response = new Response($vote->render($page, 'result', 1), 200);
            $response->headers->setCookie(new Cookie('vote'.$commentId, true));
        } else {
            $response = new Response(_("Ya ha votado anteriormente este comentario."), 400);
        }

        return $response;
    }

    /**
     * Shows the comments lists given a page number
     *
     * @return Response the response object
     **/
    public function paginateCommentsAction(Request $request)
    {
        // Retrieve all the comments for a given content id
        $contentId = $request->query->getDigits('id', null);
        $comment = new \Comment();
        $comments = $comment->get_public_comments($contentId);

        $cm = new \ContentManager();
        $comments = $cm->paginate_num_js($comments, 9, 1, 'get_paginate_comments', "'".$contentId."'");

        $caching = $this->view->caching;
        $this->view->caching = 0;
        $output = $this->renderView(
            'internal_widgets/module_print_comments.tpl',
            array(
                'num_comments_total' => count($comments),
                'paginacion'         => $cm->pager,
                'comments'           => $comments
                )
        );

        $this->view->caching = $caching;
        return new Response($output);
    }

    /**
     * TODO: not finished
     * Saves a content given its information and the content to relate to
     *
     * @return Response the response object
     **/
    public function saveAction(Request $request)
    {
        $text     = $request->request->filter('textareacomentario', '', FILTER_SANITIZE_STRING);
        $secCode  = $request->request->filter('security_code', '', FILTER_SANITIZE_STRING);
        $author   = $request->request->filter('nombre', '', FILTER_SANITIZE_STRING);
        $title    = $request->request->filter('title', '', FILTER_SANITIZE_STRING);
        $category = $request->request->filter('category', '', FILTER_SANITIZE_STRING);
        $email    = $request->request->filter('email', '', FILTER_SANITIZE_STRING);
        $id       = $request->request->filter('id', '', FILTER_SANITIZE_STRING);

        var_dump($text, $secCode, $author, $title, $category, $email, $id);die();


        if (!empty($text)) {
            if (empty($secCode) ) {
                // Normal comment
                $data = array(
                    'body'     => $text,
                    'author'   => $author,
                    'title'    => $title,
                    'category' => $category,
                    'email'    => $email,
                );
            } else {
                // Facebook comment
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

            $sessionBeforeComment = $_SESSION;

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
                $ip = Application::getRealIp();
                $created = $comment->create(
                    array(
                        'id'   => $_POST['id'],
                        'data' => $data,
                        'ip'   => $ip
                    )
                );
                if ($created) {
                    if (preg_match('@territoris@', $_SERVER['SERVER_NAME'])) {
                        $message = "El seu comentari ha estat emmagatzemat i està pendent de publicar-se.";
                    } else {
                        $message = "Su comentario ha sido guardado y está pendiente de publicación.";
                    }
                }
            }

        } else {
            if (preg_match('@territoris@', $_SERVER['SERVER_NAME'])) {
                $message = "El seu comentari no ha estat guardat.\n"
                    ."Assegureu-vos emplenar correctament tots els camps";
            } else {
                $message = "Su comentario no ha sido guardado.\n"
                    ."Asegúrese de cumplimentar correctamente todos los campos.";
            }
        }
        $response = new Response($message, 200);
        $response->send();

        $_SESSION = $sessionBeforeComment;
    }

} // END class CommentsController