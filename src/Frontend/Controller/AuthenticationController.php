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

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Handles the actions for the user authentication in frontend.
 */
class AuthenticationController extends Controller
{
    /**
     * Checks if the current user  is authenticated.
     *
     * @return Response The response object.
     */
    public function authenticatedAction()
    {
        if (!empty($this->get('core.user'))) {
            return new Response('', 200);
        }

        return new Response('', 401);
    }

    /**
     * Displays the login form template.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function loginAction(Request $request)
    {
        $auth    = $this->get('core.security.authentication');
        $error   = null;
        $referer = $request->query->filter('referer', '', FILTER_SANITIZE_STRING);
        $session = $request->getSession();

        if (empty($referer)) {
            $referer = $this->generateUrl('frontend_frontpage');
        }

        if ($auth->hasError()) {
            $auth->failure();

            $error = $auth->getErrorMessage();

            $session->getFlashBag()->add('error', $error);
        }

        return $this->render('authentication/login.tpl', [
            'recaptcha' => $auth->getRecaptchaFromSettings(),
            'token'     => $auth->getCsrfToken(),
            'referer'   => $referer
        ]);
    }
}
