<?php
/**
 * Defines the frontend controller for the comment content type
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Frontend_Controllers
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for comments
 *
 * @package Frontend_Controllers
 **/
class CommentsController extends Controller
{
    /**
     * Returns the list of comments for a given content id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function getAction(Request $request)
    {
        $contentID   = $request->query->filter('content_id', null, FILTER_SANITIZE_NUMBER_INT);
        $elemsByPage = $request->query->getDigits('elems_per_page', 10);
        $offset      = $request->query->getDigits('offset', 1);
        $darkTheme   = $request->query->getDigits('dark_theme', 0);

        if (empty($elemsByPage)) {
            $elemsByPage = 10;
        }

        if (!empty($contentID)
            && \Content::checkExists($contentID)
        ) {
            // Getting comments and total count comments for current article
            $commentManager = $this->get('comment_repository');
            $total    = $commentManager->countCommentsForContentId($contentID);
            $comments = $commentManager->getCommentsforContentId(
                $contentID,
                $elemsByPage,
                $offset
            );

            foreach ($comments as &$comment) {
                $vote = new \Vote($comment->id);
                $comment->votes = $vote;
            }

            return $this->render('comments/loader.tpl', [
                'total'          => $total,
                'comments'       => $comments,
                'contentId'      => $contentID,
                'elems_per_page' => $elemsByPage,
                'offset'         => $offset,
                'dark_theme'     => $darkTheme,
                'count'          => $total,
            ]);
        }

        return new Response('', 404);
    }

    /**
     * Shows the comments lists given a page number
     *
     * @param Request $request the request object
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
            $commentManager = $this->get('comment_repository');
            $total    = $commentManager->countCommentsForContentId($contentID);
            $comments = $commentManager->getCommentsforContentId(
                $contentID,
                $elemsByPage,
                $offset
            );

            foreach ($comments as &$comment) {
                $vote = new \Vote($comment->id);
                $comment->votes = $vote;
            }

            $contents = $this->renderView('comments/partials/comment_element.tpl', [
                'total'          => $total,
                'comments'       => $comments,
                'contentId'      => $contentID,
                'elems_per_page' => $elemsByPage,
                'offset'         => $offset,
            ]);

            // Inform the client if there is more elements
            $more = true;
            if ($total < ($elemsByPage*$offset)) {
                $more = false;
            }

            $output = [
                'contents' => $contents,
                'more'     => $more,
            ];

            $response = new Response(json_encode($output), 200);
        } else {
            $response = new Response('', 404);
        }
        return $response;
    }

    /**
     * Votes a comment given the punctuation and comment id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function voteAction(Request $request)
    {
        // Retrieve request data
        $voteValue = $request->request->filter('vote', null, FILTER_SANITIZE_STRING);
        $commentId = $request->request->getDigits('comment_id', 0);
        $cookie    = $request->cookies->get('comment-vote-'.$commentId);
        $ip        = getUserRealIP();

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
            return new Response(_("Error: no vote value!", 400));
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
     * Saves a content given its information and the content to relate to
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function saveAction(Request $request)
    {
        $body         = $request->request->filter('body', '', FILTER_SANITIZE_STRING);
        $authorName   = $request->request->filter('author-name', '', FILTER_SANITIZE_STRING);
        $authorEmail  = $request->request->filter('author-email', '', FILTER_SANITIZE_STRING);
        $contentId    = $request->request->getDigits('content-id');
        $ip           = getUserRealIP();

        $httpCode = 400;

        if (!empty($body)
            && !empty($authorName)
            && !empty($contentId)
        ) {
            // Validate email if not empty
            if (!filter_var($authorEmail, FILTER_VALIDATE_EMAIL) &&
                !empty($authorEmail)
            ) {
                $message = _('Please enter a valid email address');
            } else {
                $comment = new \Comment();
                if (\Repository\CommentManager::hasBadWordsComment($authorName.' '.$body)) {
                    $message = _('Your comment was rejected due insults usage.');
                } else {
                    try {
                        $data = [
                            'content_id'   => $contentId,
                            'body'         => $body,
                            'author'       => $authorName,
                            'author_email' => $authorEmail,
                            'author_ip'    => $ip
                        ];
                        $data = array_map('strip_tags', $data);

                        $data['body'] = '<p>'.preg_replace('@\\n@', '</p><p>', $data['body']).'</p>';

                        // Check moderation option
                        $commentsOpt = s::get('comments_config');
                        if (is_array($commentsOpt)
                            && array_key_exists('moderation', $commentsOpt)
                            && $commentsOpt['moderation'] == 0
                        ) {
                            $data['status'] = \Comment::STATUS_ACCEPTED;
                            $message = _('Your comment was accepted. Refresh the page to see it.');
                        } else {
                            $message = _('Your comment was accepted and now we have to moderate it.');
                        }

                        $comment->create($data);
                        $httpCode = 200;
                    } catch (\Exception $e) {
                        $message = $e->getMessage();
                    }
                }
            }
        } else {
            $message = _('Ensure you have completed all the form fields.');
        }

        return  new Response($message, $httpCode);
    }

    /**
     * Returns a json containing the list of comments count for each content requested
     *
     * @param Request $request the request object
     *
     * @return JsonResponse
     **/
    public function getCommentsCountAction(Request $request)
    {
        // Fetch the list of content ids, clean and filter them
        $ids  = $ids = $request->query->get('ids', []);
        $ids = array_unique(array_filter(
            array_map(
                function($id) { return (int) $id; },
                explode(',', $ids)
            ),
            function($id){return ((int) $id) > 0; }
        ));

        // Fetch data from database
        $commentsCount = [];
        if (count($ids) > 0) {
            try {
                $ids = implode(',', $ids);
                $conn  = $this->get('orm.manager')->getConnection('instance');
                $data = $conn->fetchAll(
                    'SELECT content_id, COUNT(*) as comments_count FROM comments '
                    .'WHERE content_id IN ('.$ids.') AND status = ? GROUP BY content_id',
                    [ \Comment::STATUS_ACCEPTED ]
                );
                foreach ($data as $value) {
                    $commentsCount[$value['content_id']] = $value['comments_count'];
                }
            } catch (\Exception $e) {
                return new JsonResponse($commentsCount, 500);
            }
        }

        // Prepare response
        $response = new JsonResponse();
        $response->setData($commentsCount);
        // Add edge cache support
        $response->headers->set('x-tags', 'comments,'.$ids);
        $response->headers->set('x-cache-for', '300s');
        $response->headers->set('x-cacheable', 'true');

        // I've opted to not use JSONP so no need of this
        // $response->setCallback('setFrontpageComments');
        // $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
