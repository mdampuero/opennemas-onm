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
use Symfony\Component\Debug\Debug;
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

        mb_internal_encoding("UTF-8");

        global $sc;

        $env = $sc->getParameter('environment');
        initEnvironment($env);

        // Register the Debugger into the application, transforms fatal errors into
        // exceptions
        // if ($env !== 'production') {
        //     Debug::enable(null, ($env !== 'production'));
        // }

        require_once 'Application.php';

        $request = $event->getRequest();

        // Loads one ONM instance from database
        $im = new \Onm\Instance\InstanceManager($request->getHttpHost());

        $instance = $im->load($_SERVER['SERVER_NAME']);

        $sc->setParameter('instance', $instance);
        $sc->setParameter('cache_prefix', $instance->internal_name);

        $app = \Application::load();

        $timezone = \Onm\Settings::get('time_zone');
        if (isset($timezone)) {
            $availableTimezones = \DateTimeZone::listIdentifiers();
            date_default_timezone_set($availableTimezones[$timezone]);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::REQUEST => array(array('onKernelRequest', 100)),
        );
    }
}
