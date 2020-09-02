<?php

namespace Common\Core\Component\VDB\Spider;

use VDB\Spider\RequestHandler\GuzzleRequestHandler;
use VDB\Spider\Resource;
use VDB\Spider\Uri\DiscoveredUri;

class LinkCheckRequestHandler extends GuzzleRequestHandler
{
    public function request(DiscoveredUri $uri)
    {
        $response = $this->getClient()->get($uri->toString(), ['http_errors' => false]);
        return new Resource($uri, $response);
    }
}
