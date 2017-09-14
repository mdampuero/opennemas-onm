<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Handles the actions for the user authentication in backend.
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
        $referer = $this->generateUrl('admin_welcome');
        $session = $request->getSession();
        $token   = $request->get('token');

        $session->set('login_callback', $referer);

        // Login from URL token
        if (!empty($token)) {
            $em = $this->get('orm.manager');

            try {
                $user = $em->getRepository('User')
                    ->findOneBy(sprintf('token = "%s"', $token));
            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', _('Invalid token'));
                return $this->redirect($this->generateUrl('admin_login'));
            }

            $user->token = null;
            $em->persist($user);
            $token = new UsernamePasswordToken($user, null, 'backend', $user->getRoles());

            $securityContext = $this->get('security.token_storage');
            $securityContext->setToken($token);
            $session->set('user', $user);
            $session->set('_security_backend', serialize($token));

            // Set last_login date
            if (!$user->isMaster()) {
                $time = new \DateTime();
                $time->setTimezone(new \DateTimeZone('UTC'));
                $time = $time->format('Y-m-d H:i:s');

                $this->get('setting_repository')->set('last_login', $time);
            }

            return $this->redirect($this->generateUrl('admin_welcome'));
        }

        if ($session->get('_security.backend.target_path')) {
            $referer = $session->get('_security.backend.target_path');
        }

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(Security::AUTHENTICATION_ERROR);
        }

        if (!empty($error)) {
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

        $recaptcha = '';
        if ($session->get('failed_login_attempts') >= 3) {
            $recaptcha = $this->get('core.recaptcha')
                ->configureFromParameters()
                ->getHtml();
        }

        return $this->render('login/login.tpl', [
            'recaptcha' => $recaptcha,
            'token'     => $token,
            'referer'   => $referer,
            'locale'    => $this->get('core.locale')->getLocale(),
            'locales'   => $this->get('core.locale')->getAvailableLocales()
        ]);
    }

    /**
     * Displays a popup after login/connect with social accounts.
     *
     * @return Response The response object.
     */
    public function loginCallbackAction(Request $request)
    {
        $redirect = $request->getSession()->get('_security.backend.target_path');

        if ($redirect === '/admin/login/callback') {
            return $this->render('common/close_popup.tpl');
        }

        if (!empty($redirect)) {
            return $this->redirect($redirect);
        }

        return $this->redirect($this->generateUrl('admin_welcome'));
    }
}
