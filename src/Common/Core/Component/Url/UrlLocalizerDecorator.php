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
    public function prefixUrl(string $url, string $routeName = '')
    {
        if (!empty($this->urlDecorator)) {
            $url = $this->urlDecorator->prefixUrl($url, $routeName);
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
        if (empty($routeName)
            || in_array($routeName, $this->container->get('core.helper.l10n_route')->getLocalizableRoutes())
        ) {
            $parts = $this->urlHelper->parse($url);

            $parts['path'] = '/' . $slugs[$requestLocale]
                . (array_key_exists('path', $parts) ? $parts['path'] : '');

            $url = $this->urlHelper->unparse($parts);
        }

        return $url;
    }
}
