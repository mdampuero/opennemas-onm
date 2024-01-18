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

use Api\Exception\GetListException;
use DateTime;
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
class CommentController extends FrontendController
{
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PENDING  = 'pending';

    /**
     * {@inheritdoc}
     */
    protected $extension = 'COMMENT_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.comment';

    /**
     * Returns the list of comments for a given content id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function getAction(Request $request)
    {
        $contentId = $request->query->filter('content_id', null, FILTER_SANITIZE_NUMBER_INT);
        $epp       = (int) $request->query->get('elems_per_page');
        $offset    = $request->query->getDigits('offset', 1);
        $content   = $this->getContent($contentId);

        if (empty($content)) {
            return new Response('', 404);
        }

        $comments = $this->getComments($content, $epp, $offset);
        $sh       = $this->get('core.helper.subscription');

        if ($sh->hasAdvertisements($sh->getToken($content))) {
            $this->getAds();
        }

        return $this->render('comments/loader.tpl', [
            'total'          => $comments['total'],
            'comments'       => $comments['items'],
            'contentId'      => $content->id,
            'elems_per_page' => $epp,
            'required_email' => $this->get('core.helper.comment')->isEmailRequired(),
            'offset'         => $offset,
            'more'           => $comments['total'] > ($epp * $offset),
            'x-cacheable'    => true,
            'x-tags'         => 'comments-' . $contentId,
            'recaptcha'      => $this->get('core.recaptcha')
                ->setVersion(2)
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
        $contentId = $request->query->filter('content_id', null, FILTER_SANITIZE_NUMBER_INT);
        $epp       = (int) $request->query->get('elems_per_page');
        $offset    = $request->query->getDigits('offset', 1);

        $content = $this->getContent($contentId);

        if (empty($content)) {
            return new Response('', 404);
        }

        $comments = $this->getComments($content, $epp, $offset);

        $sh = $this->get('core.helper.subscription');

        if ($sh->hasAdvertisements($sh->getToken($content))) {
            $this->getAds();
        }

        $contents = $this->get('core.template.frontend')
            ->render('comments/partials/comment_element.tpl', [
                'total'          => $comments['total'],
                'comments'       => $comments['items'],
                'contentId'      => $content->id,
                'elems_per_page' => $epp,
                'offset'         => $offset,
            ]);

        return new Response(json_encode([
            'contents' => $contents,
            'more'     => $comments['total'] > ($epp * $offset),
        ]), 200);
    }

    /**
     * Returns the number of comments of the ids passed as parameters.
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function countCommentsAction(Request $request)
    {
        $ids      = $request->query->filter('ids', '', FILTER_SANITIZE_STRING);
        $splitIds = explode(',', $ids);

        if (count($splitIds) === 0) {
            return new Response(json_encode([]));
        }

        $comments = $this->get('api.service.comment')->getList(sprintf(
            'content_id in[%s] and status = "accepted"',
            $ids
        ))['items'];

        if (count($comments) === 0) {
            return new Response(json_encode([]));
        }

        $response = [];

        foreach ($splitIds as $id) {
            $response[$id] = count(array_filter($comments, function ($comment) use ($id) {
                return $comment->content_id == $id;
            }));
        }

        return new Response(json_encode($response), 200);
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
        $comment   = $this->get($this->service)->getItem($commentId);

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

        try {
            // Positive Vote up - Negative Vote down
            $vote = ($voteValue == 'up') ? 'positive' : 'negative';

            $value = empty($comment->{$vote}) ? 1 : $comment->{$vote} + 1;

            $this->get($this->service)->updateItem($commentId, [$vote => $value]);

            $response = new Response('ok', 200);
            $response->headers->setCookie(
                new Cookie('comment-vote-' . $commentId, true, new \DateTime('+3 days'))
            );
        } catch (\Exception $e) {
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
        $authorEmail = $request->request->filter('author-email', null, FILTER_SANITIZE_STRING);
        $contentId   = $request->request->getDigits('content-id');
        $content     = $this->getContent($contentId);
        $response    = $request->request->filter('g-recaptcha-response');
        $ip          = getUserRealIP();
        $cm          = $this->get('core.helper.comment');
        $xmlRequest  = $request->isXmlHttpRequest();

        // Check current recaptcha
        $isValid = $this->get('core.recaptcha')
            ->setVersion(2)
            ->configureFromSettings()
            ->isValid($response, $request->getClientIp());

        if (!$isValid) {
            return new JsonResponse([
                'type' => 'error',
                'message' => _('Please fill the captcha code.'),
            ], 400);
        }

        try {
            $now = new DateTime();

            $data = [
                'content_id'   => $contentId,
                'body'         => $body,
                'author'       => $authorName,
                'author_ip'    => $ip,
            ];

            if (!empty($authorEmail)) {
                $data['author_email'] = $authorEmail;
            }

            $data = array_map('strip_tags', $data);

            $data['body'] = '<p>' . preg_replace('@\\n@', '</p><p>', $data['body']) . '</p>';

            $errors = $this->get('core.validator')->validate($data, 'comment');

            $data['date']                    = $now->format('Y-m-d H:i:s');
            $data['content_type_referenced'] = $content->content_type_name;

            if (!empty($errors) && $errors['type'] == 'fatal') {
                return new JsonResponse([
                    'message' => sprintf(
                        '<strong>%s</strong><br> %s',
                        _('Your comment was rejected due to:'),
                        implode('<br>', $errors['errors'])
                    ),
                    'type'    => 'error',
                ], 400);
            }

            if (!empty($errors)) {
                $data['status'] = $cm->autoReject()
                    ? self::STATUS_REJECTED
                    : self::STATUS_PENDING;

                $handling = $cm->autoReject()
                    ? _('Your comment was rejected due to:')
                    : _('Your comment is waiting for moderation due to:');

                return $this->saveData($data, $xmlRequest) ?: new JsonResponse([
                    'message' => sprintf(
                        '<strong>%s</strong><br> %s',
                        $handling,
                        implode('<br>', $errors['errors'])
                    ),
                    'type'    => 'error',
                ], 400);
            }

            if ($cm->moderateManually() || !$cm->autoAccept()) {
                $data['status'] = self::STATUS_PENDING;

                return $this->saveData($data, $xmlRequest) ?: new JsonResponse([
                    'type' => 'success',
                    'message' => _('Your comment was accepted and now we have to moderate it.'),
                ], 200);
            }

            if (!$cm->moderateManually() && $cm->autoAccept()) {
                $data['status'] = self::STATUS_ACCEPTED;

                return $this->saveData($data, $xmlRequest) ?: new JsonResponse([
                    'type' => 'success',
                    'message' => _('Your comment was accepted.'),
                ], 200);
            }
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
                'type'    => 'error',
            ], 400);
        }
    }

    /**
     * Loads the list of positions and advertisements on renderer service.
     */
    public function getAds()
    {
        $positionManager = $this->get('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup('comment');
        $advertisements  = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions);

        $this->get('frontend.renderer.advertisement')
            ->setPositions($positions)
            ->setAdvertisements($advertisements);
    }

    /**
     * Returns content for the comment.
     *
     * @param integer $id the content id.
     *
     * @return object The content object.
     */
    protected function getContent($id)
    {
        if (empty($id)) {
            return null;
        }

        $item = $this->get('entity_repository')->findOneBy([
            'content_status' => [ [ 'value' => 1 ] ],
            'in_litter'      => [ [ 'value' => 0 ] ],
            'pk_content'     => [ [ 'value' => $id ] ],
        ]);

        return $item;
    }

    /**
     * Returns comments for the content given a page.
     *
     * @param object  $contents The content object.
     * @param integer $epp     The number of elements to return.
     * @param integer $offset  The initial offset.
     *
     * @return array The comments for content.
     */
    protected function getComments($content, $epp, $offset)
    {
        $configs = $this->get('core.helper.comment')->getConfigs();

        $epp = empty($epp) ? (int) $configs['number_elements'] : $epp;

        $oql = sprintf(
            'content_id = %d'
            . ' and status = "accepted"'
            . ' order by date desc limit %d offset %d',
            $content->pk_content,
            $epp,
            ($offset - 1) * $epp
        );

        try {
            $comments = $this->get($this->service)->getList($oql);

            return $comments;
        } catch (GetListException $ex) {
            return ['total' => 0, 'items' => []];
        }
    }

    /**
     * Save comment and returns RedirectResponse if the request is a XMLHttpRequest
     *
     * @param array $data Data to save.
     * @param boolean $isXmlHttpRequest True if the request is a XMLHttpRequest
     *
     * @return any RedirectResponse if the request is a XMLHttpRequest null otherwise
     */
    protected function saveData($data, $isXmlHttpRequest)
    {
        $this->get($this->service)->createItem($data);

        if (!$isXmlHttpRequest) {
            return new RedirectResponse($this->generateUrl('frontend_comments_get', [
                'id' => $data['contentId'],
            ]));
        }

        return null;
    }
}
