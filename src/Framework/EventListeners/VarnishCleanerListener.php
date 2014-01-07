<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

namespace Framework\EventListeners;

use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * VarnishCleanerListener fixes the Response headers based on the Request.
 *
 * @author Fran Diéguez
 */
class VarnishCleanerListener implements EventSubscriberInterface
{
    /**
     * Filters the Response.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelTerminate()
    {
        global $sc;

        if ($sc->hasParameter('varnish')
            && $sc->hasParameter('varnish_ban_request')
        ) {
            $banRequest = $sc->getParameter('varnish_ban_request');

            $varnishCleaner = $sc->get('varnish_cleaner');
            $varnishCleaner->ban($banRequest);

            $logger = $sc->get('logger');
            $logger->notice('Varnish BAN queued: '.$sc->getParameter('varnish_ban_request'));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::TERMINATE => 'onKernelTerminate',
        );
    }
}
