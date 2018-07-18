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
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Handles the actions for newsletter subscriptions
 *
 * @package Frontend_Controllers
 */
class NewsletterController extends Controller
{
    /**
     * Shows a newsletter publicly
     *
     * @param int $id the id of the newsletter to show
     *
     * @return Response the response given to the user
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')")
     **/
    public function showAction($id = null)
    {
        $item = $this->get('api.service.newsletter')->getItem($id);

        if (!is_object($item) || $item->type == 1) {
            throw new ResourceNotFoundException();
            // $item->html = $this->get('core.renderer.newsletter')->render($item->contents);
        }

        $internalName = $this->get('core.instance')->internal_name;
        return new Response($item->html, 200, [
            'x-instance' => $internalName,
            'x-tags'     => 'instance-' . $internalName . ',newsletter-' . $id,
        ]);
    }

    /**
     * Shows the subscription form if the newsletter management is external if not
     * it redirects to the /user/register action
     *
     * @return Response the response given to the user
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')")
     */
    public function subscribeAction()
    {
        // If newsletter manager is internal then redirect to user register action
        $subscriptionType = $this->get('core.helper.newsletter')->getSubscriptionType();
        if ($subscriptionType === 'create_subscriptor') {
            return $this->redirect(
                $this->generateUrl('frontend_user_register', [ 'target' => 'newsletter', ]),
                301
            );
        }

        // Redirect to configured url if subscription type is ActOn
        if ($subscriptionType === 'acton') {
            $setting = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('actOn.formPage', null);
            if (!empty($setting)) {
                return $this->redirect($setting);
            }
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
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')")
     */
    public function createSubscriptionAction(Request $request)
    {
        // If the request is not HTTP POST then redirect back to the form
        if ('POST' != $request->getMethod()) {
            return new RedirectResponse($this->generateUrl('frontend_newsletter_subscribe_show'));
        }

        $nh = $this->get('core.helper.newsletter');

        // If newsletter manager is internal then redirect to user register action
        $subscriptionType = $nh->getSubscriptionType();
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
            $this->get('core.helper.newsletter_sender')->sendSubscriptionMail($data);

            $rs = ['class' => 'success'];
            if ($data['subscription'] == 'alta') {
                $rs['message'] = _("You have been subscribed to the newsletter.");
            } else {
                $rs['message'] = _("You have been unsubscribed from the newsletter.");
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
