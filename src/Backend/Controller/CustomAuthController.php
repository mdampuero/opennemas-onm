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
     * Displays the login form template.
     */
    public function loginAction(Request $request)
    {
        $lastUsername = null;

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $request->attributes
                ->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        $lastUsername = $request->getSession()
            ->get(SecurityContext::LAST_USERNAME);

        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();

        $token = $this->get('form.csrf_provider')->generateCsrfToken('authenticate');
        $_SESSION['csrf'] = $token;
        $currentLanguage  = \Application::$language;

        return $this->render(
            'login/login.tpl',
            array(
                'current_language' => $currentLanguage,
                'token'            => $token,
            )
        );
    }
}
