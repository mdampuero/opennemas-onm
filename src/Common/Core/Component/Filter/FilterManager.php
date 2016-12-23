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

use Common\Core\Component\Exception\Filter\InvalidFilterException;

/**
 * The FilterManager applies filters to values.
 */
class FilterManager
{
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
     */
    public function filter($name, $value, $args = [])
    {
        $class = __NAMESPACE__ . '\\' . \classify($name) . 'Filter';

        if (class_exists($class)) {
            $filter = new $class($this->container, $args);

            return $filter->filter($value);
        }

        throw new InvalidFilterException($name);
    }
}
