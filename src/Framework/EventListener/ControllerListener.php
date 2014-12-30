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

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Initializes the controller before it handles the request.
 */
class ControllerListener implements EventSubscriberInterface
{
    /**
     * Finds the controller that will handle the current request and executes
     * its init method.
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
        if (method_exists($controller[0], 'init')) {
            $controller[0]->init();
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
            SymfonyKernelEvents::CONTROLLER => 'onKernelController',
        );
    }
}
