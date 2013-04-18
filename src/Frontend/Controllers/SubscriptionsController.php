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
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for newsletter subscriptions
 *
 * @package Frontend_Controllers
 **/
class SubscriptionsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);

        require_once SITE_VENDOR_PATH."/phpmailer/class.phpmailer.php";
        require_once 'recaptchalib.php';

        \Frontend\Controllers\StaticPagesController::getAds();

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
    public function showAction(Request $request)
    {
        return $this->render('static_pages/subscription.tpl');
    }

    /**
     * Creates the new subscription given information by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            return new RedirectResponse($this->generateUrl('frontend_newsletter_subscribe_show'));
        }
        //Get configuration params
        $configRecaptcha = s::get('recaptcha');
        $configSiteName  = s::get('site_name');
        $configMailTo    = s::get('newsletter_maillist');

        // Get request params
        $action           = $request->request->filter('action', null, FILTER_SANITIZE_STRING);
        $verify           = $request->request->filter('verify', "", FILTER_SANITIZE_STRING);
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
                $name  = $request->request->filter('name', null, FILTER_SANITIZE_STRING);

                if (empty($email) || empty($name)) {
                    $message = _(
                        "Sorry, we were unable to complete your request.\n"
                        ."Check the form and try again"
                    );
                    $class = 'error';
                } else {
                    // Correct CAPTCHA, correct mail and name not empty

                    //Filter $_POST vars from request
                    $data['name']                = $name;
                    $data['email']               = $email;
                    $data['subscription'] = $request->request->filter('subscription', null, FILTER_SANITIZE_STRING);
                    $data['subscritorEntity']    = $request->request->filter('entity', null, FILTER_SANITIZE_STRING);
                    $data['subscritorCountry']   = $request->request->filter('country', null, FILTER_SANITIZE_STRING);
                    $data['subscritorCommunity'] = $request->request->filter('community', null, FILTER_SANITIZE_STRING);

                    $user = new \Subscriptor();
                    // Check for repeated e-mail
                    if ($user->exists_email($data['email'])) {
                        $message = _("Sorry, that email is already subscribed to our newsletter");
                        $class = 'error';
                    } else {
                        switch ($action) {
                            // Logic for subscription sending a mail to s::get('newsletter_maillist')
                            case 'submit':

                                // Build mail body
                                $formulario= "Nombre y Apellidos: ". $data['name']." \r\n".
                                    "Email: ".$data['email']." \r\n";
                                if (!empty($data['subscritorEntity'])) {
                                    $formulario.= "Entidad: ".$data['subscritorEntity']." \n";
                                }
                                if (!empty($data['subscritorCountry'])) {
                                    $formulario.= "País: ".$data['subscritorCountry']." \n";
                                }
                                if (!empty($data['subscritorCommunity'])) {
                                    $formulario.= "Provincia de Origen: ".$data['subscritorCommunity']." \n";
                                }

                                // Checking the type of action to do (alta/baja)
                                if ($data['subscription'] == 'alta') {
                                    $subject = utf8_decode("Solicitud de ALTA - Boletín ".$configSiteName);
                                    $body    =  "Solicitud de Alta en el boletín de: \r\n". $formulario;

                                    $message = _("You have been subscribed to the newsletter.");
                                    $class = 'success';
                                } else {
                                    $subject = utf8_decode("Solicitud de BAJA - Boletín ".$configSiteName);
                                    $body    =  "Solicitud de Baja en el boletín de: \r\n". $formulario;

                                    $message = _("You have been unsusbscribed from the newsletter.");
                                    $class = 'success';
                                }

                                //Send mail
                                $to = $configMailTo['subscription'];

                                $mail = new \PHPMailer();
                                $mail->SetLanguage('es');
                                $mail->IsSMTP();
                                $mail->Host = MAIL_HOST;
                                $mail->Username = MAIL_USER;
                                $mail->Password = MAIL_PASS;

                                if (!empty($mail->Username) && !empty($mail->Password)) {
                                    $mail->SMTPAuth = true;
                                } else {
                                    $mail->SMTPAuth = false;
                                }

                                $mail->Subject = $subject;
                                $mail->From = $data['email'];
                                $mail->FromName = utf8_decode($data['name']);
                                $mail->Body = utf8_decode($body);

                                $mail->AddAddress($to, $to);

                                if (!$mail->Send()) {
                                    $message = _(
                                    "Sorry, we were unable to complete your request.\n"
                                    ."Check the form and try again"
                                );
                                    $class = 'error';
                                }
                                break;
                            case 'create_subscriptor':

                                if ($data['subscription'] == 'alta') {
                                    $data['subscription'] = 1;
                                    $data['status'] = 2;

                                    $user = new \Subscriptor();

                                    if ($user->create($data)) {
                                        $message = _("You have been subscribed to our newsletter.");
                                        $class = 'success';
                                    } else {
                                        $message = _(
                                            "Sorry, we were unable to complete your request.\n"
                                            ."Check the form and try again"
                                        );
                                        $class = 'error';
                                    }
                                } else {
                                    $data['subscription'] = 0;
                                    $data['status'] = 3;

                                    $user = new \Subscriptor();
                                    $user = $user->getUserByEmail($data['email']);
                                    $data['id'] = $user->id;

                                    if ($user->update($data)) {
                                        $message = _("You have been unsubscribed from our newsletter");
                                        $class = 'success';
                                    } else {
                                        $message = _(
                                            "Sorry, we were unable to complete your request.\n"
                                            ."Check the form and try again"
                                        );
                                        $class = 'error';
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        }

        return $this->render(
            'static_pages/subscription.tpl',
            array(
                'message' => $message,
                'class'   => $class,
            )
        );
    }
}
