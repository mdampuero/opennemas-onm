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

        require_once 'recaptchalib.php';

        \Frontend\Controller\StaticPagesController::getAds();
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
        $ads = $this->getAds();

        $this->view->assign(
            array(
                'advertisements'  => $ads,
                'actual_category' => 'newsletter'
            )
        );

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
                $email  = $request->request->filter('email', null, FILTER_SANITIZE_STRING);
                $name   = $request->request->filter('name', null, FILTER_SANITIZE_STRING);
                $type = $request->request->filter('subscription', null, FILTER_SANITIZE_STRING);

                if ($type == 'alta' && (empty($email) || empty($name))) {
                    $message = _(
                        "Sorry, we were unable to complete your request.\n"
                        ."Check the form and try again"
                    );
                    $class = 'error';
                } elseif ($type == 'baja' && empty($email)) {
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
                    $data['subscription']        = $type;
                    $data['subscritorEntity']    = $request->request->filter('entity', null, FILTER_SANITIZE_STRING);
                    $data['subscritorCountry']   = $request->request->filter('country', null, FILTER_SANITIZE_STRING);
                    $data['subscritorCommunity'] = $request->request->filter('community', null, FILTER_SANITIZE_STRING);

                    $user = new \Subscriptor();
                    // Check for repeated e-mail

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

                            //  Build the message
                            $text = \Swift_Message::newInstance();
                            $text
                                ->setSubject($subject)
                                ->setBody(utf8_decode($body), 'text/html')
                                ->setBody(strip_tags(utf8_decode($body)), 'text/plain')
                                ->setTo(array($configMailTo['subscription'] => _('Subscription form')))
                                ->setFrom(array($data['email'] => $data['name']))
                                ->setSender(array('no-reply@postman.opennemas.com' => s::get('site_name')));

                            try {
                                $mailer = $this->get('mailer');
                                $mailer->send($text);
                                if ($data['subscription'] == 'alta') {
                                    $message = _("You have been subscribed to our newsletter.");
                                } else {
                                    $message = _("You have been unsubscribed from our newsletter.");
                                }
                                $class   = 'success';

                            } catch (\Swift_SwiftException $e) {
                                $message = _(
                                    "Sorry, we were unable to complete your request.\n"
                                    ."Check the form and try again"
                                );
                                $class = 'error';
                            }

                            break;
                        case 'create_subscriptor':
                            if ($data['subscription'] == 'alta') {
                                if ($user->exists_email($data['email'])) {
                                    $message = _("Sorry, that email is already subscribed to our newsletter");
                                    $class = 'error';
                                } else {
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
                                }
                            } else {
                                if ($user->exists_email($data['email'])) {
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
                                } else {
                                    $message = _("Sorry, that email is not in our database");
                                    $class = 'error';
                                }
                            }

                            break;
                    }

                }
            }
        }

        return $this->render(
            'static_pages/subscription.tpl',
            array(
                'message' => $message,
                'actual_category' => 'newsletter',
                'class'   => $class,
            )
        );
    }

    /**
     * Returns the advertisements for the subscription page
     *
     * @return void
     **/
    public function getAds()
    {
        $category = 0;

        // Get letter positions
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('article_inner', array(7, 9));

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
