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
namespace Frontend\Controller;

use Common\Core\Annotation\BotDetector;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the generic actions for contents
 *
 * @package Frontend_Controllers
 **/
class ContentsController extends Controller
{
    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function printAction(Request $request)
    {
        $dirtyID = $request->query->filter('content_id', '', FILTER_SANITIZE_STRING);
        $urlSlug = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        // Resolve content ID, we dont know which type the content is so we have to
        // perform some calculations
        preg_match("@(?P<date>\d{1,14})(?P<id>\d+)@", $dirtyID, $matches);
        $dirtyID   = $matches['date'].sprintf('%06d', $matches['id']);
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
            'cache_id' => $cacheID,
            'content'  => $content,
            'article'  => $content,
            'x-tags'   => 'content-print,'.$contentID
        ]);
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

        // Get sync params
        $wsUrl = $this->get('core.helper.instance_sync')->getSyncUrl($categoryName);
        if (empty($wsUrl)) {
            throw new ResourceNotFoundException();
        }

        // Resolve article ID
        $contentID = $cm->getUrlContent($wsUrl.'/ws/contents/resolve/'.$dirtyID, true);

        // Fetch content
        $content = $cm->getUrlContent($wsUrl.'/ws/contents/read/'.$contentID, true);
        $content = @unserialize($content);

        if (isset($content->img2) && ($content->img2 != 0)) {
            $photoInt = $this->get('entity_repository')->find('Photo', $content->img2);
            $this->view->assign('photoInt', $photoInt);
        }

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('sync', 'content', $contentID, 'print');

        return $this->render('article/article_printer.tpl', [
            'cache_id' => $cacheID,
            'content'  => $content,
            'article'  => $content,
            'x-tags'   => 'ext-content-print,'.$contentID
        ]);
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
        if ('POST' == $request->getMethod()) {
            $valid = false;
            $errors = [];

            $response = $request->request->filter('g-recaptcha-response', '', FILTER_SANITIZE_STRING);
            $isValid  = $this->get('core.recaptcha')
                ->configureFromParameters()
                ->isValid($response, $request->getClientIp());

            if (!$isValid) {
                $errors[] = _('The reCAPTCHA was not entered correctly. Try to authenticate again.');
            }

            // If the content is external load it from the external webservice
            $contentID = $request->request->getDigits('content_id', null);
            $ext       = $request->request->getDigits('ext', 0);
            if ($ext == 1) {
                // Getting Synchronize setting params
                $categoryName = $request->request->get('category_name', null);

                // Get sync params
                $wsUrl = $this->get('core.helper.instance_sync')->getSyncUrl($categoryName);
                if (empty($wsUrl)) {
                    throw new ResourceNotFoundException();
                }

                $cm = new \ContentManager();
                $content = $cm->getUrlContent($wsUrl.'/ws/contents/read/'.$contentID, true);
                $content = unserialize($content);
            } else {
                $content = new \Content($contentID);
                if ($content->content_type == '4') { // Opinion
                    $content = getService('opinion_repository')->find('Opinion', $contentID);
                }
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

            $this->view->setCaching(0);
            $this->view->assign([
                'content'     => $content,
                'senderName'  => $senderName,
                'senderEmail' => $senderEmail,
            ]);

            $mailBody      = $this->renderView('email/send_to_friend.tpl');
            $mailBodyPlain = $this->renderView('email/send_to_friend_just_text.tpl');

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

                $this->get('application.log')->notice(
                    "Email sent. Share-by-email (sender:".$senderEmail.", to: ".$recipients[0].", content_id:".$contentID.")"
                );

                $content = _('Article sent successfully');
                $httpCode = 200;
            } catch (\Exception $e) {
                // Log this error
                $logger = $this->get('application.log');
                $logger->notice("Unable to send by email the content [$contentID]: ".$e->getMessage());

                $content = array(_('Unable to send the email. Please try to send it later.'));

                $content = json_encode($content);

                $httpCode = 500;
            }

            return new Response($content, $httpCode);
        } else {
            $contentID    = $request->query->getDigits('content_id', null);
            $ext          = $request->query->getDigits('ext', 0);

            // $token = md5(uniqid('sendform'));
            // $this->get('session')->set('sendformtoken', $token);

            if ($ext == 1) {
                // Getting Synchronize setting params
                $categoryName = $request->query->get('category_name', null);

                // Get sync params
                $wsUrl = $this->get('core.helper.instance_sync')->getSyncUrl($categoryName);
                if (empty($wsUrl)) {
                    throw new ResourceNotFoundException();
                }

                $cm = new \ContentManager();
                $content = $cm->getUrlContent($wsUrl.'/ws/contents/read/'.$contentID, true);
                $content = unserialize($content);
            } else {
                $content = new \Content($contentID);
            }

            return $this->render('common/share_by_mail.tpl', [
                'content'    => $content,
                'content_id' => $contentID,
                // 'token'      => $token,
                'recaptcha' => $this->get('core.recaptcha')
                    ->configureFromParameters()
                    ->getHtml(),
                'ext'        => $ext,
            ]);
        }
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
            $this->get('content_views_repository')->setViews($contentId);
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
        $paywallActivated = $this->get('core.security')->hasExtension('PAYWALL');
        $onlyAvailableSubscribers = $content->isOnlyAvailableForSubscribers();

        if ($paywallActivated && $onlyAvailableSubscribers) {
            $newContent = $this->renderView(
                'paywall/partials/content_only_for_subscribers.tpl',
                array('id' => $content->id)
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
                    $newContent = $this->renderView(
                        'paywall/partials/content_only_for_subscribers.tpl',
                        [
                            'logged' => $isLogged,
                            'id'     => $content->id
                        ]
                    );
                    $content->body = $newContent;
                }
            } else {
                $content->body = $newContent;
            }
        }
    }
}
