<?php

namespace Common\Core\Component\VDB\Spider;

use VDB\Spider\RequestHandler\GuzzleRequestHandler;
use VDB\Spider\Resource;
use VDB\Spider\Uri\DiscoveredUri;

class LinkCheckRequestHandler extends GuzzleRequestHandler
{
    /**
     * The base domain of the request.
     */
    private $domain;

    public function request(DiscoveredUri $uri)
    {
        $response = $this->getClient()->get($uri->toString(), [
            'http_errors'     => false,
            'base_uri'        => $this->domain,
            'allow_redirects' => false
        ]);

        return new Resource($uri, $response);
    }

    /**
     * Configures the base domain of the request.
     *
     * @param string $domain The base domain of the request.
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Returns the base domain of the request handler.
     *
     * @return string The base domain for the requests.
     */
    public function getDomain()
    {
        return $this->domain;
    }
}
