<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Template;

class TemplateFactory
{
    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The list of configured templates.
     *
     * @var array
     */
    protected $templates = [];

    /**
     * The Stopwatch service.
     *
     * @var Stopwatch
     */
    protected $watcher;

    /**
     * Initializes the TemplateFactory.
     *
     * @param Container $container The service container.
     */
    public function __construct($container, $watcher)
    {
        $this->container = $container;

        if ($this->container->getParameter('kernel.environment') === 'dev') {
            $this->watcher = $watcher;
        }
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

        if ($method === 'fetch' && !empty($this->watcher)) {
            $this->watcher->start("template ({$bundleName} {$params[0]})");
        }

        $template = $this->get();
        $response = call_user_func_array([ $template, $method ], $params);

        if ($method === 'fetch' && !empty($this->watcher)) {
            $this->watcher->stop("template ({$bundleName} {$params[0]})");
        }

        return $response;
    }

    /**
     * Returns a Template service based on the name.
     *
     * @param string $name The template name.
     *
     * @return Template The Template service.
     */
    public function get(string $name = null) : Template
    {
        $name = $this->getInternalName($name);

        if (array_key_exists($name, $this->templates)) {
            return $this->templates[$name];
        }

        return $this->getTemplate($name);
    }

    /**
     * Returns the bundle name based on the current request.
     *
     * @return string The bundle name.
     */
    protected function getBundleName() : string
    {
        $request = $this->container->get('request_stack')
            ->getCurrentRequest();

        if (empty($request)) {
            return 'frontend';
        }

        $controller = $request->get('_controller');
        $controller = explode('\\', $controller);

        return strtolower($controller[0]);
    }

    /**
     * Returns the internal name for a template based on a human readable name.
     *
     * @param string $name The human readable name.
     *
     * @return string The internal template name.
     */
    protected function getInternalName(?string $name) : string
    {
        $name = !empty($name) ? $name : $this->getBundleName();

        if (empty($name) || $name === 'Frontend' || $name === 'frontend') {
            return 'frontend';
        }

        return preg_replace(
            [ '/webservice/', '/core\.template\./', '/backend/' ],
            [ '', '', 'admin' ],
            $name
        );
    }

    /**
     * Returns a Template configured with the current instance and the current
     * theme based on the template name.
     *
     * @param string $name The template name.
     *
     * @return Template The configured Template.
     */
    protected function getTemplate($name) : Template
    {
        $filters = 'core.template.' . $name . '.filters';

        $uuid = $name === 'frontend'
            ? $this->container->get('core.instance')->settings['TEMPLATE_USER']
            : 'es.openhost.theme.' . $name;

        $filters = $this->container->hasParameter($filters)
            ? $this->container->getParameter($filters)
            : [];

        $template = new Template($this->container, $filters);

        $template->addInstance($this->container->get('core.instance'));

        try {
            $theme = $this->container->get('orm.manager')
                ->getRepository('theme', 'file')
                ->findOneBy('uuid = "' . $uuid . '"');

            $template->addActiveTheme($theme);
        } catch (\Exception $e) {
            // Prevents notices for Symfony profiler requests
        }

        $this->templates[$name] = $template;

        return $template;
    }
}
