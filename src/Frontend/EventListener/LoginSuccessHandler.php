<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
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
     * Constructs a new handler.
     *
     * @param SecurityContext $context   The security context.
     * @param Router          $router    The router service.
     * @param Recaptcha       $recaptcha The Google Recaptcha.
     */
    public function __construct($context, $router, $recaptcha, $logger)
    {
        $this->context   = $context;
        $this->router    = $router;
        $this->recaptcha = $recaptcha;
        $this->logger    = $logger;
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
        if (!is_null($response)) {
            $valid = $this->recaptcha->configureFromSettings()->isValid(
                $request->get('g-recaptcha-response'),
                $request->getClientIp()
            );
        }

        $session = $request->getSession();
        $session->set('user', $user);
        $session->set('user_language', $user->user_language);

        $isTokenValid = getService('form.csrf_provider')->isCsrfTokenValid(
            $session->get('intention'),
            $request->get('_token')
        );

        // Login fails because of CSRF token or reCaptcha
        if (!$isTokenValid || $valid === false) {
            $session->set(
                'failed_login_attempts',
                $session->get('failed_login_attempts') + 1
            );

            if (!$isTokenValid) {
                $session->getFlashBag()->add(
                    'error',
                    _('Login token is not valid. Try to authenticate again.')
                );
                $this->logger->info("User ".$user->username." (ID:".$user->id.") tried to log in. Invalid token");
            }

            if ($valid === false) {
                $session->getFlashBag()->add(
                    'error',
                    _('The reCAPTCHA was not entered correctly. Try to authenticate'
                    . ' again.')
                );
                $this->logger->info("User ".$user->username." (ID:".$user->id.") tried to log in. Recaptcha failed.");
            }

            $this->context->setToken(null);

            return new RedirectResponse($request->headers->get('referer'));
        }

        $session->set('failed_login_attempts', 0);
        $this->logger->info("User ".$user->username." (ID:".$user->id.") has logged in.");

        $response = new RedirectResponse($request->get('_referer'));
        $response->headers->setCookie(
            new Cookie('__onm_user', json_encode([
                'name'        => $user->name,
                'language'    => $user->user_language,
                'user_groups' => $user->fk_user_group
            ]), 0, null, null, false, false)
        );

        return $response;
    }
}
