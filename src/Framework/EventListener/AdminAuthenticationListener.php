<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Framework\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ResponseListener fixes the Response headers based on the Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AdminAuthenticationListener implements EventSubscriberInterface
{
    /**
     * @var Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $context;


    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * Filters the Response.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (preg_match('@^/_.*@', $event->getRequest()->getRequestUri()) != 1
            && $this->context->getToken()
        ) {
            $token = $this->context->getToken();
            $user = $token->getUser();

            if ($user && $user != 'anon.') {
                $user->eraseCredentials();
            }

            $token->setAuthenticated(false);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::REQUEST => 'onKernelRequest',
        );
    }
}
