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
 */
namespace Frontend\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles the actions for comments
 *
 * @package Frontend_Controllers
 */
class CommentsController extends Controller
{
    /**
     * Returns the list of comments for a given content id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function getAction(Request $request)
    {
        $contentID   = $request->query->filter('content_id', null, FILTER_SANITIZE_NUMBER_INT);
        $elemsByPage = $request->query->getDigits('elems_per_page');
        $offset      = $request->query->getDigits('offset', 1);
        $darkTheme   = $request->query->getDigits('dark_theme', 0);

        $configs = $this->get('core.helper.comment')->getConfigs();
        if (empty($elemsByPage)) {
            $elemsByPage = (int) $configs['number_elements'];
        }

        if (empty($contentID)
            || !\Content::checkExists($contentID)
        ) {
            return new Response('', 404);
        }

        // Getting comments and total count comments for current article
        $cm       = $this->get('comment_repository');
        $total    = $cm->countCommentsForContentId($contentID);
        $comments = $cm->getCommentsforContentId($contentID, $elemsByPage, $offset);

        foreach ($comments as &$comment) {
            $vote           = new \Vote($comment->id);
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
            'recaptcha'      => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
        ]);
    }

    /**
     * Shows the comments lists given a page number
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function ajaxAction(Request $request)
    {
        $contentID   = $request->query->filter('content_id', null, FILTER_SANITIZE_NUMBER_INT);
        $elemsByPage = $request->query->getDigits('elems_per_page');
        $offset      = $request->query->getDigits('offset', 1);

        if (empty($contentID)
            || !\Content::checkExists($contentID)
        ) {
            return new Response('Content doesnt exists', 404);
        }

        $configs = $this->get('core.helper.comment')->getConfigs();
        if (empty($elemsByPage)) {
            $elemsByPage = (int) $configs['number_elements'];
        }

        // Getting comments for current article
        $cm       = $this->get('comment_repository');
        $total    = $cm->countCommentsForContentId($contentID);
        $comments = $cm->getCommentsforContentId($contentID, $elemsByPage, $offset);

        foreach ($comments as &$comment) {
            $vote           = new \Vote($comment->id);
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
        $more = ($total < ($elemsByPage * $offset)) ? false : true;

        return new Response(json_encode([
            'contents' => $contents,
            'more'     => $more,
        ]), 200);
    }

    /**
     * Votes a comment given the punctuation and comment id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function voteAction(Request $request)
    {
        // Retrieve request data
        $voteValue = $request->request->filter('vote', null, FILTER_SANITIZE_STRING);
        $commentId = $request->request->getDigits('comment_id', 0);
        $cookie    = $request->cookies->get('comment-vote-' . $commentId);
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
        if (!in_array($voteValue, [ 'up', 'down' ])) {
            return new Response(_('Not valid vote value'), 400);
        }

        // 1 Vote up - 2 Vote down
        $voteValue = ($voteValue == 'up') ? 1 : 2;

        // Create the vote
        $voteObject = new \Vote($commentId);
        if (is_null($voteObject)) {
            return new Response(_("Error: no vote value!"), 400);
        }

        $update = $voteObject->update($voteValue, $ip);

        if ($update) {
            $response = new Response('ok', 200);
            $response->headers->setCookie(
                new Cookie('comment-vote-' . $commentId, true, new \DateTime('+3 days'))
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
     */
    public function saveAction(Request $request)
    {
        $body        = $request->request->filter('body', '', FILTER_SANITIZE_STRING);
        $authorName  = $request->request->filter('author-name', '', FILTER_SANITIZE_STRING);
        $authorEmail = $request->request->filter('author-email', '', FILTER_SANITIZE_STRING);
        $contentId   = $request->request->getDigits('content-id');
        $response    = $request->request->filter('g-recaptcha-response', null, FILTER_SANITIZE_STRING);
        $ip          = getUserRealIP();
        $cm          = $this->get('core.helper.comment');


        // Check current recaptcha
        $isValid = $this->get('core.recaptcha')
            ->configureFromSettings()
            ->isValid($response, $request->getClientIp());

        if (!$isValid) {
            return new JsonResponse([
                'type' => 'error',
                'message' => _('Please fill the captcha code.'),
            ], 400);
        }

        $httpCode = 200;
        try {
            $data = [
                'content_id'   => $contentId,
                'body'         => $body,
                'author'       => $authorName,
                'author_email' => $authorEmail,
                'author_ip'    => $ip
            ];
            $data = array_map('strip_tags', $data);

            $data['body'] = '<p>' . preg_replace('@\\n@', '</p><p>', $data['body']) . '</p>';

            if ($cm->moderateManually()) {
                $data['status'] = \Comment::STATUS_PENDING;

                $message = [
                    'message' => _('Your comment was accepted and now we have to moderate it.'),
                    'type'    => 'warning',
                ];

                $comment = new \Comment();
                $comment->create($data);
            } else {
                $errors = $this->get('core.validator')->validate($data, 'comment');

                if (empty($errors)) {
                    $data['status'] = $cm->autoAccept()
                        ? \Comment::STATUS_ACCEPTED
                        : \Comment::STATUS_PENDING;

                    $handling = $cm->autoAccept()
                        ? _('Your comment was accepted.')
                        : _('Your comment is valid and now is waiting for moderation.');

                    $httpCode = 200;
                    $message  = [
                        'message' => $handling,
                        'type'    => 'success',
                    ];

                    $comment = new \Comment();
                    $comment->create($data);
                } else {
                    $data['status'] = $cm->autoReject()
                        ? \Comment::STATUS_REJECTED
                        : \Comment::STATUS_PENDING;

                    $errorType = $errors['type'];
                    $httpCode  = 400;

                    $handling = ($cm->autoReject())
                        ? _('Your comment was rejected due to:')
                        : _('Your comment is waiting for moderation due to:');

                    $message = [
                        'message' => sprintf(
                            '<strong>%s</strong><br> %s',
                            $handling,
                            implode('<br>', $errors['errors'])
                        ),
                        'type'    => 'error',
                    ];

                    if ($errorType != 'fatal') {
                        $comment = new \Comment();
                        $comment->create($data);
                    }
                }
            }
        } catch (\Exception $e) {
            $httpCode = 400;
            $message  = $e->getMessage();
        }

        $response = new JsonResponse($message, $httpCode);
        if (!$request->isXmlHttpRequest()) {
            $response = new RedirectResponse($this->generateUrl('frontend_comments_get', [
                'id' => $contentId,
            ]));
        }

        return $response;
    }

    /**
     * Returns a json containing the list of comments count for each content requested
     *
     * @param Request $request the request object
     *
     * @return JsonResponse
     */
    public function getCommentsCountAction(Request $request)
    {
        // Fetch the list of content ids, clean and filter them
        $ids = $ids = $request->query->get('ids', []);
        $ids = array_unique(array_filter(
            array_map(
                function ($id) {
                    return (int) $id;
                },
                explode(',', $ids)
            ),
            function ($id) {
                return ((int) $id) > 0;
            }
        ));

        // Fetch data from database
        $commentsCount = [];
        if (count($ids) > 0) {
            try {
                $ids  = implode(',', $ids);
                $conn = $this->get('orm.manager')->getConnection('instance');
                $data = $conn->fetchAll(
                    'SELECT content_id, COUNT(*) as comments_count FROM comments '
                    . 'WHERE content_id IN (' . $ids . ') AND status = ? GROUP BY content_id',
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
        $response->headers->set('x-tags', 'comments,' . $ids);
        $response->headers->set('x-cache-for', '300s');
        $response->headers->set('x-cacheable', 'true');

        return $response;
    }
}
