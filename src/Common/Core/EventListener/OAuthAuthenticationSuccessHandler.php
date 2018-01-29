<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\EventListener;

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
class OAuthAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

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
     * Redirects to target after a successful OAuth-based authentication. This
     * will save the current user in database basing on the target.
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
        $target = $request->getSession()->get('_security.opennemas.target_path');
        $user   = $token->getUser();

        // Create a new user
        if (!preg_match('/connect$/', $target) && !$user->exists()) {
            $this->container->get('orm.manager')->persist($user, 'instance');
        }

        return new RedirectResponse(
            $this->container->get('router')
                ->generate('core_authentication_complete')
        );
    }
}
