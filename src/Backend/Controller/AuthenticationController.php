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
use Onm\Settings as s;

/**
 * Handles the actions for the user authentication in backend.
 *
 */
class AuthenticationController extends Controller
{
    /**
     * Displays the login form template.
     *
     * @return Response The response object.
     */
    public function loginAction(Request $request)
    {
        $error   = null;
        $route   = $request->get('_route');
        $referer = $this->generateUrl('admin_welcome');
        $session = $request->getSession();
        $token   = $request->get('token');

        $session->set('login_callback', $referer);

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
                return $this->redirect($this->generateUrl('admin_login'));
            }

            $user = array_pop($user);
            $user->updateUserToken($user->id, null);
            $token = new UsernamePasswordToken($user, null, 'backend', $user->getRoles());

            $securityContext = $this->get('security.context');
            $securityContext->setToken($token);

            $request = $this->getRequest();
            $session->set('_security_backend', serialize($token));

            if ($session->get('_security.backend.target_path')) {
                $referer = $session->get('_security.backend.target_path');
            }

            // Set last_login date
            $time = new \DateTime();
            $time->setTimezone(new \DateTimeZone('UTC'));
            $time = $time->format('Y-m-d H:i:s');

            if (!$user->isMaster()) {
                s::set('last_login', $time);
            }

            return $this->redirect($this->generateUrl('admin_welcome'));
        }


        if ($session->get('_security.backend.target_path')) {
            $referer = $session->get('_security.backend.target_path');
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

            $session->getFlashBag()->add('error', $msg);

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
                'referer'               => $referer,
                'languages'             => $this->container->getParameter('available_languages')
            )
        );
    }

    /**
     * Displays a popup after login/connect with social accounts.
     *
     * @return Response The response object.
     */
    public function loginCallbackAction(Request $request)
    {
        $redirect = $request->getSession()->get('_security.backend.target_path');

        if ($redirect == '/admin/login/callback') {
            return $this->render('common/close_popup.tpl');
        } elseif (!empty($redirect)) {
            return $this->redirect($redirect);
        } else {
            return $this->redirect($this->generateUrl('admin_welcome'));
        }
    }
}
