<?php
/**
 * Handles all the request for Welcome actions
 *
 * @package Manager_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Manager\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\SecurityContext;

use Onm\Framework\Controller\Controller;

/**
 * Handles all the request for Welcome actions
 *
 * @package Manager_Controllers
 **/
class AuthenticationController extends Controller
{
    /**
     * Displays the login form template.
     */
    public function loginAction(Request $request)
    {
        $error   = null;
        $route   = $request->get('_route');
        $referer = $this->generateUrl('manager_welcome');

        if ($this->session->get('_security.manager.target_path')) {
            $referer = $this->session->get('_security.manager.target_path');
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

        $token = $this->get('form.csrf_provider')
            ->generateCsrfToken('manager_authenticate');
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
