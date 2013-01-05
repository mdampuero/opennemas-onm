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
     * Renders
     *
     * @return Response the response object
     **/
    public function getAction(Request $request)
    {
        $contentID   = $request->query->filter('content_id', null, FILTER_SANITIZE_NUMBER_INT);
        $elemsByPage = $request->query->getDigits('elems_per_page', 10);
        $offset      = $request->query->getDigits('offset', 1);
        $darkTheme   = $request->query->getDigits('dark_theme', 0);

        if (!empty($contentID)
            && \Content::checkExists($contentID)
        ) {
            // Getting comments for current article
            list($total, $comments) = \Comment::getPublicCommentsAndTotalCount(
                $contentID,
                $elemsByPage,
                $offset
            );

            $output = $this->renderView(
                'comments/loader.tpl',
                array(
                    'total'          => $total,
                    'comments'       => $comments,
                    'contentId'      => $contentID,
                    'elems_per_page' => $elemsByPage,
                    'offset'         => $offset,
                    'dark_theme'     => $darkTheme,
                )
            );

            $response = new Response($output, 200);
        } else {
            $response = new Response('', 404);
        }
        return $response;
    }

    /**
     * Shows the comments lists given a page number
     *
     * @return Response the response object
     **/
    public function ajaxAction(Request $request)
    {
        $contentID   = $request->query->filter('content_id', null, FILTER_SANITIZE_NUMBER_INT);
        $elemsByPage = $request->query->getDigits('elems_per_page', 10);
        $offset      = $request->query->getDigits('offset', 1);

        if (!empty($contentID)
            && \Content::checkExists($contentID)
        ) {
            // Getting comments for current article
            list($total, $comments) = \Comment::getPublicCommentsAndTotalCount(
                $contentID,
                $elemsByPage,
                $offset
            );

            $contents = $this->renderView(
                'comments/partials/comment_element.tpl',
                array(
                    'total'          => $total,
                    'comments'       => $comments,
                    'contentId'      => $contentID,
                    'elems_per_page' => $elemsByPage,
                    'offset'         => $offset,
                )
            );

            // Inform the client if there is more elements
            $more = true;
            if ($total < ($elemsByPage + ($elemsByPage*$offset))) {
                $more = false;
            }

            $output = array(
                'contents' => $contents,
                'more'     => $more,
            );

            $response = new Response(json_encode($output), 200);
        } else {
            $response = new Response('', 404);
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
        $voteValue = $request->request->filter('vote', null, FILTER_SANITIZE_STRING);
        $commentId = $request->request->getDigits('comment_id', 0);
        $ip        = $request->getClientIp();
        $cookie    = $request->cookies->get('comment-vote-'.$commentId);

        // User already voted this comment
        if (!is_null($cookie)) {
            return new Response(_('Already voted.'), 400);
        }

        // Reject the request is not sent by POST
        if ('POST' !== $request->getMethod()) {
            return new Response(_('Not valid request method'), 400);
        }

        // Reject the vote action if the vote value is not valid
        if (!in_array($voteValue, array('up', 'down'))) {
            return new Response(_('Not valid vote value'), 400);
        }

        // 1 A favor o 2 en contra
        if ($voteValue == 'up') {
            $voteValue = 1;
        } else {
            $voteValue = 2;
        }

        // Create the vote
        $vote = new \Vote($commentId);
        if (is_null($vote)) {
            return new Response(_("Error no vote value!", 400));
        }
        $update = $vote->update($voteValue, $ip);

        if ($update) {
            $response = new Response('ok', 200);
            $response->headers->setCookie(
                new Cookie('comment-vote-'.$commentId, true, new \DateTime('+3 days'))
            );
        } else {
            $response = new Response(_("You have voted this comment previously."), 400);
        }

        return $response;
    }

    /**
     * TODO: not finished
     * Saves a content given its information and the content to relate to
     *
     * @return Response the response object
     **/
    public function saveAction(Request $request)
    {
        $body        = $request->request->filter('body', '', FILTER_SANITIZE_STRING);
        $authorName  = $request->request->filter('author-name', '', FILTER_SANITIZE_STRING);
        $authorEmail = $request->request->filter('author-email', '', FILTER_SANITIZE_STRING);
        $contentId   = $request->request->getDigits('content-id');
        $ip          = $request->getClientIp();

        $session = $this->get('session')->start();

        $httpCode = 200;

        $sessionBeforeComment = $_SESSION;
        if (!empty($body) && !empty($authorName) && !empty($authorEmail) && !empty($contentId)) {
            $data = array(
                'body'     => $body,
                'author'   => $authorName,
                'email'    => $authorEmail,
            );


            // Get $_SESSION values for userComment
            $_SESSION['username'] = $data['author'];
            $_SESSION['userid'] = 'on-content#'.$contentId;

            // Prevent XSS attack
            $data = array_map('strip_tags', $data);

            $comment = new \Comment();
            if ($comment->hasBadWordsComment($data)) {
                $message = _('Your comment was rejected due insults usage.');
                $httpCode = 400;
            } else {
                $created = $comment->create(
                    array(
                        'id'   => $contentId,
                        'data' => $data,
                        'ip'   => $ip
                    )
                );
                if ($created) {
                    $message = _('Your comment was accepted and now we have to moderate it.');
                }
            }

        } else {
            $message = _('Ensure you have completed all the form fields.');
            $httpCode = 400;
        }
        $_SESSION = $sessionBeforeComment;

        return  new Response($message, $httpCode);
    }
}
