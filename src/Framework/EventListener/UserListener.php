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
     * @param SecurityContextInterface $context  The security context service.
     * @param OnmUserProvider          $provider The user provider.
     */
    public function __construct($context, $provider)
    {
        $this->context  = $context;
        $this->provider = $provider;
    }

    /**
     * Refresh the user roles for an authenticated user.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (preg_match('@^/_.*@', $event->getRequest()->getRequestUri()) != 1
            && $this->context->getToken()
        ) {
            $token = $this->context->getToken();
            $user = $token->getUser();

            if ($user && $user != 'anon.') {
                $user = $this->provider->loadUserByUsername($user->getUsername());
                $user->eraseCredentials();
                $token->setUser($user);
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
