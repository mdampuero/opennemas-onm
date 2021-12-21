<?php

namespace Common\Core\Component\Url;

use Common\Core\Component\Helper\UrlHelper;
use Symfony\Component\DependencyInjection\Container;

class UrlDecorator
{
    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The inner url decorator.
     *
     * @var UrlDecorator
     */
    protected $urlDecorator;

    /**
     * The url helper.
     *
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * Initialize the url decorator.
     *
     * @param Container    $container    The service container.
     * @param UrlDecorator $urlDecorator An inner url decorator.
     * @param UrlHelper    $urlHelper An inner url decorator.
     */
    public function __construct(Container $container, UrlHelper $urlHelper, UrlDecorator $urlDecorator = null)
    {
        $this->urlHelper    = $urlHelper;
        $this->urlDecorator = $urlDecorator;
        $this->container    = $container;
    }

    /**
     * Returns the url prefixed.
     *
     * @param String url The url to prefix.
     *
     * @return String The url prefixed.
     */
    public function prefixUrl(string $url, string $route = '')
    {
        if (!empty($this->urlDecorator)) {
            $url = $this->urlDecorator->prefixUrl($url, $route);
        }

        return $url;
    }
}
