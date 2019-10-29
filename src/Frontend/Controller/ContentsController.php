<?php
/**
 * Handles the generic actions for contents
 *
 * @package Frontend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Common\Core\Annotation\BotDetector;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the generic actions for contents
 *
 * @package Frontend_Controllers
 */
class ContentsController extends Controller
{
    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function printAction(Request $request)
    {
        $dirtyID = $request->query->filter('content_id', '', FILTER_SANITIZE_STRING);
        $urlSlug = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        // Resolve content ID, we dont know which type the content is so we have to
        // perform some calculations
        preg_match("@(?P<date>\d{1,14})(?P<id>\d+)@", $dirtyID, $matches);

        if (empty($matches)) {
            throw new ResourceNotFoundException();
        }

        $contentID = $matches['id'];

        $content = new \Content($contentID);
        $content = $this->get('content_url_matcher')
            ->matchContentUrl($content->content_type_name, $dirtyID, $urlSlug);

        if (empty($content)) {
            throw new ResourceNotFoundException();
        }

        // Check for paywall
        if (!is_null($content)) {
            $this->paywallHook($content);
        }

        if (isset($content->img2) && ($content->img2 != 0)) {
            $photoInt = $this->get('entity_repository')->find('Photo', $content->img2);
            $this->view->assign('photoInt', $photoInt);
        }

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('content', $contentID, 'print');

        return $this->render('article/article_printer.tpl', [
            'cache_id'  => $cacheID,
            'content'   => $content,
            'article'   => $content,
            'o_content' => $content,
            'x-tags'    => 'content-print,' . $contentID
        ]);
    }

    /**
     * Increments the num views for a content given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @BotDetector
     */
    public function statsAction(Request $request)
    {
        $contentId = $request->query->getDigits('content_id', 0);

        // Raise exception if content id is not provided
        if ($contentId <= 0) {
            throw new ResourceNotFoundException();
        }

        // Increment view only if the request is performed with an AJAX request
        if ($request->isXmlHttpRequest()) {
            $saved = $this->get('content_views_repository')->setViews($contentId);

            $httpCode = 400;
            $content  = "false";

            if ($saved) {
                $httpCode = 200;
                $content  = "Ok";
            }
        } else {
            $httpCode = 400;
            $content  = "Not AJAX request";
        }

        return new Response($content, $httpCode);
    }

    /**
     * Alteres the article given the paywall module status
     */
    public function paywallHook(&$content)
    {
        $paywallActivated         = $this->get('core.security')->hasExtension('PAYWALL');
        $onlyAvailableSubscribers = $content->isOnlyAvailableForSubscribers();

        if ($paywallActivated && $onlyAvailableSubscribers) {
            $newContent = $this->renderView(
                'paywall/partials/content_only_for_subscribers.tpl',
                ['id' => $content->id]
            );

            $user = $this->getUser();
            if (!empty($user) && is_object($user)) {
                if (!empty($user->meta)
                    && array_key_exists('paywall_time_limit', $user->meta)
                ) {
                    $userSubscriptionDateString = $user->meta['paywall_time_limit'];
                } else {
                    $userSubscriptionDateString = '';
                }
                $userSubscriptionDate = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $userSubscriptionDateString,
                    new \DateTimeZone('UTC')
                );

                $now = new \DateTime('now', new \DateTimeZone('UTC'));

                $hasSubscription = $userSubscriptionDate > $now;

                if (!$hasSubscription) {
                    $newContent    = $this->renderView(
                        'paywall/partials/content_only_for_subscribers.tpl',
                        [ 'id'     => $content->id ]
                    );
                    $content->body = $newContent;
                }
            } else {
                $content->body = $newContent;
            }
        }
    }

    /**
     * Redirects from a content permalink to a content url
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function permalinkAction(Request $request)
    {
        $id = (int) $request->query->getDigits('content_id');

        if ($id <= 0) {
            throw new ResourceNotFoundException();
        }

        // The only way to know which type of content is to query for the entire
        // content and then do the translation to the entity repository service
        // Not very proud of this.
        $content = new \Content($id);
        $content = $this->get('entity_repository')->find(classify($content->content_type_name), $id);

        if (!is_object($content)) {
            throw new ResourceNotFoundException();
        }

        $url = $this->get('core.helper.url_generator')->generate($content);

        return new \Symfony\Component\HttpFoundation\RedirectResponse($url);
    }
}
