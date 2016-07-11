<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Templating;

class Templating
{
    /**
     * The container that this class will use to fetch services
     *
     * @var string
     */
    public $container;

    /**
     * Initializes the Templating.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Redirects function calls to the right Template service.
     *
     * @param string $method The method name.
     * @param array  $params The method parameters.
     *
     * @return mixed The template engine response.
     */
    public function __call($method, $params)
    {
        $bundleName = $this->getBundleName();

        if ($method === 'fetch' && $this->container->has('debug.stopwatch')) {
            $stopwatch = $this->container->get('debug.stopwatch');
            $stopwatch->start("template ({$bundleName} {$params[0]})");
        }

        $template = $this->getTemplateObject($bundleName);
        $response = call_user_func_array([ $template, $method ], $params);

        if ($method === 'fetch' && $this->container->has('debug.stopwatch')) {
            $stopwatch = $this->container->get('debug.stopwatch');
            $stopwatch->stop("template ({$bundleName} {$params[0]})");
        }

        return $response;
    }

    /**
     * Returns the bundle name from the matched controller.
     *
     * @return string The bundle name.
     */
    protected function getBundleName()
    {
        $controller = $this->container->get('request')->get('_controller');
        $controllerNameParts = explode('\\', $controller);

        return $controllerNameParts[0];
    }

    /**
     * Returns the template service basing on the module name.
     *
     * @param string $module The module name.
     *
     * @return mixed The template service.
     */
    protected function getTemplateObject($module)
    {
        if ($module === 'Manager' || $module === 'ManagerWebService') {
            return $this->container->get('core.template.manager');
        }

        if ($module === 'Backend' || $module === 'BackendWebService') {
            return $this->container->get('core.template.admin');
        }

        return $this->container->get('core.template');
    }
}
