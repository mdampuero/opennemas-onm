<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\AdvertisementHelper;

/**
 * Defines test cases for class class.
 */
class AdvertisementHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->instanceConnection = $this->getMockBuilder('Converter' . uniqid())
            ->setMethods([ 'objectify', 'responsify' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->helper = new AdvertisementHelper($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'orm.connection.instance':
                return $this->instanceConnection;
        }

        return null;
    }

    /**
     * Tests helper constructor.
     */
    public function testConstructor()
    {
        $this->assertAttributeEquals($this->container, 'container', $this->helper);
        $this->assertAttributeEquals($this->instanceConnection, 'conn', $this->helper);
    }

    /**
     * Tests getPositions.
     */
    public function testGetPositions()
    {
        $this->assertEquals([], $this->helper->getPositions());
    }
}
