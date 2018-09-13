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
        if (!empty($this->get('core.user'))) {
            return $this->redirect($this->generateUrl('admin_welcome'));
        }

        $target  = $this->generateUrl('admin_welcome');
        $session = $request->getSession();
        $token   = $request->get('token');

        if ($request->getSession()->has('_target')) {
            $target = $request->getSession()->get('_target');
        }

        // Login from URL token
        if (!empty($token)) {
            $em = $this->get('orm.manager');

            try {
                $user = $em->getRepository('User')
                    ->findOneBy(sprintf('token = "%s"', $token));
            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', _('Invalid token'));
                return $this->redirect($this->generateUrl('backend_authentication_login'));
            }

            $user->token = null;
            $em->persist($user);
            $token = new UsernamePasswordToken($user, null, 'backend', $user->getRoles());

            $securityContext = $this->get('security.token_storage');
            $securityContext->setToken($token);
            $session->set('user', $user);
            $session->set('_security_backend', serialize($token));

            // Set last_login date
            if (!$this->get('core.security')->hasPermission('MASTER')) {
                $time = new \DateTime();
                $time->setTimezone(new \DateTimeZone('UTC'));
                $time = $time->format('Y-m-d H:i:s');

                $this->get('setting_repository')->set('last_login', $time);
            }

            return $this->redirect($this->generateUrl('admin_welcome'));
        }

        $auth      = $this->get('core.security.authentication');
        $recaptcha = '';

        if ($auth->isRecaptchaRequired()) {
            $recaptcha = $auth->getRecaptchaFromParameters();
        }

        if ($auth->hasError()) {
            $session->getFlashBag()->add('error', $auth->getErrorMessage());
        }

        return $this->render('login/login.tpl', [
            'locale'    => $this->get('core.locale')->getLocale(),
            'locales'   => $this->get('core.locale')->getAvailableLocales(),
            'recaptcha' => $recaptcha,
            'target'    => $target,
            'token'     => $auth->getCsrfToken()
        ]);
    }
}
