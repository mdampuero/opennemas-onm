<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HttpClickjackingHeadersListener
{
    protected $ignoredContentTypes = [ 'json', 'xml' ];

    /**
     * The service container.
     *
     * @param \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * Initializes the AuthenticationListener.
     *
     * @param \Symfony\Component\DependencyInjection\Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }


    /**
     * Adds clickjacking headers to the response basing on if
     * is not an API JSON response or an ESI fragment
     *
     * @param FilterResponseEvent $event The current event.
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $uri                 = $event->getRequest()->getRequestUri();
        $response            = $event->getResponse();
        $responseContentType = $response->headers->get('Content-Type');

        // Checks if it is a frontend uri or if it is the desired content-type
        if (!$this->container->get('core.helper.url')->isFrontendUri($uri)
            || $this->isIgnoredContentType($responseContentType)
        ) {
            return;
        }

       // Force headeres in order to avoid page from beign showed on iframe
       // Adding header with modern CSP to avoid clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");
    }

    /**
     * Check if given content type header is in excluded types list
     *
     * @param String $contentType The current content type header.
     * @return Boolean True if content type is list, false otherwise
     */
    protected function isIgnoredContentType($contentType)
    {
        foreach ($this->ignoredContentTypes as $ignoredCT) {
            if (strpos($contentType, $ignoredCT) !== false) {
                return true;
            }
        }

        return false;
    }
}
