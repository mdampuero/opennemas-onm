<?php

namespace BackendWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

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
        $user = $this->get('core.security.user');

        $user->terms_accepted = null;

        if ($request->get('accept') && $request->get('accept') === 'true') {
            $user->terms_accepted =
                new \DateTime(null, new \DateTimeZone('UTC'));
        }

        $em->persist($user, $user->getOrigin());

        return new JsonResponse();
    }
}
