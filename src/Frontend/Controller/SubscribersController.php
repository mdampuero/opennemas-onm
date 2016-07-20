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

/**
 * Handles the actions for newsletter subscriptions
 *
 * @package Frontend_Controllers
 **/
class SubscribersController extends Controller
{
    /**
     * Shows the subscription form
     *
     * @return void
     **/
    public function showAction()
    {
        $ads = $this->getAds();

        return $this->render('static_pages/subscription.tpl', [
            'advertisements'  => $ads,
            'actual_category' => 'newsletter'
        ]);
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

        // Get request params
        $verify = $request->request->filter('verify', '', FILTER_SANITIZE_STRING);
        $action = $request->request->filter('action', '', FILTER_SANITIZE_STRING);
        $data = [
            'email'        => $request->request->filter('email', '', FILTER_SANITIZE_STRING),
            'name'         => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'subscription' => $request->request->filter('subscription', '', FILTER_SANITIZE_STRING),
        ];

        // Set default values to return
        $message = null;
        $class   = '';

        // Check verify for bots
        if (empty($verify)) {
            // Check reCaptcha
            $message = _("The reCAPTCHA wasn't entered correctly. Go back and try it again.");
            $class = 'error';
            if ($this->checkRecaptcha($request)) {
                $message = _(
                    "Sorry, we were unable to complete your request.\n"
                    ."Check the form and try again"
                );
                // Check name and email
                if (!empty($data['email']) && !empty($data['name'])) {
                    if ($action == 'create_subscriptor') {
                        $rs = $this->createSubscription($data);
                    } elseif ($action == 'submit') {
                        $rs = $this->sendSubscriptionMail($request, $data);
                    }
                }
            }
        }

        return $this->render(
            'static_pages/subscription.tpl',
            [
                'message'         => $rs['message'],
                'actual_category' => 'newsletter',
                'class'           => $rs['class'],
            ]
        );
    }

    /**
     * Check if recaptcha is valid
     *
     * @return $valid bool
     **/
    public function checkRecaptcha($request)
    {
        $rcChallengeField = $request->request->filter('recaptcha_challenge_field', '', FILTER_SANITIZE_STRING);
        $rcResponseField  = $request->request->filter('recaptcha_response_field', '', FILTER_SANITIZE_STRING);
        $configRecaptcha  = $this->get('setting_repository')->get('recaptcha');

        // Check new and old reCAPTCHA
        $valid = false;
        $response = $request->get('g-recaptcha-response');
        if (!is_null($response)) {
            $rs = $this->get('google_recaptcha');
            $recaptcha = $rs->getPublicRecaptcha();
            $resp = $recaptcha->verify(
                $request->get('g-recaptcha-response'),
                $request->getClientIp()
            );

            $valid = $resp->isSuccess();
        } else {
            $captcha = $this->get('recaptcha')
                ->setPrivateKey($configRecaptcha['private_key'])
                ->setRemoteIp($request->getClientIp());

            $resp = $captcha->check($rcChallengeField, $rcResponseField);
            $valid = $resp->isValid();
        }

        return $valid;
    }

    /**
     * Sends an email with the new subscription data
     *
     * @param Array $data Data for subscription
     *
     * @return Array Message and class to show the user
     **/
    public function sendSubscriptionMail($request, $data)
    {
        // Get extra parameters
        $data['subscritorEntity']    = $request->request->filter('entity', '', FILTER_SANITIZE_STRING);
        $data['subscritorCountry']   = $request->request->filter('country', '', FILTER_SANITIZE_STRING);
        $data['subscritorCommunity'] = $request->request->filter('community', '', FILTER_SANITIZE_STRING);

        // Build mail body
        $text = "Nombre y Apellidos: ". $data['name']." \r\n".
            "Email: ".$data['email']." \r\n";
        if (!empty($data['subscritorEntity'])) {
            $text.= "Entidad: ".$data['subscritorEntity']." \n";
        }
        if (!empty($data['subscritorCountry'])) {
            $text.= "País: ".$data['subscritorCountry']." \n";
        }
        if (!empty($data['subscritorCommunity'])) {
            $text.= "Provincia de Origen: ".$data['subscritorCommunity']." \n";
        }

        //Get configuration params
        $sr = $this->get('setting_repository');
        $configSiteName  = $sr->get('site_name');
        $configMailTo    = $sr->get('newsletter_maillist');

        // Checking the type of action to do (alta/baja)
        if ($data['subscription'] == 'alta') {
            $subject = utf8_decode("Solicitud de ALTA - Boletín ".$configSiteName);
            $body    =  "Solicitud de Alta en el boletín de: \r\n". $text;

            $message = _("You have been subscribed to the newsletter.");
        } else {
            $subject = utf8_decode("Solicitud de BAJA - Boletín ".$configSiteName);
            $body    =  "Solicitud de Baja en el boletín de: \r\n". $text;

            $message = _("You have been unsusbscribed from the newsletter.");
        }

        //  Build the message
        $mail = \Swift_Message::newInstance();
        $mail
            ->setSubject($subject)
            ->setBody(utf8_decode($body), 'text/html')
            ->setBody(strip_tags(utf8_decode($body)), 'text/plain')
            ->setTo(array($configMailTo['subscription'] => _('Subscription form')))
            ->setFrom(array($data['email'] => $data['name']))
            ->setSender(array('no-reply@postman.opennemas.com' => $sr->get('site_name')));

        try {
            $mailer = $this->get('mailer');
            $mailer->send($mail);
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

        return [
            'message' => $message,
            'class'   => $class
        ];
    }

    /**
     * Creates a new subscripcion
     *
     * @param Array $data Data for subscription
     *
     * @return Array Message and class to show the user
     **/
    public function createSubscription($data)
    {
        $user = new \Subscriber();
        if ($data['subscription'] == 'alta') {
            if ($user->existsEmail($data['email'])) {
                $data['subscription'] = 1;
                $data['status'] = 2;

                $user = $user->getUserByEmail($data['email']);
                $data['id'] = $user->id;

                if ($user->update($data)) {
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
                $data['subscription'] = 1;
                $data['status'] = 2;

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
            if ($user->existsEmail($data['email'])) {
                $data['subscription'] = 0;
                $data['status'] = 3;

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

        return [
            'message' => $message,
            'class'   => $class
        ];
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
        $positionManager = $this->get('core.manager.advertisement');
        $positions = $positionManager->getPositionsForGroup('article_inner', array(7, 9));

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
