<?php

namespace BackendWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

/**
 * Handles requests for Getting Started.
 */
class GettingStartedController extends Controller
{
    /**
     * Accept the terms of use for the current user.
     *
     * @param Request  $request The request object.
     *
     * @return Response The response object.
     */
    public function acceptTermsAction(Request $request)
    {
        $em   = $this->get('orm.manager');
        $user = $em->getRepository('User', 'instance')
            ->find($this->getUser()->id);

        if (empty($user)) {
            $user = $em->getRepository('User', 'manager')
                ->find($this->getUser()->id);
        }

        if ($request->get('accept') && $request->get('accept') === 'true') {
            $user->terms_accepted =
                new \DateTime(null, new \DateTimeZone('UTC'));
        } else {
            $user->terms_accepted = null;
            $user->deleteMetaKey($user->id, 'terms_accepted');
        }

        $em->persist($user, $user->getOrigin());

        return new JsonResponse();
    }

    /**
     * Saves the payment information.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function savePaymentInfoAction(Request $request)
    {
        $billing = $request->request->get('billing');

        if (!empty($billing)) {
            $instance = $this->get('core.instance');
            $instance->metas['billing'] = $billing;
            $this->get('instance_manager')->persist($instance);
        }

        return new JsonResponse();
    }
}
