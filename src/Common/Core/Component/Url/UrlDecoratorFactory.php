<?php

namespace Common\Core\Component\Url;

use Common\Core\Component\Helper\UrlHelper;
use Common\Model\Entity\Instance;
use Symfony\Component\DependencyInjection\Container;

class UrlDecoratorFactory
{
    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The url helper.
     *
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * Initializes the UrlDecoratorFactory.
     *
     * @param Container $container The service container.
     * @param UrlHelper $urlHelper The url helper class.
     * @param Instance  $instance  The current instance.
     */
    public function __construct(Container $container, UrlHelper $urlHelper, Instance $instance)
    {
        $this->container = $container;
        $this->urlHelper = $urlHelper;
        $this->instance  = $instance;
    }

    /**
     * Returns the url decorator given an instance.
     *
     * @param Instance $instance The instance to get the decorator for.
     *
     * @return UrlDecorator The url decorator suitable for that instance.
     */
    public function getUrlDecorator()
    {
        $urlDecorator = null;

        if ($this->instance->hasMultilanguage()) {
            $urlDecorator = new UrlLocalizerDecorator($this->container, $this->urlHelper);
        }

        if ($this->instance->isSubdirectory()) {
            $urlDecorator = !empty($urlDecorator)
                ? new UrlSubdirectoryDecorator($this->container, $this->urlHelper, $urlDecorator)
                : new UrlSubdirectoryDecorator($this->container, $this->urlHelper);
        }

        return new UrlDecorator($this->container, $this->urlHelper, $urlDecorator ?? null);
    }
}
