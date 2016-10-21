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
     * Returns an empty response on fake login.
     *
     * @return JsonResponse The response object.
     */
    public function fakeLoginAction()
    {
        return new JsonResponse();
    }

    /**
     * Returns the parameters to use in login.
     *
     * @return JsonResponse The response object.
     */
    public function loginAction(Request $request)
    {
        $error   = null;
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
                    'text' => _($error)
                );
            }

            $attempts = $request->getSession()->get('failed_login_attempts');
            if ($attempts) {
                $request->getSession()->set('failed_login_attempts', $attempts + 1);
            } else {
                $request->getSession()->set('failed_login_attempts', 1);
            }
        }

        $errors = $request->getSession()->getFlashbag()->get('error');
        if ($errors) {
            $message = array(
                'type' => 'error',
                'text' => $errors[0]
            );
        }

        $intention = time() . rand();
        $token     = $this->get('security.csrf.token_manager')->getToken($intention);

        $request->getSession()->set('intention', $intention);

        $failedLoginAttempts = $request->getSession()->get('failed_login_attempts') ? : 0;

        return new JsonResponse([
            'attempts' => $failedLoginAttempts,
            'locale'   => $this->get('core.locale')->getLocale(),
            'token'    => $token,
            'referer'  => $referer,
            'locales'  => $this->get('core.locale')->getLocales(),
            'message'  => $message,
        ]);
    }

    /**
     * Returns the security data.
     *
     * @return JsonResponse The response object.
     */
    public function refreshAction()
    {
        return new JsonResponse([
            'instance'    => $this->get('core.instance')->getData(),
            'instances'   => $this->get('core.security')->getInstances(),
            'permissions' => array_values($this->get('core.security')->getPermissions()),
            'user'        => $this->get('core.user')->getData(),
        ]);
    }
}
