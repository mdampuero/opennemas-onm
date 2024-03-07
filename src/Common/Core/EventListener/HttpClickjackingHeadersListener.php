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
    /**
     * Adds clickjacking headers to the response basing on if
     * is not an API JSON response or an ESI fragment
     *
     * @param FilterResponseEvent $event The current event.
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response            = $event->getResponse();
        $responseContentType = $response->headers->get('Content-Type');

        // Waits for the moment when the content-type info is available, to gather the data
        if (empty($responseContentType)) {
            return;
        }

        $request = $event->getRequest();
        $uri     = $request->getRequestUri();

        // Won't add the headers if it is an API JSON response or an ESI fragment, since it is not necessary.
        if (strpos($responseContentType, 'application/json') !== false || strpos($uri, '/widget/render/') !== false) {
            return;
        }

       // Adding header to avoid page from beign showed on iframe
        if (!$response->headers->has('X-Frame-Options')) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        }

        // Adding header with modern CSP to avoid clickjacking
        if (!$response->headers->has('Content-Security-Policy')) {
            $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");
        }
    }
}
