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
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Onm\Settings as s;

/**
 * Handles all backend requests when maintenance mode is enabled.
 */
class MaintenanceModeListener implements EventSubscriberInterface
{
    /**
     * Checks if maintenance mode is enabled and returns a custom response.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        if (strpos($request->getRequestUri(), '/admin') !== 0) {
            return;
        }

        $maintenanceFile = APP_PATH.'/../.maintenance';

        if (file_exists($maintenanceFile)) {
            $request = $event->getRequest();

            $attributes = array(
                '_controller' => 'OnmFrameworkBundle:Maintenance:default',
                'format'      => $request->getRequestFormat(),
            );

            $request = $request->duplicate(null, null, $attributes);
            $request->setMethod('GET');

            try {
                $response = $event->getKernel()->handle($request, HttpKernelInterface::SUB_REQUEST, true);
            } catch (\Exception $e) {
                return;
            }

            $event->setResponse($response);
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
            SymfonyKernelEvents::REQUEST => array(array('onKernelRequest', 100)),
        );
    }
}
