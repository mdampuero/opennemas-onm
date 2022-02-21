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

        if (!array_key_exists('path', $parts) || empty($parts['path']) || !$this->isDecorable($parts['path'])) {
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
     * @param string $path The path to check if is decorable.
     *
     * @return bool True if the url can be decorated, false otherwise.
     */
    private function isDecorable(string $path)
    {
        try {
            $path       = preg_replace('@/$@', '', $path);
            $parameters = $this->container->get('router')->match($path);
            $routeName  = $parameters['_route'];
        } catch (\Exception $e) {
            return false;
        }

        $routes = array_filter(
            $this->container->get('router')->getRouteCollection()->all(),
            function ($route) {
                return true === $route->getOption('subdirectory');
            }
        );

        return in_array($routeName, array_keys($routes));
    }
}
