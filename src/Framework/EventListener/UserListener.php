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
     * @var SecurityContextInterface
     */
    private $context;

    /**
     * The user provider
     *
     * @var OnmUserProvider
     */
    private $provider;

    /**
     * Initializes the listener.
     *
     * @param SecurityContext $context  The security context service.
     * @param OnmUserProvider $provider The user provider.
     * @param Router          $router   The router service.
     */
    public function __construct($context, $provider, $router)
    {
        $this->context  = $context;
        $this->provider = $provider;
        $this->router   = $router;
    }

    /**
     * Refresh the user roles for an authenticated user.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $uri = $event->getRequest()->getRequestUri();

        if (preg_match('@^/_.*@', $uri) != 1 && $this->context->getToken()) {
            $token = $this->context->getToken();
            $user = $token->getUser();

            if ($user && $user != 'anon.') {
                $user = $this->provider->loadUserByUsername($user->getUsername());
                $user->eraseCredentials();
                $token->setUser($user);
            }

            $database  = getService('instance_manager')->current_instance->getDatabaseName();
            $namespace = getService('instance_manager')->current_instance->internal_name;

            getService('dbal_connection')->selectDatabase($database);
            getService('cache')->setNamespace($namespace);
            $GLOBALS['application']->conn->selectDatabase($database);

            if ($user->isMaster() || $user->isEnabled()) {
                return;
            }

            if (preg_match('@^/admin.*@', $uri) === 1) {
                $response =  new RedirectResponse(
                    $this->router->generate('admin_logout')
                );

                $event->setResponse($response);
            }

            if (preg_match('@^/manager(ws).*@', $uri) === 1) {
                $response = new JsonResponse('', 401);
                $event->setResponse($response);
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
