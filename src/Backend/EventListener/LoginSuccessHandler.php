<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Handler to load user data when an user logs in the system successfully.
 */
class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * Constructs a new handler.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * This is called when an interactive authentication attempt succeeds.
     *
     * @param Request        $request The request object.
     * @param TokenInterface $token   The security token.
     *
     * @return Response The response to return.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        $session  = $request->getSession();
        $attempts = $session->get('failed_login_attempts', 0);
        $user     = $token->getUser();

        // TODO: Remove when Smarty can get services from service container
        $session->set('user', $user);

        // TODO: Remove when removing logging actions from old data model
        $_SESSION['userid']   = $user->id;
        $_SESSION['username'] = $user->username;
        //$_SESSION['accesscategories'] = $user->getAccessCategoryIds();

        $recaptchaValid = $this->isRecaptchaValid($request);
        $csrfTokenValid = $this->isCsrfTokenValid($request);

        // Check token, user type and reCaptcha
        if ($recaptchaValid && $csrfTokenValid && $user->type === 0) {
            $time = new \DateTime();
            $time->setTimezone(new \DateTimeZone('UTC'));
            $time = $time->format('Y-m-d H:i:s');

            if (!$user->isMaster()) {
                s::set('last_login', $time);
            }

            return new RedirectResponse($request->get('_referer'));
        }

        $session->set('failed_login_attempts', $attempts + 1);

        if (!$csrfTokenValid) {
            $session->getFlashBag()->add(
                'error',
                _('Login token is not valid. Try to authenticate again.')
            );
        }

        if (!$recaptchaValid) {
            $session->getFlashBag()->add(
                'error',
                _('The reCAPTCHA was not entered correctly. Try to authenticate'
                . ' again.')
            );
        }

        if (!$user->type != 0) {
            $session->getFlashBag()->add(
                'error',
                _('Your user is not allowed to access, please contact your administrator')
            );
        }

        $container->get('security.token_storage')->setToken(null);

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * Checks if the CSRF token is valid basing on the request.
     *
     * @param Request $request The request object.
     *
     * @return boolean True if the CSRF token is valid. False otherwise.
     */
    protected function isCsrfTokenValid(Request $request)
    {
        if (empty($request->get('_token'))) {
            return false;
        }

        return $this->container->get('form.csrf_provider')->isCsrfTokenValid(
            $request->getSession()->get('intention'),
            $request->get('_token')
        );
    }

    /**
     * Checks if the recaptcha is valid basing on the request.
     *
     * @param Request $request The request object.
     *
     * @return boolean True if the recaptcha code is valid or missing. False
     *                 otherwise.
     */
    protected function isRecaptchaValid(Request $request)
    {
        if (empty($request->get('g-recaptcha-response'))) {
            return true;
        }

        $ip       = $request->getClientIp();
        $response = $request->get('g-recaptcha-response');

        return $this->container->get('google_recaptcha')->getOnmRecaptcha()
            ->verify($response, $ip)->isSuccess();
    }
}
