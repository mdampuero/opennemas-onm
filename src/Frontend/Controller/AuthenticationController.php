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
        $auth      = $this->get('core.security.authentication');
        $referer   = '/' . trim(
            $request->query->filter('referer', '', FILTER_SANITIZE_STRING),
            '/'
        );
        $session   = $request->getSession();
        $recaptcha = '';

        if (empty($referer)) {
            $referer = $this->generateUrl('frontend_frontpage');
        }

        if ($auth->hasError()) {
            $auth->failure();

            $session->getFlashBag()->add('error', $auth->getErrorMessage());
        }

        if ($auth->isRecaptchaRequired()) {
            $recaptcha = $auth->getRecaptchaFromParameters();
        }

        return $this->render('authentication/login.tpl', [
            'recaptcha' => $recaptcha,
            'token'     => $auth->getCsrfToken(),
            'referer'   => $referer
        ]);
    }
}
