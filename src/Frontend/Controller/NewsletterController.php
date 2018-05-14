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

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for newsletter subscriptions
 *
 * @package Frontend_Controllers
 */
class NewsletterController extends Controller
{
    /**
     * Shows the subscription form if the newsletter management is external if not
     * it redirects to the /user/register action
     *
     * @return Response the response given to the user
     */
    public function subscribeAction()
    {
        // If newsletter manager is internal then redirect to user register action
        $subscriptionType = $this->get('setting_repository')->get('newsletter_subscriptionType');
        if ($subscriptionType === 'create_subscriptor') {
            return $this->redirect(
                $this->generateUrl('frontend_user_register', [ 'target' => 'newsletter', ]),
                301
            );
        }

        list($positions, $advertisements) = $this->getAds();

        return $this->render('static_pages/subscription.tpl', [
            'actual_category' => 'newsletter',
            'ads_positions'   => $positions,
            'advertisements'  => $advertisements,
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
    public function createSubscriptionAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            return new RedirectResponse($this->generateUrl('frontend_newsletter_subscribe_show'));
        }

        // If newsletter manager is internal then redirect to user register action
        $subscriptionType = $this->get('setting_repository')->get('newsletter_subscriptionType');
        if ($subscriptionType === 'create_subscriptor') {
            return $this->redirect(
                $this->generateUrl('frontend_user_register', [ 'target' => 'newsletter', ]),
                301
            );
        }

        // Get request params
        $captchaResponse = $request->request->filter('g-recaptcha-response', null, FILTER_SANITIZE_STRING);
        $data            = [
            'email'               => $request->request->filter('email', '', FILTER_SANITIZE_STRING),
            'name'                => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'subscription'        => $request->request->filter('subscription', '', FILTER_SANITIZE_STRING),
            // Garbage from cronicas
            'subscritorEntity'    => $request->request->filter('entity', '', FILTER_SANITIZE_STRING),
            'subscritorCountry'   => $request->request->filter('country', '', FILTER_SANITIZE_STRING),
            'subscritorCommunity' => $request->request->filter('community', '', FILTER_SANITIZE_STRING),
        ];
        // Get extra parameters

        try {
            // Recaptcha is not valid
            if (!$this->get('core.recaptcha')->configureFromSettings()
                ->isValid($captchaResponse, $request->getClientIp())
            ) {
                throw new \Exception(_("The reCAPTCHA wasn't entered correctly. Go back and try it again."));
            }

            // Data is not valid
            if (empty($data['email']) || empty($data['name'])) {
                throw new \Exception(_("Check the form and try again"));
            }

            // Create the subscription
            $this->get('core.helper.newsletter')->sendSubscriptionMail($data);

            $rs = ['class' => 'success'];
            if ($data['subscription'] == 'alta') {
                $rs['message'] = _("You have been subscribed to our newsletter.");
            } else {
                $rs['message'] = _("You have been unsubscribed from our newsletter.");
            }
        } catch (\Exception $e) {
            $rs = [
                'message' => $e->getMessage(),
                'class' => 'error'
            ];
        }

        list($positions, $advertisements) = $this->getAds();

        return $this->render('static_pages/subscription.tpl', [
            'actual_category' => 'newsletter',
            'class'           => $rs['class'],
            'message'         => $rs['message'],
            'ads_positions'   => $positions,
            'advertisements'  => $advertisements,
            'recaptcha' => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
        ]);
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
        $positions       = $positionManager->getPositionsForGroup('article_inner', [ 7, 9 ]);
        $advertisements  = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, 0);

        return [ $positions, $advertisements ];
    }
}
