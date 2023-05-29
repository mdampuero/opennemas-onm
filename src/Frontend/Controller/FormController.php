<?php
/**
 * Handles the actions for newsletter subscriptions
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

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for form send by mail
 *
 * @package Frontend_Controllers
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

        $this->getAds();

        return $this->render('static_pages/form.tpl', [
            'recaptcha'   => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
            'x-tags'      => 'frontpage-form',
            'x-cacheable' => true,
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
        $verify = $request->request->filter('security_code', '', FILTER_SANITIZE_STRING);

        if ('POST' != $request->getMethod() || !empty($verify)) {
            return new RedirectResponse($this->generateUrl('frontend_participa_frontpage'));
        }

        $email    = trim($request->request->filter('email', null, FILTER_SANITIZE_STRING));
        $response = $request->request->filter('g-recaptcha-response');
        $formType = $request->request->filter('form_type', '', FILTER_SANITIZE_STRING);
        $message  = '';
        $class    = 'error';

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
            $notAllowed = [ 'cx', 'g-recaptcha-response', 'recipient', 'security_code', 'subject' ];

            foreach ($request->request as $key => $value) {
                if (!in_array($key, $notAllowed)) {
                    $body .= "<p><strong>" . ucfirst($key) . "</strong>: $value </p> \n";
                }
            }

            $subject  = $request->request->filter('subject', null, FILTER_SANITIZE_STRING);
            $settings = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get([ 'site_name', 'contact_email' ]);

            $mailSender = $this->getParameter('mailer_no_reply_address');

            if (!empty($settings['contact_email'])) {
                //  Build the message
                $text = \Swift_Message::newInstance();
                $text
                    ->setSubject('[' . _('Contribute') . '] ' . $subject)
                    ->setBody($body, 'text/html')
                    ->setTo([ $settings['contact_email'] => $settings['contact_email'] ])
                    ->setFrom([ $mailSender => $settings['site_name'] ])
                    ->setSender([ $mailSender => $settings['site_name'] ]);

                $headers = $text->getHeaders();
                $headers->addParameterizedHeader(
                    'ACUMBAMAIL-SMTPAPI',
                    $this->get('core.instance')->internal_name . ' - Form'
                );

                $path  = $this->getParameter('core.paths.spool.files');
                $file1 = $request->files->get('image1');

                if ($file1) {
                    $file1->move($path, $file1->getClientOriginalName());

                    $text->attach(\Swift_Attachment::fromPath(
                        $path . '/' . $file1->getClientOriginalName(),
                        $file1->getClientMimeType()
                    )->setFilename($file1->getClientOriginalName()));
                }

                $file2 = $request->files->get('image2');

                if ($file2) {
                    $file2->move($path, $file2->getClientOriginalName());

                    $text->attach(\Swift_Attachment::fromPath(
                        $path . '/' . $file2->getClientOriginalName(),
                        $file2->getClientMimeType()
                    )->setFilename($file2->getClientOriginalName()));
                }

                try {
                    $mailer = $this->get('mailer');
                    $mailer->send($text);

                    $this->get('application.log')->notice(
                        "Email sent. Frontend form (From: " . $email . ", to: "
                        . $settings['contact_email']
                    );

                    $class   = 'success';
                    $message = _('The information has been sent');
                } catch (\Exception $e) {
                    $message = _('Sorry, we were unable to complete your request');
                    $this->get('application.log')->notice(
                        "Email NOT sent. Frontend form (From:" . $email
                        . ", to: " . $settings['contact_email'] . "):"
                        . $e->getMessage()
                    );
                }
            } else {
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
     * Loads the list of positions and advertisements on renderer service.
     */
    public function getAds()
    {
        $positionManager = $this->get('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup('article_inner', [ 7, 9 ]);
        $advertisements  = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions);

        $this->get('frontend.renderer.advertisement')
            ->setPositions($positions)
            ->setAdvertisements($advertisements);
    }
}
