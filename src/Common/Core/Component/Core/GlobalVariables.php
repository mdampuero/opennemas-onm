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
     * The device type.
     *
     * @var string
     */
    protected $device;

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
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The section name.
     *
     * @var string
     */
    protected $section;

    /**
     * The current request.
     *
     * @var Request
     */
    protected $request;

    /**
     * The route name.
     *
     * @var string
     */
    protected $route;

    /**
     * The current theme.
     *
     * @var Theme
     */
    protected $theme;

    /**
     * The current user
     *
     * @var user
     */
    protected $user;

    /**
     * Initializes the GlobalVariables.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->request   = $container->get('request_stack')->getCurrentRequest();
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
     * Returns the current advertisement group name for a page.
     *
     * @return string The advertisement group name.
     */
    public function getAdvertisementGroup()
    {
        return $this->container->get('core.helper.advertisement')->getGroup();
    }

    /**
     * Returns the service container.
     *
     * @return CategoryService The Category service.
     */
    public function getCategories()
    {
        return $this->container->get('api.service.category');
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
     * Returns the device type.
     *
     * @return string The device type.
     */
    public function getDevice()
    {
        return $this->device;
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
        return $this->instance;
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
     * Returns the current request.
     *
     * @return Request The current request.
     */
    public function getRequest()
    {
        return $this->request;
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
     * Returns the section name.
     *
     * @return string The section name.
     */
    public function getSection()
    {
        $template = $this->container->get('core.template');

        if ($template->hasValue('o_category')) {
            return $template->getValue('o_category')->name;
        }

        return 'home';
    }

    /**
     * Returns the security service.
     *
     * @return Security The security service.
     */
    public function getSecurity()
    {
        return $this->container->get('core.security');
    }

    /**
     * Returns the subscriptions helper.
     *
     * @return SubscriptionHelper The subscription helper.
     */
    public function getSubscription()
    {
        return $this->container->get('core.helper.subscription');
    }

    /**
     * Returns the theme service.
     *
     * @return Theme The theme service.
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Returns the current user.
     *
     * @return User The current user.
     */
    public function getUser()
    {
        return $this->user;
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
     * Sets the device type for the current request.
     *
     * @return string $device The device type.
     */
    public function setDevice($device)
    {
        $this->device = $device;
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
     * Sets the instance.
     *
     * @param Instance The instance.
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
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

    /**
     * Sets the theme.
     *
     * @param Theme The theme.
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * Sets the user.
     *
     * @param User The user.
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
