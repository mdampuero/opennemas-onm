<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Data\Core;

use Common\Core\Component\Exception\Filter\InvalidFilterException;

/**
 * The FilterManager applies filters to values.
 */
class FilterManager
{
    /**
     * The value to filter.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Initalizes the filter manager.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Applies a filter to a value.
     *
     * @param string $name  The filter name.
     * @param mixed  $value The value to filter.
     * @param mixed  $args  The arguments for filter.
     *
     * @return mixed The filtered value.
     *
     * @throw InvalidFilterException
     */
    public function filter($name, $args = [])
    {
        if (empty($this->value)) {
            return $this;
        }

        $class = str_replace('Core', 'Filter', __NAMESPACE__)
            . '\\' . \classify($name) . 'Filter';

        if (!class_exists($class)) {
            throw new InvalidFilterException($name);
        }

        $filter = new $class($this->container, $args);

        $this->value = $filter->filter($this->value);

        return $this;
    }

    /**
     * Returns the current value to filter.
     *
     * @return mixed The current value to filter.
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Changes the current value to filter.
     *
     * @param mixed $value The current value to filter.
     *
     * @return mixed
     */
    public function set($value)
    {
        $this->value = $value;

        return $this;
    }
}
