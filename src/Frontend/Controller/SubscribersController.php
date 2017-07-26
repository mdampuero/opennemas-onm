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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for newsletter subscriptions
 *
 * @package Frontend_Controllers
 */
class SubscribersController extends Controller
{
    /**
     * Shows the subscription form
     *
     * @return void
     */
    public function showAction()
    {
        list($positions, $advertisements) = $this->getAds();

        return $this->render('static_pages/subscription.tpl', [
            'ads_positions'   => $positions,
            'advertisements'  => $advertisements,
            'actual_category' => 'newsletter',
            'recaptcha' => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
        ]);
    }

    /**
     * Creates the new subscription given information by POST
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
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

        $response  = $request->request->filter('g-recaptcha-response', null, FILTER_SANITIZE_STRING);

        // Check current recaptcha
        $isValid = $this->get('core.recaptcha')
            ->configureFromSettings()
            ->isValid($response, $request->getClientIp());

        // Set default values to return
        $rs = [ 'class' => null, 'message' => null ];

        // Check verify for bots
        if (!$isValid) {
            $rs = [
                'message' => _(
                    _("The reCAPTCHA wasn't entered correctly. Go back and try it again.")
                ),
                'class' => 'error'
            ];
        } else {
            // Check name and email
            if (!empty($data['email']) && !empty($data['name'])) {
                if ($action == 'create_subscriptor') {
                    $rs = $this->createSubscription($data);
                } elseif ($action == 'submit') {
                    $rs = $this->sendSubscriptionMail($request, $data);
                }
            } else {
                $rs = [
                    'message' => _("Check the form and try again"),
                    'class' => 'error'
                ];
            }
        }

        return $this->render('static_pages/subscription.tpl', [
            'message'         => $rs['message'],
            'actual_category' => 'newsletter',
            'class'           => $rs['class'],
            'recaptcha'       => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
        ]);
    }

    /**
     * Sends an email with the new subscription data
     *
     * @param Array $data Data for subscription
     *
     * @return Array Message and class to show the user
     */
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

        // Get configuration params
        $settings = $this->get('setting_repository')->get([
            'site_name',
            'newsletter_maillist'
        ]);

        $configSiteName = $settings['site_name'];
        $configMailTo = $settings['newsletter_maillist'];

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
            ->setSender([
                'no-reply@postman.opennemas.com' => $this->get('setting_repository')->get('site_name')
            ]);

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
     */
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
     */
    public function getAds()
    {
        // Get letter positions
        $positionManager = $this->get('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup('article_inner', array(7, 9));
        $advertisements  = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, 0);

        return [ $positions, $advertisements ];
    }
}
