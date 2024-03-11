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
     * Adds clickjacking headers to the response basing on if
     * is not an API JSON response or an ESI fragment
     *
     * @param FilterResponseEvent $event The current event.
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response            = $event->getResponse();
        $responseContentType = $response->headers->get('Content-Type');

        if ($this->isIgnoredContentType($responseContentType)) {
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

    /**
     * Check if given content type header is in excluded types list
     *
     * @param String $contentType The current content type header.
     * @return Boolean True if content type is list, false otherwise
     */
    protected function isIgnoredContentType($contentType)
    {
        if (empty($contentType)) {
            return true;
        }

        foreach ($this->ignoredContentTypes as $ignoredCT) {
            if (strpos($contentType, $ignoredCT) !== false) {
                return true;
            }
        }

        return false;
    }
}
