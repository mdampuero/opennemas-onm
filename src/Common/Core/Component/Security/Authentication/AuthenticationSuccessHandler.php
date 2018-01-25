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
     * @param TokenStorage   $ts     The token storage.
     */
    public function __construct($auth, $logger, $ts)
    {
        $this->auth   = $auth;
        $this->logger = $logger;
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

        $session->set('user', $user);
        $session->set('user_language', $user->user_language);

        // Check reCAPTCHA only if present
        if (!is_null($recaptcha)) {
            $this->auth->checkRecaptcha($recaptcha, $request->getClientIp());
        }

        $this->auth->checkCsrfToken($request->get('_token'));

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

        return new RedirectResponse($request->get('_target'));
    }
}
