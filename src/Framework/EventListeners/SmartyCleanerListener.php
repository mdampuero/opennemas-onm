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
 * ResponseListener fixes the Response headers based on the Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SmartyCleanerListener implements EventSubscriberInterface
{
    /**
     * Filters the Response.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelTerminate()
    {
        // global $sc;

        // $logger = $sc->get('logger');

        // if ($sc->hasParameter('varnish_ban_request')) {
        //     $banRequest = $sc->getParameter('varnish_ban_request');

        //     $varnishCleaner = $sc->get('varnish_cleaner');
        //     $varnishCleaner->ban($banRequest);

        //     $logger->notice('Varnish BAN queued: '.$sc->getParameter('varnish_ban_request'));
        // }
        //  elseif (!preg_match('@media@', $_SERVER['REQUEST_URI'])) {
        //     $logger->notice('TerminateEVent: varnish_cleaner do not have requests'.$_SERVER['REQUEST_URI']);
        // }

    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::TERMINATE => 'onKernelTerminate',
        );
    }
}
