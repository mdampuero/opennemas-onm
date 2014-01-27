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
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ResponseListener fixes the Response headers based on the Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ControllerListener implements EventSubscriberInterface
{
    /**
     * Filters the Response.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        // Assign request attributes to query parameters
        $request = $event->getRequest();
        if (is_array($request->attributes->get('_route_params'))) {
            foreach ($request->attributes->get('_route_params') as $key => $value) {
                $request->query->set($key, $value);
            }
        }

        $controller = $event->getController();
        $controllerName = get_class($controller[0]);

        if (method_exists($controller[0], 'init')) {
            $controller[0]->init();
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::CONTROLLER => 'onKernelController',
        );
    }
}
