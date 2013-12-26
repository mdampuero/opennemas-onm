<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\EventListeners;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Onm\Settings as s;

/**
 * InstanceLoaderListener initializes the instance from the request object
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class InstanceLoaderListener implements EventSubscriberInterface
{
    /**
     * Filters the Response.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        // Load instance from database
        $instanceManager = getService('instance_manager');
        $instance        = $instanceManager->load($_SERVER['SERVER_NAME']);

        global $sc;
        $sc->setParameter('instance', $instance);
        $sc->setParameter('cache_prefix', $instance->internal_name);

        if ($instance->internal_name == 'onm_manager') {
            return false;
        }
        // Initialize the instance database connection
        $databaseName               = $instance->getDatabaseName();
        $databaseInstanceConnection = getService('db_conn');
        $databaseInstanceConnection->selectDatabase($databaseName);

        // CRAP: take this out, Workaround
        $app = \Application::load();
    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::REQUEST => array(array('onKernelRequest', 100)),
        );
    }
}
