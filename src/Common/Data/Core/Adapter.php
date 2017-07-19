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

/**
 * The `Adapter` class defines a common actions to adapt items from an
 * deprecated value and/or format to the up-to-date value and/or format.
 */
abstract class Adapter
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the Adapter.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns an item after executing some conversions.
     *
     * @param mixed $item   The item to adapt.
     * @param array $params The array of parameters to use during conversion.
     *
     * @return mixed The adapted item.
     */
    abstract public function adapt($item, $params = []);
}
