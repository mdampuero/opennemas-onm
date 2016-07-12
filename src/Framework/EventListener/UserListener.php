<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Framework\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ResponseListener fixes the Response headers based on the Request.
 */
class UserListener implements EventSubscriberInterface
{
    /**
     * The security context.
     *
     * @var SecurityContextInterface
     */
    private $context;

    /**
     * The user provider.
     *
     * @var OnmUserProvider
     */
    private $provider;

    /**
     * The router service.
     *
     * @var OnmUserProvider
     */
    private $router;

    /**
     * The current session.
     *
     * @var Session
     */
    private $session;

    /**
     * Initializes the listener.
     *
     * @param SecurityContext $context  The security context service.
     * @param OnmUserProvider $provider The user provider.
     * @param Router          $router   The router service.
     * @param Session         $session  The current session.
     */
    public function __construct($context, $provider, $router, $session)
    {
        $this->context  = $context;
        $this->provider = $provider;
        $this->router   = $router;
        $this->session  = $session;
    }

    /**
     * Refresh the user roles for an authenticated user.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $uri = $event->getRequest()->getRequestUri();

        if (preg_match('@^/_.*@', $uri) != 1
            && $this->context->getToken()
            && $this->context->getToken()->getUser()
            && $this->context->getToken()->getUser() != 'anon.'
        ) {
            $token = $this->context->getToken();
            $user = $token->getUser();

            $user = $this->provider->loadUserByUsername($user->email);
            $user->eraseCredentials();
            $token->setUser($user);

            $database  = getService('instance_manager')->current_instance->getDatabaseName();
            $namespace = getService('instance_manager')->current_instance->internal_name;

            getService('dbal_connection')->selectDatabase($database);
            getService('cache')->setNamespace($namespace);
            $GLOBALS['application']->conn->selectDatabase($database);

            if ($user->isMaster() || $user->isEnabled()) {
                return;
            }

            // Logout for web services
            if (preg_match('@^/admin/entityws.*@', $uri) === 1
                || preg_match('@^/manager(ws).*@', $uri) === 1
            ) {
                $event->setResponse(new JsonResponse(null, 401));
                return;
            }

            // Logout for backend
            if (preg_match('@^/admin.*@', $uri) === 1) {
                $event->setResponse(
                    new RedirectResponse(
                        $this->router->generate('admin_logout')
                    )
                );
            }
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::REQUEST => 'onKernelRequest',
        );
    }
}
