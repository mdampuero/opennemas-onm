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
        $user = $this->getUser();

        if ($user->isMaster()) {
            $GLOBALS['application']->conn->selectDatabase('onm-instances');
        }

        if ($request->get('accept') && $request->get('accept') === 'true') {
            $date = new \DateTime(null, new \DateTimeZone('UTC'));

            $newMeta = array('terms_accepted' => $date->format('Y-m-d H:i:s'));
            $user->setMeta($newMeta);

            $user->meta = array_merge($user->meta, $newMeta);
        } else {
            $user->deleteMetaKey($user->id, 'terms_accepted');
        }

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
