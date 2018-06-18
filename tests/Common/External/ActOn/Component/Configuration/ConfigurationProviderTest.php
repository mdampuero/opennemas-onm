<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\External\ActOn\Component\Configuration;

use Common\External\ActOn\Component\Configuration\OrmConfigurationProvider;

/**
 * Defines test cases for OrmConfigurationTest class.
 */
class OrmConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->dataset = $this->getMockBuilder('DataSet' . uniqid())
            ->setMethods([ 'delete', 'get', 'set' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->dataset);

        $this->provider = new OrmConfigurationProvider($this->em);
    }

    /**
     * Tests getConfiguration.
     */
    public function testGetConfiguration()
    {
        $this->dataset->expects($this->once())->method('get')
            ->with('act_on_configuration')->willReturn([ 'token' => 'glorp' ]);

        $this->assertEquals([ 'token' => 'glorp' ], $this->provider->getConfiguration());
    }

    /**
     * Tests setConfiguration when empty value provided.
     */
    public function testSetConfigurationWhenEmpty()
    {
        $this->dataset->expects($this->once())->method('delete')
            ->with('act_on_configuration');

        $this->provider->setConfiguration(null);
    }


    /**
     * Tests setConfiguration when value provided.
     */
    public function testSetConfigurationWhenValueProvided()
    {
        $this->dataset->expects($this->once())->method('set')
            ->with('act_on_configuration', [ 'token' => 'glorp' ]);

        $this->provider->setConfiguration([ 'token' => 'glorp' ]);
    }
}
