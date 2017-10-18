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
     * Returns a localized url
     *
     * @param string $url The url to localize.
     * @param array  $routeName Route name to know if the route should be localized or not.
     *
     * @return string The generated URI.
     */
    public function localizeUrl($url, $routeName = '', $forceAbsolute = false)
    {
        // L10n for urls
        $requestedLocale = $this->container
            ->get('request_stack')->getCurrentRequest()
            ->attributes->get('_locale', '');

        $localeService = $this->container->get('core.locale');
        $requestLocale = $localeService->getRequestLocale();
        $defaultLocale = $localeService->getLocale('frontend');

        // If no locale skip the l10n setting
        if ($requestLocale == $defaultLocale
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
            // Append the locale for uri to the url path part
            if ($forceAbsolute) {
                $parts         = parse_url($url);
                $parts['path'] = $localeForUri . $parts['path'];
                $url           = implode('/', $parts);

                return $url;
            }

            $url = '/' . $requestLocale . $url;
        }

        return $url;
    }

    /**
     * Returns the list of localizable routes
     *
     * @return array the list of localizable routes
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
}
