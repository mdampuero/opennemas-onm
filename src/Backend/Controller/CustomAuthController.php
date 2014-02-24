<?php

namespace Backend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Onm\Framework\Controller\Controller;

/**
 * Auth controller.
 *
 */
class CustomAuthController extends Controller
{
    /**
     * Bad credentials exception class name.
     */
    const BAD_CREDENTIALS =
        'Symfony\Component\Security\Core\Exception\BadCredentialsException';

    /**
     * Invalid token exception class name.
     */
    const INVALID_TOKEN =
        'Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException';

    /**
     * Displays the login form template.
     */
    public function loginAction(Request $request)
    {
        $error   = null;
        $referer = $this->session->get('_security.backend.target_path');

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes
                ->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()
                ->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        if ($error) {
            $msg = '';
            if (is_a($error, self::BAD_CREDENTIALS)) {
                $msg = 'Username or password incorrect.';
            } else if (is_a($error, self::INVALID_TOKEN)) {
                $msg = 'Login token is not valid. Try to authenticate again.';
            } else {
                $msg = $error->getMessage();
            }

            $this->session->getFlashBag()->add('error', $msg);

            $_SESSION['failed_login_attempts'] =
                isset($_SESSION['failed_login_attempts']) ?
                $_SESSION['failed_login_attempts'] + 1 : 1;
        }

        $token = $this->get('form.csrf_provider')
            ->generateCsrfToken('authenticate');
        $currentLanguage  = \Application::$language;

        $failed_login_attempts = isset($_SESSION['failed_login_attempts']) ?
            $_SESSION['failed_login_attempts'] : 0;

        return $this->render(
            'login/login.tpl',
            array(
                'failed_login_attempts' => $failed_login_attempts,
                'current_language'      => $currentLanguage,
                'token'                 => $token,
                'referer'               => $referer
            )
        );
    }
}
