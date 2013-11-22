<?php
/**
 * Handles the actions for comments
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Frontend_Controllers
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for comments
 *
 * @package Frontend_Controllers
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

            $output = $this->renderView(
                'comments/loader.tpl',
                array(
                    'total'          => $total,
                    'comments'       => $comments,
                    'contentId'      => $contentID,
                    'elems_per_page' => $elemsByPage,
                    'offset'         => $offset,
                    'dark_theme'     => $darkTheme,
                    'count'          => $total,
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
        $ip        = $request->getClientip();

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
     * Saves a content given its information and the content to relate to
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function saveAction(Request $request)
    {
        $body        = $request->request->filter('body', '', FILTER_SANITIZE_STRING);
        $authorName  = $request->request->filter('author-name', '', FILTER_SANITIZE_STRING);
        $authorEmail = $request->request->filter('author-email', '', FILTER_SANITIZE_STRING);
        $contentId   = $request->request->getDigits('content-id');
        $ip          = $request->getClientip();

        $httpCode = 400;

        if (!empty($body)
            && !empty($authorName)
            && !empty($authorEmail)
            && !empty($contentId)
        ) {
            // Prevent XSS attack

            $comment = new \Comment();
            if (\Repository\CommentManager::hasBadWordsComment($authorName.' '.$body)) {
                $message = _('Your comment was rejected due insults usage.');
            } else {
                try {
                    $data          = array(
                        'content_id'   => $contentId,
                        'body'         => $body,
                        'author'       => $authorName,
                        'author_email' => $authorEmail,
                        'author_ip'    => $ip,
                    );
                    $data = array_map('strip_tags', $data);

                    $comment->create($data);
                    $httpCode = 200;
                    $message = _('Your comment was accepted and now we have to moderate it.');
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                }
            }
        } else {
            $message = _('Ensure you have completed all the form fields.');
        }

        return  new Response($message, $httpCode);
    }

    /**
     * Synchronize disqus comments to local database
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function disqusSyncAction(Request $request)
    {
        // Get disqus shortname and secretkey
        $disqusShortName = s::get('disqus_shortname');
        $disqusSecretKey = s::get('disqus_secret_key');

        // Create Disqus instance
        $disqus = new \DisqusAPI($disqusSecretKey);

        // Set API call params
        $params = array('forum' => $disqusShortName, 'order' =>  'asc', 'limit' => 100);

        // Save last sync time in cache
        $this->container->get('cache')->save(CACHE_PREFIX.'disqus_last_sync', time());

        // Fetch last comment date
        $comment = new \Comment();
        $lastDate = $comment->getLastCommentDate();
        if ($lastDate) {
            $params['since'] = date('Y-m-d H:i:s', strtotime($lastDate) + 1);
        }

        // Store all contents id on this array to update num comments
        $contents = array();

        // Fetch the latest comments (http://disqus.com/api/docs/posts/list/)
        do {
            try {
                $posts = $disqus->posts->list($params);

                foreach ($posts as $post) {
                    // Fetch thread details (http://disqus.com/api/docs/threads/details/)
                    $threadDetails = $disqus->threads->details(array('thread' => $post->thread));

                    // Get content id from disqus identifier
                    $contentId = 0;
                    if (!empty($threadDetails) && isset($threadDetails->identifiers[0])) {
                        $disqusIdentifier = @explode('-', $threadDetails->identifiers[0]);
                        if (isset($disqusIdentifier[1])) {
                            $contentId = $disqusIdentifier[1];
                        }
                    }

                    // Add contents id to array
                    $contents[$contentId] = $contentId;

                    // Get parent_id if not null
                    $parentId = 0;
                    if (!is_null($post->parent)) {
                        $parentId = $comment->getCommentIdFromPropertyAndValue('disqus_post_id', $post->parent);
                    }

                    $data = array(
                        'content_id'   => $contentId,
                        'author'       => $post->author->name,
                        'author_email' => @$post->author->email,
                        'author_url'   => @$post->author->url,
                        'author_ip'    => @$post->ipAddress,
                        'date'         => date('Y-m-d H:i:s', strtotime($post->createdAt)),
                        'body'         => $post->raw_message,
                        'status'       => ($post->isApproved) ? 'accepted': 'rejected',
                        'agent'        => 'Disqus v3.0',
                        'type'         => 'comment',
                        'parent_id'    => $parentId,
                        'user_id'      => 0,
                    );

                    // Create comment
                    $comment->create($data);

                    // Set contentmeta for comment
                    $comment->setProperty('disqus_post_id', $post->id);
                    $comment->setProperty('disqus_thread_id', $post->thread);
                    $comment->setProperty('disqus_thread_link', $threadDetails->link);

                }

                if (!empty($posts)) {
                    $params['since'] = $posts[count($posts)-1]->createdAt;
                }

            } catch (\DisqusAPIError $e) {
                $this->get('logger')->notice(
                    "Unable to import disqus comment ".$e->getMessage()
                );
            }

        } while (count($posts) == 100);

        foreach ($contents as $id) {
            $comment->updateContentTotalComments($id);
        }

        return  new Response('', 200);
    }
}
