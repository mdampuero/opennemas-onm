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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for form send by mail
 *
 * @package Frontend_Controllers
 **/
class FormController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);

        require_once 'recaptchalib.php';

        \Frontend\Controller\StaticPagesController::getAds();

        $this->session = $this->get('session');
        $this->session->start();
    }

    /**
     * Description of the action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        return $this->render('static_pages/form.tpl');
    }

    /**
     * Creates the new subscription given information by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function sendAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            return new RedirectResponse($this->generateUrl('frontend_participa_frontpage'));
        }

        //Get configuration params
        $configRecaptcha = s::get('recaptcha');

        // Get request params
        $verify           = $request->request->filter('security_code', "", FILTER_SANITIZE_STRING);
        $rcChallengeField = $request->request->filter('recaptcha_challenge_field', null, FILTER_SANITIZE_STRING);
        $rcResponseField  = $request->request->filter('recaptcha_response_field', null, FILTER_SANITIZE_STRING);
        $message          = null;
        $class            = "";

        if (empty($verify)) {
            // Get reCaptcha validate response
            $resp = recaptcha_check_answer(
                $configRecaptcha['private_key'],
                $request->getClientIp(),
                $rcChallengeField,
                $rcResponseField
            );

            // What happens when the CAPTCHA was entered incorrectly
            if (!$resp->is_valid) {
                $message = _("The reCAPTCHA wasn't entered correctly. Go back and try it again.");
                $class = 'error';
            } else {

                // Correct CAPTCHA, bad mail and name empty
                $email = $request->request->filter('email', null, FILTER_SANITIZE_STRING);


                if (empty($email)) {
                    $message = _(
                        "Sorry, we were unable to complete your request.\n"
                        ."Check the form and try again"
                    );
                    $message = _("Email is required but will not be published");
                    $class = 'error';
                } else {
                    // Correct CAPTCHA, correct mail and name not empty
                    // check data form is correcty and serialize form
                    $body ='';
                    $notAllowed = array("subject", "cx", "security_code", "submit",
                        "recaptcha_challenge_field", "recaptcha_response_field");
                    foreach ($request->request as $key => $value) {
                        if (!in_array($key, $notAllowed)) {
                            $body .= "<p>$key => $value </p> \n";
                        }
                    }

                    $name      = $request->request->filter('name', '', FILTER_SANITIZE_STRING);
                    $subject   = $request->request->filter('subject', null, FILTER_SANITIZE_STRING);
                    $recipient = $request->request->filter('recipient', null, FILTER_SANITIZE_STRING);


                    $mailSender = s::get('mail_sender');
                    if (empty($mailSender)) {
                        $mailSender = "no-reply@postman.opennemas.com";
                    }
                    //  Build the message
                    $text = \Swift_Message::newInstance();
                    $text
                        ->setSubject($subject)
                        ->setBody($body, 'text/html')
                        ->setTo(array($recipient => $recipient))
                        ->setFrom(array($email => $name))
                        ->setSender(array($mailSender => s::get('site_name')));

                    if (isset($_FILES['image1']) && !empty($_FILES['image1']["name"])) {
                        $file = $_FILES["image1"]["tmp_name"];
                        $filename = $_FILES["image1"]["name"];
                        $type =$_FILES["image1"]["type"];
                        $text->attach(\Swift_Attachment::fromPath($file, $type)->setFilename($filename));

                    }
                    if (isset($_FILES['image2']) && !empty($_FILES['image2']["name"])) {
                        $file = $_FILES["image2"]["tmp_name"];
                        $filename = $_FILES["image2"]["name"];
                        $type =$_FILES["image2"]["type"];
                        $text->attach(\Swift_Attachment::fromPath($file, $type)->setFilename($filename));
                    }

                    try {
                        $mailer = $this->get('mailer');
                        $mailer->send($text);

                        $action = new \Action();
                        $action->set(array('action_name'=>'form_1','counter'=>1));

                        $message = _("The information has been sent");

                        $class   = 'success';

                    } catch (\Swift_SwiftException $e) {
                        $message = _(
                            "Sorry, we were unable to complete your request.\n"
                            ."Check the form and try again"
                        );
                        $class = 'error';
                    }


                }
            }
        }

        return $this->render(
            'static_pages/form.tpl',
            array(
                'message' => $message,
                'class'   => $class,
            )
        );
    }
}
