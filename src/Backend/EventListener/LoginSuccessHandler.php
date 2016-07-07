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
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Handler to load user data when an user logs in the system successfully.
 */
class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * The security context.
     *
     * @var SecurityContext
     */
    protected $context;

    /**
     * The recaptcha service.
     *
     * @var Recaptcha
     */
    protected $recaptcha;

    /**
     * The router service.
     *
     * @var Router
     */
    protected $router;

    /**
     * The setting repository
     *
     * @var SettingManager
     */
    protected $sm;

    /**
     * Constructs a new handler.
     *
     * @param SecurityContext $context   The security context.
     * @param Router          $router    The router service.
     * @param Recaptcha       $recaptcha The Google Recaptcha.
     * @param SettingManager  $sm        The setting repository.
     */
    public function __construct($context, $router, $recaptcha, $sm)
    {
        $this->context   = $context;
        $this->router    = $router;
        $this->recaptcha = $recaptcha;
        $this->sm        = $sm;
    }

    /**
     * This is called when an interactive authentication attempt succeeds.
     *
     * @param Request        $request The request object.
     * @param TokenInterface $token   The security token.
     *
     * @return Response The response to return.
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token
    ) {
        $user  = $token->getUser();
        $valid = true;

        // Check reCaptcha if is set
        $response = $request->get('g-recaptcha-response');
        if (!empty($response)) {
            $valid = $this->recaptcha->getOnmRecaptcha()->verify(
                $request->get('g-recaptcha-response'),
                $request->getClientIp()
            )->isSuccess();
        }

        $session = $request->getSession();
        $session->set('user', $user);
        $session->set('user_language', $user->getMeta('user_language'));

        $isTokenValid = getService('form.csrf_provider')->isCsrfTokenValid(
            $session->get('intention'),
            $request->get('_token')
        );

        // Login fails because of CSRF token, user type or reCaptcha
        if (!$isTokenValid || !$valid || $user->type != 0) {
            $session->set(
                'failed_login_attempts',
                $session->get('failed_login_attempts') + 1
            );

            if (!$isTokenValid) {
                $session->getFlashBag()->add(
                    'error',
                    _('Login token is not valid. Try to authenticate again.')
                );
            }

            if ($valid) {
                $session->getFlashBag()->add(
                    'error',
                    _('The reCAPTCHA was not entered correctly. Try to authenticate again.')
                );
            }

            if ($user->type != 0) {
                $session->getFlashBag()->add(
                    'error',
                    _('Your user is not allowed to access, please contact your administrator')
                );
            }

            $this->context->setToken(null);

            return new RedirectResponse($request->headers->get('referer'));
        }

        $session->set('failed_login_attempts', 0);

        // Set last_login date
        $time = new \DateTime();
        $time->setTimezone(new \DateTimeZone('UTC'));
        $time = $time->format('Y-m-d H:i:s');

        if (!$user->isMaster()) {
            $this->sm->set('last_login', $time);
        }

        return new RedirectResponse($request->get('_referer'));
    }
}
