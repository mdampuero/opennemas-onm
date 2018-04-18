<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Core;

class GlobalVariables implements \ArrayAccess
{
    /**
     * The action name.
     *
     * @var string
     */
    protected $action;

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The endpoint name.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * The extension name.
     *
     * @var string
     */
    protected $extension;

    /**
     * The route name.
     *
     * @var string
     */
    protected $route;

    /**
     * Initializes the GlobalVariables.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns the last requested action.
     *
     * @return string The last requested action.
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Returns the service container.
     *
     * @return ServiceContainer The service container.
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Returns the endpoint name where the current request has been handled.
     *
     * @return string The endpoint name.
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Returns the current environment.
     *
     * @return string The current environment.
     */
    public function getEnvironment()
    {
        return $this->container->getParameter('kernel.environment');
    }

    /**
     * Returns the name of the extension that handles the current request.
     *
     * @return string The extension name.
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Returns the current instance.
     *
     * @return Instance The current instance.
     */
    public function getInstance()
    {
        return $this->container->get('core.instance');
    }

    /**
     * Returns the locale service.
     *
     * @return Locale The locale service.
     */
    public function getLocale()
    {
        return $this->container->get('core.locale');
    }

    /**
     * Returns the route name.
     *
     * @return string The route name.
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Returns the theme service.
     *
     * @return Locale The theme service.
     */
    public function getTheme()
    {
        return $this->container->get('core.theme');
    }

    /**
     * Returns the current user.
     *
     * @return User The current user.
     */
    public function getUser()
    {
        return $this->container->get('core.user');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $method = 'get' . ucfirst($offset);

        return method_exists($this, $method);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $method = 'get' . ucfirst($offset);

        if (!method_exists($this, $method)) {
            return null;
        }

        return $this->{$method}();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $method = 'set' . ucfirst($offset);

        if (method_exists($this, $method)) {
            $this->{$method}($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->offsetSet($offset, null);
    }

    /**
     * Sets the action that handled the current request.
     *
     * @param string $action The action name.
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Sets the endpoint where the request has been handled.
     *
     * @param string $endpoint The endpoint name.
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Sets the extensions that handled the current request.
     *
     * @param string $extension The extension name.
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * Sets the route name.
     *
     * @param string The route name.
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }
}
