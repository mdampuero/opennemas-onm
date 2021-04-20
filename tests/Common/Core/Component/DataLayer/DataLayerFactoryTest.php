<?php

namespace Tests\Common\Core\Component\DataLayer;

use Common\Core\Component\DataLayer\DataLayer;
use Common\Core\Component\DataLayer\DataLayerFactory;
use Common\Core\Component\DataLayer\DataLayerHenneo;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for DataLayerFactory class.
 */
class DataLayerFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->dataset = $this->getMockBuilder('Opennemas\Orm\Core\DataSet')
            ->setMethods([ 'delete', 'get', 'init', 'set' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->instance = new Instance();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->dataset);

        $this->dlf = new DataLayerFactory($this->container);
    }

    /**
     * Returns custom service basing on the string passed to the container.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.instance':
                return $this->instance;

            case 'orm.manager':
                return $this->em;

            default:
                return null;
        }
    }

    /**
     * Tests getDataLayer when is the standard data layer.
     */
    public function testGetDataLayerWhenStandard()
    {
        $this->instance->activated_modules = [];

        $this->assertEquals(new DataLayer($this->container), $this->dlf->getDataLayer());
    }

    /**
     * Tests getDataLayer when is the henneo data layer.
     */
    public function testGetDataLayerWhenHenneo()
    {
        $this->instance->activated_modules = [ 'es.openhost.module.dataLayerHenneo' ];

        $this->assertEquals(new DataLayerHenneo($this->container), $this->dlf->getDataLayer());
    }
}
