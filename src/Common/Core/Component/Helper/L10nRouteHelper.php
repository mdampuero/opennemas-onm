<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

class L10nRouteHelper
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The list of routes that can be localized.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Initializes the L10nRouteHelper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns the list of localizable routes.
     *
     * @return array The list of localizable routes.
     */
    public function getLocalizableRoutes()
    {
        // Get the list of routes that could be localized
        if (empty($this->routes)) {
            $routes = array_filter(
                $this->container->get('router')->getRouteCollection()->all(),
                function ($route) {
                    return true === $route->getOption('l10n')
                        || 'true' === $route->getOption('l10n');
                }
            );

            $this->routes = array_keys($routes);
        }

        return $this->routes;
    }

    /**
     * Returns a localized url.
     *
     * @param string  $url           The url to localize.
     * @param array   $routeName     Route name to know if the route should be
     *                               localized or not.
     * @param boolean $forceAbsolute Whether the route has to be absolute.
     *
     * @return string The generated URI.
     */
    public function localizeUrl($url, $routeName = '')
    {
        $localeService = $this->container->get('core.locale');
        $requestLocale = $localeService->getRequestLocale();
        $defaultLocale = $localeService->getLocale('frontend');
        $urlHelper     = $this->container->get('core.helper.url');

        // If no locale or locale is the default locale skip the l10n setting
        if ($requestLocale === $defaultLocale
            || !in_array($requestLocale, $localeService->getSlugs())
        ) {
            return $url;
        }

        $routes = [];
        if (!empty($routeName)) {
            $routes = $this->getLocalizableRoutes();
        }

        // Only localize urls if the user comes from a localized site
        // and if the url can be localized
        if ((!empty($routeName) && in_array($routeName, $routes))
            || empty($routeName)
        ) {
            $parts = $urlHelper->parse($url);

            $parts['path'] = '/' . $requestLocale
                . (array_key_exists('path', $parts) ? $parts['path'] : '');

            $url = $urlHelper->unparse($parts);
        }

        return $url;
    }
}
