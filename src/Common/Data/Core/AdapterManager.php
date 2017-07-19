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

use Common\Data\Core\Exception\InvalidAdapterException;

/**
 * The `AdapterManager` class provides methods to adapt values from a deprecated
 * format to the up-to-date format.
 */
class AdapterManager
{
    /**
     * The service container.
     *
     * @var ServiceCotainer
     */
    protected $container;

    /**
     * Initializes the AdapterManager.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Applies an adapter to a value.
     *
     * @param string $name  The adapter name.
     * @param mixed  $value The value to adapt.
     * @param mixed  $args  The arguments for adapter.
     *
     * @return mixed The filtered value.
     */
    public function adapt($name, $value, $args = [])
    {
        $class = str_replace('Core', 'Adapter', __NAMESPACE__)
            . '\\' . \classify($name) . 'Adapter';

        if (class_exists($class)) {
            $adapter = new $class($this->container, $args);

            return $adapter->adapt($value);
        }

        throw new InvalidAdapterException($name);
    }
}
