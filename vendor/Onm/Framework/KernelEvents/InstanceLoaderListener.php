<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onm\Framework\KernelEvents;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Onm\Settings as s;

/**
 * ResponseListener fixes the Response headers based on the Request.
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
        mb_internal_encoding("UTF-8");

        global $sc;

        require_once 'Application.php';

        initEnvironment($sc->getParameter('environment'));

        // Loads one ONM instance from database
        $im = new \Onm\Instance\InstanceManager();

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
