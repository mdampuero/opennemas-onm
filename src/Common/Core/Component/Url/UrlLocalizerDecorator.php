<?php

namespace Common\Core\Component\Url;

class UrlLocalizerDecorator extends UrlDecorator
{
    /**
     * The list of routes that can be localized.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * {@inheritdoc}
     */
    public function prefixUrl(string $url)
    {
        if (!empty($this->urlDecorator)) {
            $url = $this->urlDecorator->prefixUrl($url);
        }

        $routeName = '';
        $parts     = $this->urlHelper->parse($url);

        if (array_key_exists('path', $parts) && !empty($parts['path'])) {
            try {
                $path       = preg_replace('@/$@', '', $parts['path']);
                $parameters = $this->container->get('router')->match($path);
                $routeName  = $parameters['_route'];
            } catch (\Exception $e) {
                return $url;
            }
        }

        $localeService = $this->container->get('core.locale');
        $defaultLocale = $localeService->getLocale('frontend');
        $requestLocale = $localeService->getRequestLocale();
        $slugs         = $localeService->getSlugs();

        // If locale is the default locale skip the l10n setting
        if ($requestLocale === $defaultLocale
            || !array_key_exists($requestLocale, $slugs)
        ) {
            return $url;
        }

        // Localize if unknown route or route can be localized
        if (in_array($routeName, $this->container->get('core.helper.l10n_route')->getLocalizableRoutes())
        ) {
            $parts['path'] = '/' . $slugs[$requestLocale]
                . (array_key_exists('path', $parts) ? $parts['path'] : '');

            $url = $this->urlHelper->unparse($parts);
        }

        return $url;
    }
}
