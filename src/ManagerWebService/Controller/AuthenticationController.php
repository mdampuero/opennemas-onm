<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManagerWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Onm\Framework\Controller\Controller;

class AuthenticationController extends Controller
{
    /**
     * Returns the parameters to use in login.
     *
     * @return JsonResponse The response object.
     */
    public function loginAction(Request $request)
    {
        $error   = null;
        $route   = $request->get('_route');
        $referer = $this->generateUrl('manager_welcome');
        $message = array();

        if ($request->getSession()->get('_security.manager.target_path')) {
            $referer = $this->request->getSession()->get('_security.manager.target_path');
        }

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes
                ->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()
                ->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        if ($error) {
            if ($error instanceof BadCredentialsException) {
                $message = array(
                    'type' => 'error',
                    'text' => _('Username or password incorrect.')
                );
            } elseif ($error instanceof InvalidCsrfTokenException) {
                $message = array(
                    'type' => 'error',
                    'text' => _('Login token is not valid. Try to authenticate again.')
                );
            } else {
                $message = array(
                    'type' => 'error',
                    'text' => _($error->getMessage())
                );
            }

            $attempts = $request->getSession()->get('failed_login_attempts');
            if ($attempts) {
                $request->getSession()->get('failed_login_attempts', $attempts + 1);
            } else {
                $request->getSession()->get('failed_login_attempts', 1);
            }
        }


        $intention = time() . rand();
        $token     = $this->get('form.csrf_provider')->generateCsrfToken($intention);

        $request->getSession()->set('intention', $intention);

        $currentLanguage = \Application::$language;

        $failedLoginAttempts =  0;
        if (isset($_SESSION['failed_login_attempts'])) {
            $failedLoginAttempts =  $request->getSession()->get('failed_login_attempts');
        }

        return new JsonResponse(
            array(
                'attempts'         => $failedLoginAttempts,
                'current_language' => $currentLanguage,
                'token'            => $token,
                'referer'          => $referer,
                'languages'        => $this->container->getParameter('available_languages'),
                'message'          => $message,
            )
        );
    }
}
