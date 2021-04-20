<?php

namespace Common\Core\Component\DataLayer;

use Common\Model\Entity\Instance;
use Symfony\Component\DependencyInjection\Container;

/**
 * A factory to return an instance of the specific data layer service basing on enabled modules.
 */
class DataLayerFactory
{
    /**
     * The service container.
     *
     * @var Container
     *
     */
    protected $container;

    /**
     * The current instance.
     *
     * @var Instance
     *
     */
    protected $instance;

    /**
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->instance  = $this->container->get('core.instance');
    }

    /**
     * Returns the data layer service.
     *
     * @return DataLayer The data layer service.
     */
    public function getDataLayer()
    {
        $modules = $this->instance->activated_modules;

        if (in_array('es.openhost.module.dataLayerHenneo', $modules)) {
            return new DataLayerHenneo($this->container);
        }

        return new DataLayer($this->container);
    }
}
