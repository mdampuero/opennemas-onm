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

use Common\Core\Component\Security\Authentication\TwoFactorManager;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

                $this->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->set('last_login', $time);
            }

            return $this->redirect($this->generateUrl('admin_welcome'));
        }

        $auth = $this->get('core.security.authentication');

        if ($auth->hasError()) {
            $session->getFlashBag()->add('error', $auth->getErrorMessage());
        }

        if ($request->query->has('language')) {
            $this->get('core.locale')
                ->setContext('backend')
                ->setLocale($request->query->get('language'))
                ->apply();
        }

        return $this->render('login/login.tpl', [
            'locale'           => $this->get('core.locale')->getLocale(),
            'availableLocales' => $this->get('core.locale')->getAvailableLocales(),
            'recaptcha'        => $auth->getRecaptchaFromParameters(),
            'target'           => $target,
            'token'            => $auth->getCsrfToken()
        ]);
    }

    /**
     * Displays and processes the two factor challenge.
     *
     * @return Response The response object.
     */
    public function twoFactorAction(Request $request)
    {
        $twoFactor = $this->get('core.security.authentication.two_factor');
        $logger    = $this->get('application.log');

        if (!$twoFactor->isPending()) {
            return $this->redirect($this->generateUrl('admin_welcome'));
        }
        
        if ($request->isMethod('POST')) {
            $code = $request->request->get('verification_code');

            if ($twoFactor->verify($code)) {
                $target = $twoFactor->getTarget();

                if (empty($target)) {
                    $target = $this->generateUrl('admin_welcome');
                }

                $response = new RedirectResponse(
                    $this->get('core.decorator.url')->prefixUrl($target)
                );

                $twoFactor->complete($response);

                return $response;
            }

            $logger->error('2FA - Verification challenge failed.');
            $request->getSession()->getFlashBag()->add(
                'error',
                _('The verification code is not valid or has expired. Please try again.')
            );
        }

        return $this->render('login/twofactor.tpl', [
            'email'            => $twoFactor->getMaskedEmail(),
            'locale'           => $this->get('core.locale')->getLocale(),
            'availableLocales' => $this->get('core.locale')->getAvailableLocales(),
        ]);
    }

    /**
     * Cancels the two factor challenge and returns to the login form.
     */
    public function twoFactorCancelAction(Request $request)
    {
        $twoFactor = $this->get('core.security.authentication.two_factor');

        if ($twoFactor) {
            $twoFactor->clear();
        }

        $tokenStorage = $this->get('security.token_storage');

        if ($tokenStorage) {
            $tokenStorage->setToken(null);
        }

        $session = $request->getSession();

        if ($session) {
            $session->invalidate();
        }

        $response = $this->redirect($this->generateUrl('backend_authentication_login'));
        $response->headers->clearCookie(TwoFactorManager::COOKIE_NAME);

        return $response;
    }
}
