<?php
/**
 * Handles the generic actions for contents
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Onm\Framework\Controller\Controller;
use Onm\Module\ModuleManager;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the generic actions for contents
 *
 * @package Frontend_Controllers
 **/
class ContentsController extends Controller
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
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function printAction(Request $request)
    {
        $dirtyID      = $request->query->filter('content_id', '', FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

        // Resolve article ID
        $contentID = \Content::resolveID($dirtyID);
        $cacheID   = $this->view->generateCacheId('article', null, $contentID);

        $content = new \Content($contentID);
        $content = $content->get($contentID);

        // Check for paywall
        if (!is_null($content)) {
            $this->paywallHook($content);
        }


        if (isset($content->img2) && ($content->img2 != 0)) {
            $photoInt = new \Photo($content->img2);
            $this->view->assign('photoInt', $photoInt);
        }

        return $this->render(
            'article/article_printer.tpl',
            array(
                'cache_id' => $cacheID,
                'content'  => $content,
                'article'  => $content
            )
        );
    }

    /**
     * Print an external article
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function extPrintAction(Request $request)
    {
        $dirtyID      = $request->query->filter('content_id', '', FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

        $cm = new \ContentManager;

        // Getting Synchronize setting params
        $wsUrl = '';
        $syncParams = s::get('sync_params');
        foreach ($syncParams as $siteUrl => $categoriesToSync) {
            foreach ($categoriesToSync as $value) {
                if (preg_match('/'.$categoryName.'/i', $value)) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        // Resolve article ID
        $contentID = $cm->getUrlContent($wsUrl.'/ws/contents/resolve/'.$dirtyID, true);
        $cacheID   = $this->view->generateCacheId('article', null, $contentID);

        // Fetch content
        $content = $cm->getUrlContent($wsUrl.'/ws/contents/read/'.$contentID, true);
        $content = unserialize($content);

        if (isset($content->img2) && ($content->img2 != 0)) {
            $photoInt = new \Photo($content->img2);
            $this->view->assign('photoInt', $photoInt);
        }

        return $this->render(
            'article/article_printer.tpl',
            array(
                'cache_id' => $cacheID,
                'content'  => $content,
                'article'  => $content
            )
        );
    }

    /**
     * Shares an content by email
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function shareByEmailAction(Request $request)
    {
        $session = $this->get('session');
        if ('POST' == $request->getMethod()) {
            // Check direct access
            if ($session->get('sendformtoken') != $request->request->get('token')) {
                throw new ResourceNotFoundException();
            }

            // If the content is external load it from the external webservice
            $contentID = $request->request->getDigits('content_id', null);
            if (false && $ext == 1) {
                $content = $cm->getUrlContent($wsUrl.'/ws/contents/read/'.$contentID, true);
                $content = unserialize($content);
            } else {
                $content = new \Content($contentID);
            }

            // Check if the content exists
            if (is_null($content->id)) {
                throw new ResourceNotFoundException();
            }

            // Fetch information required for sending the mail
            $senderEmail  = $request->request->filter('sender_email', null, FILTER_VALIDATE_EMAIL);
            $senderName   = $request->request->filter('sender_name', null, FILTER_SANITIZE_STRING);
            $mailSubject  = sprintf(
                _('%s has shared with you a content from %s.'),
                $senderName,
                s::get('site_name')
            );
            $recipients   = explode(',', $request->request->get('recipients', array()));

            $errors = array();
            if (empty($senderEmail)) {
                $errors []= _('Fill your Email address');
            }
            if (empty($senderName)) {
                $errors []= _('Complete your name');
            }
            $cleanRecipients = array();
            foreach ($recipients as $recipient) {
                if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    $cleanRecipients []= $recipient;
                }
            }
            if (count($cleanRecipients) <= 0) {
                $errors []= _('Provide a list of valid emails separated by commas.');
            }

            if (count($errors) > 0) {
                $content = json_encode($errors);
                $httpCode = 400;

                return new Response($content, $httpCode);
            }

            $tplMail = new \Template(TEMPLATE_USER);
            $tplMail->caching = 0;
            $tplMail->assign(
                array(
                    'content'     => $content,
                    'senderName'  => $senderName,
                    'senderEmail' => $senderEmail,
                )
            );

            $mailBody      = $tplMail->fetch('email/send_to_friend.tpl');
            $mailBodyPlain = $tplMail->fetch('email/send_to_friend_just_text.tpl');

            //  Build the message
            $message = \Swift_Message::newInstance();
            $message
                ->setSubject($mailSubject)
                ->setBody($mailBody, 'text/html')
                ->setBody($mailBodyPlain, 'text/plain')
                ->setTo($recipients[0])
                ->setFrom(array($senderEmail => $senderName))
                ->setSender(array('no-reply@postman.opennemas.com' => s::get('site_name')))
                ->setBcc($recipients);

            try {
                $mailer = $this->get('mailer');
                $mailer->send($message);

                $content = _('Article sent successfully');
                $httpCode = 200;
            } catch (\Exception $e) {
                // Log this error
                $logger = $this->get('logger');
                $logger->notice("Unable to send by email the content [$contentID]: ".$e->getMessage());

                $content = array(_('Unable to send the email. Please try to send it later.'));

                $content = json_encode($content);

                $httpCode = 500;
            }

            return new Response($content, $httpCode);
        } else {
            $contentId = $request->query->getDigits('content_id', null);

            $session = $this->get('session');

            $token = md5(uniqid('sendform'));
            $session->set('sendformtoken', $token);

            $content = new \Content($contentId);

            return $this->render(
                'common/share_by_mail.tpl',
                array(
                    'content'    => $content,
                    'content_id' => $contentId,
                    'token'      => $token,
                )
            );
        }
    }

    /**
     * Adds a vote for a content
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function rateContentAction(Request $request)
    {
        // If is POST request perform the vote action
        // if not render the vote
        if ('POST' == $request->getMethod()) {
            $ip        = getRealIp();
            $contentId = $request->request->getDigits('content_id', null);
            $voteValue = $request->request->getDigits('vote_value', null);

            $content = new \Content($contentId);

            if (is_null($content->id)) {
                // Content does not exists so raise an Not Found exception
                throw new ResourceNotFoundException();
            } else {
                $rating = new \Rating($content->id);
                $update = $rating->update($voteValue, $ip);

                // Render the rating system after rating the content
                $content = $rating->render('', 'result', 1);

                // Return the content and set a cookie for avoiding multiple rates
                $response = new Response($content, 200);
                $response->headers->setCookie(
                    new Cookie(
                        "rating-" . $contentId,
                        'true',
                        time() + 60 * 60 * 24 * 30
                    )
                );
            }
        } else {
            $contentId = $request->query->getDigits('content_id', null);
            $alreadyVoted = ($request->cookies->get('rating-'.$contentId) !== null) ? 'result' : 'vote';

            // Render the rating system
            $rating   = new \Rating($contentId);
            $content  = $rating->render('', $alreadyVoted);

            $response = new Response($content, 200);
        }

        return $response;
    }

    /**
     * Increments the num views for a content given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function statsAction(Request $request)
    {
        $contentId = $request->query->getDigits('content_id', 0);

        // Raise exception if content id is not provided
        if ($contentId <= 0) {
            throw new ResourceNotFoundException();
        }

        // Increment view only if the request is performed with an AJAX request
        if ($request->isXmlHttpRequest()) {
            \Content::setNumViews($contentId);
            $httpCode = 200;
            $content = "Ok";
        } else {
            $httpCode = 400;
            $content = "Not AJAX request";
        }

        return new Response($content, $httpCode);
    }

    /**
     * Alteres the article given the paywall module status
     *
     * @return Article the article
     **/
    public function paywallHook(&$content)
    {
        $paywallActivated = ModuleManager::isActivated('PAYWALL');
        $onlyAvailableSubscribers = $content->isOnlyAvailableForSubscribers();

        if ($paywallActivated && $onlyAvailableSubscribers) {
            $newContent = $this->renderView(
                'paywall/partials/content_only_for_subscribers.tpl',
                array('id' => $content->id)
            );

            $isLogged = array_key_exists('userid', $_SESSION);
            if ($isLogged) {
                if (array_key_exists('meta', $_SESSION)
                    && array_key_exists('paywall_time_limit', $_SESSION['meta'])) {
                    $userSubscriptionDateString = $_SESSION['meta']['paywall_time_limit'];
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
                    $newContent = $this->renderView(
                        'paywall/partials/content_only_for_subscribers.tpl',
                        array(
                            'logged' => $isLogged,
                            'id'     => $content->id
                        )
                    );
                    $content->body = $newContent;
                }
            } else {
                $content->body = $newContent;
            }
        }
    }
}
