<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Onm\Framework\Controller\Controller;

/**
 * Handles the actions for the user authentication in backend.
 *
 */
class AuthenticationController extends Controller
{
    /**
     * Displays the login form template.
     */
    public function loginAction(Request $request)
    {
        $error   = null;
        $route   = $request->get('_route');
        $referer = $this->generateUrl('admin_welcome');
        $token = $request->get('token');

        if ($token) {
            $user = $this->get('user_repository')->findBy(
                array(
                    'token' => array(array('value' => $token))
                ),
                array('token' => 'asc'),
                1,
                1
            );

            if (!$user) {
                $request->getSession()->getFlashBag()->add('error', _('Invalid token'));
                return $this->redirect($this->generateUrl('admin_login_form'));
            }

            $user = array_pop($user);
            $user->updateUserToken($user->id, null);
            $token = new UsernamePasswordToken($user, null, 'backend', $user->getRoles());

            $securityContext = $this->get('security.context');
            $securityContext->setToken($token);

            $request = $this->getRequest();
            $session = $request->getSession();
            $session->set('_security_backend', serialize($token));

            if ($this->session->get('_security.backend.target_path')) {
                $referer = $this->session->get('_security.backend.target_path');
            }

            return $this->redirect($this->generateUrl('admin_welcome'));
        }


        if ($this->session->get('_security.backend.target_path')) {
            $referer = $this->session->get('_security.backend.target_path');
        }

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes
                ->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()
                ->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        if ($error) {
            $msg = '';
            if ($error instanceof BadCredentialsException) {
                $msg = _('Username or password incorrect.');
            } elseif ($error instanceof InvalidCsrfTokenException) {
                $msg = _('Login token is not valid. Try to authenticate again.');
            } else {
                $msg = $error->getMessage();
            }

            $this->session->getFlashBag()->add('error', $msg);

            $_SESSION['failed_login_attempts'] =
                isset($_SESSION['failed_login_attempts']) ?
                $_SESSION['failed_login_attempts'] + 1 : 1;
        }

        $intention = time() . rand();
        $token     = $this->get('form.csrf_provider')->generateCsrfToken($intention);

        $this->request->getSession()->set('intention', $intention);

        $currentLanguage  = \Application::$language;

        $failedLoginAttempts =  0;
        if (isset($_SESSION['failed_login_attempts'])) {
            $failedLoginAttempts = $_SESSION['failed_login_attempts'];
        }

        return $this->render(
            'login/login.tpl',
            array(
                'failed_login_attempts' => $failedLoginAttempts,
                'current_language'      => $currentLanguage,
                'token'                 => $token,
                'referer'               => $referer
            )
        );
    }
}
