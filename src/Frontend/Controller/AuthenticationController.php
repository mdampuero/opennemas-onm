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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\SecurityContext;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the user authentication in frontend.
 */
class AuthenticationController extends Controller
{
    /**
     * Displays the login form template.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function loginAction(Request $request)
    {
        $error   = null;
        $referer = $request->query->filter('referer', '', FILTER_SANITIZE_STRING);
        $session = $request->getSession();

        if (empty($referer)) {
            $referer = $this->generateUrl('frontend_frontpage');
        }

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        if ($error) {
            if ($error instanceof BadCredentialsException) {
                $msg = _('Username or password incorrect.');
            } elseif ($error instanceof InvalidCsrfTokenException) {
                $msg = _('Login token is not valid. Try to authenticate again.');
            } else {
                $msg = $error->getMessage();
            }

            $session->getFlashBag()->add('error', $msg);
            $session->set('failed_login_attempts', $session->get('failed_login_attempts') + 1);
        }

        // Generate CSRF token
        $intention = time() . rand();
        $token     = $this->get('security.csrf.token_manager')->getToken($intention);

        $session->set('intention', $intention);

        return $this->render('authentication/login.tpl', [
            'failed_login_attempts' => $session->get('failed_login_attempts'),
            'token'                 => $token,
            'referer'               => $referer
        ]);
    }
}
