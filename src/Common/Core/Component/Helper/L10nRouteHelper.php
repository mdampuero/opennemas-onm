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
}
