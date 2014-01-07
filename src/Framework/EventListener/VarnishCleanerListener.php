<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

namespace Framework\EventListener;

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
        global $kernel;
        $container = $kernel->getContainer();

        if ($container->hasParameter('varnish')
            && $container->hasParameter('varnish_ban_request')
        ) {
            $banRequest = $container->getParameter('varnish_ban_request');

            $varnishCleaner = $container->get('varnish_cleaner');
            $varnishCleaner->ban($banRequest);

            $logger = $container->get('logger');
            $logger->notice('Varnish BAN queued: '.$container->getParameter('varnish_ban_request'));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::TERMINATE => 'onKernelTerminate',
        );
    }
}
