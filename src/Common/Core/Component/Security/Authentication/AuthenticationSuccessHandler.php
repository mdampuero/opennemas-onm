<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Security\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Handler to load user data when an user logs in the system successfully.
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
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
     * @param Logger         $logger The logger service.
     * @param Router         $router The router service.
     * @param TokenStorage   $ts     The token storage.
     */
    public function __construct($auth, $logger, $router, $ts)
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
        $session   = $request->getSession();
        $target    = $request->get('_target');

        if (empty($target)) {
            $target = $this->router->generate('frontend_user_show');
        }

        $session->remove('_target');

        // Check reCAPTCHA only if present
        if (!is_null($recaptcha)) {
            $this->auth->checkRecaptcha($recaptcha, $request->getClientIp());
        }

        $this->auth->checkCsrfToken($request->get('_token'));

        if ($request->isXmlHttpRequest()) {
            $target = $this->router->generate('core_authentication_authenticated');
        }

        if ($this->auth->hasError()) {
            $this->auth->failure();

            $error = $this->auth->getErrorMessage();

            $session->getFlashBag()->add('error', $error);
            $this->logger->info($error);
            $this->ts->setToken(null);

            if (!$request->isXmlHttpRequest()) {
                $target = $request->headers->get('referer');
            }

            return new RedirectResponse($target);
        }

        $this->auth->success();
        $this->logger->info("User $user->username (ID: $user->id) has logged in.");

        return new RedirectResponse($target);
    }
}
