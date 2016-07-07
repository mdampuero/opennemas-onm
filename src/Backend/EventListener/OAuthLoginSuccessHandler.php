<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
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
 * Handler to load user data when an user logs in the system successfully by
 * using their social accounts.
 */
class OAuthLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * The security context.
     *
     * @var SecurityContext
     */
    private $context;

    /**
     * The router service.
     *
     * @var Router
     */
    protected $router;

    /**
     * Constructs a new handler.
     *
     * @param SecurityContext $context The security context.
     * @param Router          $router  The router service.
     */
    public function __construct($context, $router)
    {
        $this->context = $context;
        $this->router  = $router;
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
        $referer    = $this->router->generate('admin_welcome');
        $user       = $token->getUser();

        // Set session array
        $request->getSession()->set('user', $user);
        $request->getSession()->set('user_language', $user->getMeta('user_language'));

        if ($request->getSession()->get('_security.backend.target_path')) {
            $referer = $request->getSession()->get('_security.backend.target_path');
        }

        return new RedirectResponse($referer);
    }
}
