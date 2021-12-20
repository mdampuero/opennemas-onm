<?php

namespace Common\Core\Component\Url;

use Common\Core\Component\Helper\UrlHelper;
use Common\Model\Entity\Instance;

class UrlDecoratorFactory
{
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
     * @param UrlHelper $urlHelper The url helper class.
     * @param Instance  $instance The current instance.
     */
    public function __construct(UrlHelper $urlHelper, Instance $instance)
    {
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
            $urlDecorator = new UrlLocalizerDecorator($this->urlHelper);
        }

        if ($this->instance->isSubdirectory()) {
            $urlDecorator = !empty($urlDecorator)
                ? new UrlSubdirectoryDecorator($this->urlHelper, $urlDecorator)
                : new UrlSubdirectoryDecorator($this->urlHelper);
        }

        return new UrlDecorator($this->urlHelper, $urlDecorator ?? null);
    }
}
