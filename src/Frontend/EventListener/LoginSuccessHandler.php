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
     * The authentication service.
     *
     * @var Authentication
     */
    protected $auth;

    /**
     * The logger service.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * The router service.
     *
     * @var Router
     */
    protected $router;

    /**
     * The token storage.
     *
     * @var TokenStorage
     */
    protected $ts;

    /**
     * Constructs a new handler.
     *
     * @param Authentication $auth   The authentication service.
     * @param TokenStorage   $ts     The token storage.
     * @param Router         $router The router service.
     * @param Logger         $logger The logger service.
     */
    public function __construct($auth, $ts, $router, $logger)
    {
        $this->auth   = $auth;
        $this->logger = $logger;
        $this->router = $router;
        $this->ts     = $ts;
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
        $user      = $token->getUser();
        $recaptcha = $request->get('g-recaptcha-response');

        $session = $request->getSession();
        $session->set('user', $user);
        $session->set('user_language', $user->user_language);

        // Check reCaptcha if is set
        if (!is_null($recaptcha)) {
            $this->auth->checkRecaptcha($recaptcha, $request->getClientIp());
        }

        $this->auth->checkCsrfToken($request->get('_token'));

        // Login fails because of CSRF token or reCaptcha
        if ($this->auth->hasError()) {
            $this->auth->failure();

            $error = $this->auth->getErrorMessage();

            $session->getFlashBag()->add('error', $error);
            $this->logger->info($error);
            $this->ts->setToken(null);

            return new RedirectResponse($request->headers->get('referer'));
        }

        $this->auth->success();
        $this->logger->info("User $user->username (ID: $user->id) has logged in.");

        $response = new RedirectResponse($request->get('_referer'));

        return $response;
    }
}
