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
     * Initializes the UrlGeneratorHelper.
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
     * @param string $content The content to generate the url.
     * @param array  $params  The list of params required to generate the URI.
     *
     * @return string The generated URI.
     */
    public function localizeUrl($url, $routeName = '')
    {
        // L10n for urls
        $requestedLocale = $this->container
            ->get('request_stack')->getCurrentRequest()
            ->attributes->get('_locale', '');

        // If no locale skip the l10n setting
        if (empty($requestedLocale)) {
            return $url;
        }

        $localeSettings = $this->container
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('locale');

        $localeForUri = '';
        if (is_array($localeSettings)
            && is_array($localeSettings)
            && array_key_exists('main', $localeSettings)
            && $requestedLocale !== $localeSettings['main']
            && in_array($requestedLocale, $localeSettings['frontend'])
        ) {
            $localeForUri = $requestedLocale;
        }

        $routes = [];
        if (!empty($routeName)) {
            $routes = $this->getLocalizableRoutes();
        }

        // Only localize urls if the user comes from a localized site
        // and if the url can be localized
        if (!empty($localeForUri)
            && (
                (!empty($routeName) && in_array($urlName, $routes))
                || (empty($routeName))
            )
        ) {
            // Append the locale for uri to the url path part
            if ($forceAbsolute) {
                $parts         = parse_url($url);
                $parts['path'] = $localeForUri . $parts['path'];
                $url           = implode('/', $parts);

                return $url;
            }

            $url = '/' . $localeForUri . $url;
        }

        return $url;
    }

    private function getLocalizableRoutes()
    {
        // Get the list of routes that could be localized
        $routes = array_filter(
            $this->container->get('router')->getRouteCollection()->all(),
            function ($route) {
                return true === $route->getOption('l10n')
                    || 'true' === $route->getOption('l10n');
            }
        );

        return array_keys($routes);
    }
}
