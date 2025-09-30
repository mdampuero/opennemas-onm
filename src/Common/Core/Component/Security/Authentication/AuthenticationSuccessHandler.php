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
     * The url decorator.
     *
     * @var UrlDecorator
     */
    protected $urlDecorator;

    /**
     * The two factor manager.
     *
     * @var TwoFactorManager
     */
    protected $twoFactor;

    /**
     * Constructs a new handler.
     *
     * @param Authentication   $auth         The authentication service.
     * @param Logger           $logger       The logger service.
     * @param Router           $router       The router service.
     * @param TokenStorage     $ts           The token storage.
     * @param UrlDecorator     $urlDecorator The url decorator to transform url to subdirectory.
     * @param TwoFactorManager $twoFactor    The two factor manager.
     */
    public function __construct($auth, $logger, $router, $ts, $urlDecorator, $twoFactor = null)
    {
        $this->auth         = $auth;
        $this->logger       = $logger;
        $this->router       = $router;
        $this->ts           = $ts;
        $this->urlDecorator = $urlDecorator;
        $this->twoFactor    = $twoFactor;
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
        $recaptcha = $request->get('g-recaptcha-response');
        $session   = $request->getSession();
        $target    = $request->get('_target');
        $referer   = $request->headers->get('referer');

        if (empty($target)) {
            $target = $this->router->generate('frontend_user_show');
        }

        $session->remove('_target');

        // Check reCAPTCHA only if present
        if (!is_null($recaptcha)) {
            $this->auth->checkRecaptcha($recaptcha, $request->getClientIp(), $referer);
        }

        $this->auth->checkCsrfToken($request->get('_token'));

        if ($request->isXmlHttpRequest()) {
            $target = $this->router->generate('core_authentication_authenticated');
        }

        if ($this->auth->hasError()) {
            $this->logger->info($this->auth->getInternalErrorMessage());
            $this->ts->setToken(null);

            if (!$request->isXmlHttpRequest()) {
                $target = $referer;
            }

            return new RedirectResponse($target);
        }

        $this->auth->success();
        $this->logger->info('security.authentication.success');

        if ($this->twoFactor && $this->twoFactor->shouldChallenge($request, $token->getUser())) {
            if ($this->twoFactor->initiate($request, $token->getUser(), $target)) {
                return new RedirectResponse($this->twoFactor->getVerificationUrl());
            }

            $this->twoFactor->clear();
            $session->getFlashBag()->add(
                'error',
                _('We were unable to send the verification code. Please try again or contact an administrator.')
            );

            $this->ts->setToken(null);

            $loginUrl = $this->urlDecorator->prefixUrl(
                $this->router->generate('backend_authentication_login')
            );

            return new RedirectResponse($loginUrl);
        }

        return new RedirectResponse($target);
    }
}
