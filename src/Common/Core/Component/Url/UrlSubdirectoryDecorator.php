<?php

namespace Common\Core\Component\Url;

class UrlSubdirectoryDecorator extends UrlDecorator
{
    /**
     * {@inheritdoc}
     */
    protected $urlDecorator;

    /**
     * {@inheritdoc}
     */
    protected $urlHelper;

    /**
     * {@inheritdoc}
     */
    public function prefixUrl(string $url, string $routeName = '')
    {
        if (!empty($this->urlDecorator)) {
            $url = $this->urlDecorator->prefixUrl($url, $routeName);
        }

        $parts = $this->urlHelper->parse($url);

        $parts['path'] = $this->container->get('core.instance')->subdirectory
            . $parts['path'];

        $url = $this->urlHelper->unparse($parts);

        return $url;
    }
}
