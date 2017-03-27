<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Filter;

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
        $this->container = $container;

        if (!is_array($params)) {
            $message = 'Filter expects an argument of type array. '
                . gettype($params) . ' given.';

            throw new \InvalidArgumentException($message);
        }

        $this->params = $params;
    }

    /**
     * Returns the parameter give a name.
     *
     * @param string $name    The parameter name.
     * @param mixed  $default The default value for the parameter.
     *
     * @return mixed If the parameter exists, return the value. Otherwise,
     *               returns the default value.
     */
    public function getParameter($name, $default = false)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }

        if ($this->container->hasParameter($name)) {
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
