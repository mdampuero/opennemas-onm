<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
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
     * The service container.
     *
     * @var SecurityContainer
     */
    private $container;

    /**
     * Initializes the listener.
     *
     * @param SecurityContext $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;

        $this->context  = $container->get('security.token_storage');
        $this->provider = $container->get('onm_user_provider');
        $this->router   = $container->get('router');
        $this->session  = $container->get('session');
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

            $user = $this->provider->loadUserByUsername($user->getUsername());
            $user->eraseCredentials();
            $token->setUser($user);

            $instance = $this->container->get('core.instance');

            $database  = $instance->getDatabaseName();
            $namespace = $instance->internal_name;

            getService('orm.manager')->getConnection('instance')->selectDatabase($database);
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
