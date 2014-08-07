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

use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * VarnishCleanerListener fixes the Response headers based on the Request.
 */
class VarnishCleanerListener implements EventSubscriberInterface
{
    private $enabled = false;

    /**
     * Initializes the VarnishCleanerListener.
     *
     * @param array            $varnishConf      The Varnish server configuration.
     * @param VernishCleaner   $varnishCleaner   The Varnish Cleaner service.
     * @param MessageExchanger $messageExchanger The Varnish MessageExchanger service.
     * @param LoggerInterface  $logger           The logger service
     */
    public function __construct($varnishConf, $varnishCleaner, $messageExchanger, $logger)
    {
        if (is_array($varnishConf) && count($varnishConf) > 0) {
            $this->enabled = true;
        }

        $this->varnishCleaner   = $varnishCleaner;
        $this->messageExchanger = $messageExchanger;
        $this->logger           = $logger;
    }

    /**
     * Filters the Response.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance.
     */
    public function onKernelTerminate()
    {
        // If varnish cleaner is configured and new messages were registered
        // send messages to varnish
        if ($this->enabled
            && count($this->messageExchanger->getMessages()) > 0
        ) {
            $banRequests = $this->messageExchanger->getMessages();


            foreach ($banRequests as $banRequest) {
                $response = $this->varnishCleaner->ban($banRequest);

                foreach ($response as $message) {
                    $this->logger->info($message);
                }
            }
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
            SymfonyKernelEvents::TERMINATE => 'onKernelTerminate',
        );
    }
}
