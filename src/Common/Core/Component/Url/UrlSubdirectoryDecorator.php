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
    public function prefixUrl(string $url)
    {
        if (!empty($this->urlDecorator)) {
            $url = $this->urlDecorator->prefixUrl($url);
        }

        $parts = $this->urlHelper->parse($url);

        if (!$this->isDecorable()) {
            return $url;
        }

        $parts['path'] = $this->container->get('core.instance')->subdirectory
            . $parts['path'];

        $url = $this->urlHelper->unparse($parts);

        return $url;
    }

    /**
     * Returns true if the url can be decorated and false otherwise.
     *
     * @return bool True if the url can be decorated, false otherwise.
     */
    private function isDecorable()
    {
        if ($this->container->get('core.locale')->getContext() === 'backend') {
            return false;
        }

        return true;
    }
}
