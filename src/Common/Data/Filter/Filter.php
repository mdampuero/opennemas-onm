<?php

namespace Common\Data\Filter;

abstract class Filter
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The filter parameters.
     *
     * @var array
     */
    protected $params;

    /**
     * Initializes the Filter.
     *
     * @param array $params The filter parameters.
     */
    public function __construct($container, $params = [])
    {
        if (!is_array($params)) {
            $message = 'Filter expects an argument of type array. '
                . gettype($params) . ' given.';

            throw new \InvalidArgumentException($message);
        }

        $this->container = $container;
        $this->params    = $params;
    }

    /**
     * Returns the parameter give a name.
     *
     * @param string  $name      The parameter name.
     * @param mixed   $default   The default value for the parameter.
     * @param boolean $container Whether to search the parameter in the
     *                           service container.
     *
     * @return mixed If the parameter exists, return the value. Otherwise,
     *               returns the default value.
     */
    public function getParameter($name, $default = false, $container = true)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }

        if ($container && $this->container->hasParameter($name)) {
            return $this->container->getParameter($name);
        }

        return $default;
    }

    /**
     * Filters a string.
     *
     * @param string $str The string to filter.
     *
     * @return string The transformed string.
     */
    abstract public function filter($str);
}
