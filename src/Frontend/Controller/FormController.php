<?php
/**
 * Handles the actions for newsletter subscriptions
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

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for form send by mail
 *
 * @package Frontend_Controllers
 **/
class FormController extends Controller
{
    /**
     * Displays a form to send content to the newspaper.
     *
     * @return Response The response object.
     */
    public function frontpageAction()
    {
        if (!$this->get('core.security')->hasExtension('FORM_MANAGER')) {
            throw new ResourceNotFoundException();
        }

        return $this->render('static_pages/form.tpl', [
            'advertisements' => $this->getAds(),
            'recaptcha'      => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
            'x-tags'         => 'frontpage-form',
        ]);
    }

    /**
     * Sends a new content created by readers to the newspaper via email.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function sendAction(Request $request)
    {
        // Get request params
        $verify = $request->request->filter('security_code', "", FILTER_SANITIZE_STRING);

        if ('POST' != $request->getMethod() || !empty($verify)) {
            return new RedirectResponse($this->generateUrl('frontend_participa_frontpage'));
        }

        $email     = trim($request->request->filter('email', null, FILTER_SANITIZE_STRING));
        $response  = $request->request->filter('g-recaptcha-response', null, FILTER_SANITIZE_STRING);
        $formType  = $request->request->filter('form_type', '', FILTER_SANITIZE_STRING);
        $message   = '';
        $class     = 'error';

        // Check current recaptcha
        $isValid = $this->get('core.recaptcha')
            ->configureFromSettings()
            ->isValid($response, $request->getClientIp());

        if (empty($email)) {
            $message .= _('Email is required but it will not be published');
        }

        if (!$isValid) {
            $message .= (empty($message) ? '' : '<br>')
                . _("The reCAPTCHA wasn't entered correctly. Go back and try it again.");
        }

        // What happens when the CAPTCHA was entered incorrectly
        if (!empty($email) && $isValid) {
            // Check data form is correcty and serialize form
            $body       = '';
            $notAllowed = [ 'subject', 'cx', 'security_code', 'submit', 'g-recapcha-response' ];

            foreach ($request->request as $key => $value) {
                if (!in_array($key, $notAllowed)) {
                    $body .= "<p><strong>".ucfirst($key)."</strong>: $value </p> \n";
                }
            }

            $name      = $request->request->filter('name', '', FILTER_SANITIZE_STRING);
            $subject   = $request->request->filter('subject', null, FILTER_SANITIZE_STRING);
            $recipient = trim($request->request->filter('recipient', null, FILTER_SANITIZE_STRING));

            $settings = $this->get('setting_repository')->get([ 'mail_sender', 'site_name', 'contact_email' ]);

            if (!array_key_exists('mail_sender', $settings)
                || empty($settings['mail_sender'])
            ) {
                $settings['mail_sender'] = "no-reply@postman.opennemas.com";
            }

            //  Build the message
            $text = \Swift_Message::newInstance();
            $text
                ->setSubject($subject)
                ->setBody($body, 'text/html')
                ->setTo(array($recipient => $recipient))
                ->setFrom(array($email => $name))
                ->setSender(array($settings['mail_sender'] => $settings['site_name']));

            $file1 = $request->files->get('image1');
            if ($file1) {
                $text->attach(\Swift_Attachment::fromPath(
                    $file1->getPathname(),
                    $file1->getClientMimeType()
                )->setFilename($file1->getClientOriginalName()));
            }

            $file2 = $request->files->get('image2');
            if ($file2) {
                $text->attach(\Swift_Attachment::fromPath(
                    $file2->getPathname(),
                    $file2->getClientMimeType()
                )->setFilename($file2->getClientOriginalName()));
            }

            try {
                $mailer = $this->get('swiftmailer.mailer.direct');
                $mailer->send($text);

                $this->get('application.log')->notice(
                    "Email sent. Frontend form (sender:".$email.", to: ".$recipient.")"
                );

                $action = new \Action();
                $action->set([ 'action_name' => 'form_1', 'counter' => 1 ]);

                $class   = 'success';
                $message = _('The information has been sent');
            } catch (\Swift_SwiftException $e) {
                $message = _('Sorry, we were unable to complete your request');
            }
        }


        return $this->render('static_pages/form.tpl', [
            'recaptcha' => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
            'message'   => $message,
            'class'     => $class,
            'formType'  => $formType,
        ]);
    }

    /**
     * Sends an email with form fields.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function widgetSendAction(Request $request)
    {
        // Get verify value to avoid bots
        $verify = $request->request->filter('security_code', '', FILTER_SANITIZE_STRING);

        if ('POST' == $request->getMethod() && empty($verify)) {
            $email     = trim($request->request->filter('email', null, FILTER_SANITIZE_STRING));
            $response  = $request->request->filter('g-recaptcha-response', null, FILTER_SANITIZE_STRING);
            $errors    = [];

            // Check current recaptcha
            $isValid = $this->get('core.recaptcha')
                ->configureFromSettings()
                ->isValid($response, $request->getClientIp());

            if (!$isValid) {
                $errors[] = _(
                    "The reCAPTCHA wasn't entered correctly. Go back and try it again."
                );
            }

            // Check email not empty
            if (empty($email)) {
                $errors[] = _('Email is required but it will not be published');
            }

            // If errors, return Response
            if (count($errors) > 0) {
                $content = json_encode($errors);
                $httpCode = 400;

                return new Response($content, $httpCode);
            }

            // Check if data is correct and generate email body
            $body       = '';
            $notAllowed = [ 'subject', 'cx', 'security_code', 'submit', 'g-recaptcha-response', 'recipients' ];
            foreach ($request->request as $key => $value) {
                if (!in_array($key, $notAllowed)) {
                    $body .= "<p><strong>".ucfirst($key)."</strong>: $value </p> \n";
                }
            }

            // Get subject and recipients. Also clean recipients
            $subject    = $request->request->filter('subject', null, FILTER_SANITIZE_STRING);
            $recipients = $request->request->filter('recipients', null, FILTER_SANITIZE_STRING);
            $cleanRecipients = [];
            $recipientsArray = explode(',', $recipients);
            foreach ($recipientsArray as $recipient) {
                $cleanRecipients[] = trim($recipient);
            }

            $settings = $this->get('setting_repository')->get([ 'site_name', 'contact_email' ]);

            //  Build the message
            $text = \Swift_Message::newInstance();
            $text
                ->setSubject($subject)
                ->setBody($body, 'text/html')
                ->setTo($cleanRecipients)
                ->setFrom($email)
                ->setSender([ 'no-reply@postman.opennemas.com' => $settings['site_name'] ]);

            try {
                $mailer = $this->get('mailer');
                $mailer->send($text);

                $this->get('application.log')->notice(
                    "Email sent. Frontend widget form (sender:".$email.", to: ".$recipients.")"
                );
                $content = _('The information has been sent');
                $httpCode = 200;
            } catch (\Swift_SwiftException $e) {
                $content = _('Unable to send the email. Please try to send it later.');
                $httpCode = 500;
            }

            return new Response($content, $httpCode);
        }
    }

    /**
     * Returns the advertisements for the form frontpage.
     *
     * @return array The list of advertisemnets.
     */
    public function getAds()
    {
        // Get letter positions
        $positions = $this->get('core.manager.advertisement')
            ->getPositionsForGroup('article_inner', [ 7, 9 ]);

        return \Advertisement::findForPositionIdsAndCategory($positions, 0);
    }
}
