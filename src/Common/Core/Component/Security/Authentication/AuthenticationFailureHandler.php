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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

/**
 * Handler to load user data when an user logs in the system successfully.
 */
class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    /**
     * The authentication service.
     *
     * @var Authentication
     */
    protected $auth;

    /**
     * The router service.
     *
     * @var Router
     */
    protected $router;

    /**
     * Constructs a new handler.
     *
     * @param Authentication $auth   The authentication service.
     * @param Logger         $logger The logger service.
     * @param Router         $router The router service.
     */
    public function __construct($auth, $logger, $router)
    {
        $this->auth   = $auth;
        $this->logger = $logger;
        $this->router = $router;
    }

    /**
     * This is called when an interactive authentication attempt succeeds.
     *
     * @param Request        $request The request object.
     * @param TokenInterface $token   The security token.
     *
     * @return Response The response to return.
     */
    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ) {
        $referer = $request->headers->get('referer');

        $this->auth->addError($exception);
        $this->auth->failure();
        $this->logger->info($this->auth->getInternalErrorMessage());

        if ($request->isXmlHttpRequest()) {
            return new RedirectResponse(
                $this->router->generate('core_authentication_authenticated')
            );
        }

        if (preg_match('/admin\/login/', $referer)) {
            return new RedirectResponse(
                $this->router->generate('backend_authentication_login')
            );
        }

        return new RedirectResponse(
            $this->router->generate('frontend_authentication_login')
        );
    }
}
