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

use Api\Exception\GetItemException;
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
     */
    public function showAction($id = null)
    {
        try {
            $item = $this->get('api.service.newsletter')->getItem($id);
        } catch (GetItemException $e) {
            throw new ResourceNotFoundException();
        }

        return new Response($item->html, 200, [
            'x-tags'      => 'newsletter-' . $id,
            'x-cacheable' => true,
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

        $this->getAds();

        return $this->render('static_pages/subscription.tpl', [
            'recaptcha' => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
        ]);
    }

    public function unsubscribeAction(Request $request)
    {
        try {
            $email           = base64_decode($request->get('email_hash'));
            $listId          = $request->get('list_id');
            $newsletterLists = $this->get('core.helper.newsletter')->getRecipients();
            $user            = $this->get('api.service.subscriber')->getItemby(sprintf('email = "%s"', $email));

            $userGroups = array_filter($user->user_groups, function ($element) {
                return $element['status'] == 1;
            });

            $userGroups = array_map(function ($element) {
                return $element['user_group_id'];
            }, $userGroups);

            $susbcribedNewsletters = array_filter($newsletterLists, function ($list) use ($userGroups) {
                return in_array($list['id'], $userGroups);
            });

            $susbcribedNewsletter = array_filter($susbcribedNewsletters, function ($newsletter) use ($listId) {
                return $newsletter['id'] == $listId;
            });

            if (empty($susbcribedNewsletter)) {
                return new RedirectResponse($this->get('router')->generate('frontend_frontpage'));
            }

            $susbcribed         = array_pop($susbcribedNewsletter);
            $susbcribedListName = $susbcribedNewsletter['name'];
            $unsubscribedId     = $susbcribed['id'];
            $finalUserGropus    = array_filter($user->user_groups, function ($item) use ($unsubscribedId) {
                return $item['user_group_id'] != $unsubscribedId;
            });

            $this->get('api.service.subscriber')->patchItem($user->id, [ 'user_groups' => $finalUserGropus ]);
            return $this->render('user/unsubscribe_completed.tpl', [
                'email' => $email,
                'lists' => [ $susbcribedListName ]
            ]);
        } catch (\Exception $e) {
            return new RedirectResponse($this->get('router')->generate('frontend_frontpage'));
        }
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
        $captchaResponse = $request->request->filter('g-recaptcha-response');
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

        $this->getAds();

        return $this->render('static_pages/subscription.tpl', [
            'class'     => $rs['class'],
            'message'   => $rs['message'],
            'recaptcha' => $this->get('core.recaptcha')
                ->configureFromSettings()
                ->getHtml(),
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
