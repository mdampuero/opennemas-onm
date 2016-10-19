<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

/**
 * Adds support for readers to send content to the newspaper via email.
 */
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
        if ('POST' != $request->getMethod()) {
            return new RedirectResponse($this->generateUrl('frontend_participa_frontpage'));
        }

        // Get request params
        $verify = $request->request->filter('security_code', "", FILTER_SANITIZE_STRING);

        if (!empty($verify)) {
            return new RedirectResponse($this->generateUrl('frontend_participa_frontpage'));
        }

        $email     = trim($request->request->filter('email', null, FILTER_SANITIZE_STRING));
        $response  = $request->request->filter('g-recaptcha-response', null, FILTER_SANITIZE_STRING);
        $formType  = $request->request->filter('form_type', '', FILTER_SANITIZE_STRING);
        $message   = '';
        $class     = 'error';

        // Recaptcha for the next content
        $recaptcha = $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml();

        // Check current recaptcha
        $isValid = $this->get('core.recaptcha')
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
                    $body .= "<p>$key => $value </p> \n";
                }
            }

            $name      = $request->request->filter('name', '', FILTER_SANITIZE_STRING);
            $subject   = $request->request->filter('subject', null, FILTER_SANITIZE_STRING);
            $recipient = trim($request->request->filter('recipient', null, FILTER_SANITIZE_STRING));

            $settings = $this->get('setting_manager')->get([ 'mail_sender', 'site_name' ]);

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

            if (isset($_FILES['image1']) && !empty($_FILES['image1']["name"])) {
                $file     = $_FILES["image1"]["tmp_name"];
                $filename = $_FILES["image1"]["name"];
                $type     = $_FILES["image1"]["type"];
                $text->attach(\Swift_Attachment::fromPath($file, $type)->setFilename($filename));
            }

            if (isset($_FILES['image2']) && !empty($_FILES['image2']["name"])) {
                $file     = $_FILES["image2"]["tmp_name"];
                $filename = $_FILES["image2"]["name"];
                $type     = $_FILES["image2"]["type"];
                $text->attach(\Swift_Attachment::fromPath($file, $type)->setFilename($filename));
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
            'recaptcha' => $recaptcha,
            'message'   => $message,
            'class'     => $class,
            'formType'  => $formType,
        ]);
    }

    /**
     * Returns the advertisements for the letters frontpage.
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
