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
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

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

        $template = $this->getTemplate($bundleName);
        $response = call_user_func_array([ $template, $method ], $params);

        if ($method === 'fetch' && $this->container->has('debug.stopwatch')) {
            $stopwatch = $this->container->get('debug.stopwatch');
            $stopwatch->stop("template ({$bundleName} {$params[0]})");
        }

        return $response;
    }

    /**
     * Returns the template service for backend.
     *
     * @return Template The template service.
     */
    public function getBackendTemplate()
    {
        $template = $this->container->get('core.template.admin');

        if (empty($template->getTheme())) {
            $theme = $this->container->get('orm.manager')
                ->getRepository('theme', 'file')
                ->findOneBy('uuid = "es.openhost.theme.admin"');

            $template->addInstance($this->container->get('core.instance'));
            $template->addActiveTheme($theme);
        }

        return $template;
    }

    /**
     * Returns the template service for manager.
     *
     * @return Template The template service.
     */
    public function getManagerTemplate()
    {
        $template = $this->container->get('core.template.manager');

        if (empty($template->getTheme())) {
            $theme = $this->container->get('orm.manager')
                ->getRepository('theme', 'file')
                ->findOneBy('uuid = "es.openhost.theme.manager"');

            $template->addInstance($this->container->get('core.instance'));
            $template->addActiveTheme($theme);
        }

        return $template;
    }

    /**
     * Returns the template service basing on the module name.
     *
     * @param string $module The module name.
     *
     * @return mixed The template service.
     */
    public function getTemplate($module = null)
    {
        if ($module === 'Manager' || $module === 'ManagerWebService') {
            return $this->getManagerTemplate();
        }

        if ($module === 'Backend' || $module === 'BackendWebService') {
            return $this->getBackendTemplate();
        }

        $template = $this->container->get('core.template');

        if (empty($template->getInstance())
            || $template->getInstance() !== $this->container->get('core.instance')
        ) {
            $template->addInstance($this->container->get('core.instance'));
        }

        if (empty($template->getTheme())
            || $template->getTheme() !== $this->container->get('core.theme')
        ) {
            $template->addActiveTheme($this->container->get('core.theme'));
        }

        return $template;
    }

    /**
     * Returns the bundle name from the matched controller.
     *
     * @return string The bundle name.
     */
    protected function getBundleName()
    {
        $controller = $this->container->get('request_stack')
            ->getCurrentRequest()->get('_controller');
        $controller = explode('\\', $controller);

        return $controller[0];
    }
}
