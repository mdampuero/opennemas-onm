<?php

/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Framework\Component\Assetic;

class DynamicCssServiceTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityRepository')
            ->setMethods([ 'find', 'getDataSet' ])
            ->getMock();

        $this->settings = $this->getMockBuilder('DataSet')
            ->disableOriginalConstructor()
            ->setMethods([ 'get', 'set' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([$this, 'serviceContainerCallback']));

        $this->em->expects($this->any())
            ->method('getDataSet')
            ->willReturn($this->settings);

        $this->dcs = $this->getMockBuilder('Framework\Component\Assetic\DynamicCssService')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'get' ])
            ->getMock();
    }

    /**
     * Returns a mocked service basing on the service name
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'orm.manager':
                return $this->em;

            default:
                return null;
        }
    }

    /**
     * Tests getTimestamp when empty timestamp settings
     */
    public function testGetTimestampWhenEmpty()
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with('orm.manager')
            ->willReturn($this->em);

        $this->em->expects($this->once())
            ->method('getDataset')
            ->with('Settings', 'instance');

        $this->settings->expects($this->once())
            ->method('get')
            ->willReturn([]);

        $this->settings->expects($this->once())
            ->method('set');

        $datetime = new \Datetime();
        $this->assertEquals($datetime->getTimestamp(), $this->dcs->getTimestamp('section'));
    }

    /**
     * Tests getTimestamp when timestamp exists
     */
    public function testGetTimestampWhenExists()
    {
        $timestamps['section'] = '1583322926';
        $this->settings->expects($this->once())
            ->method('get')
            ->with('dynamic_css', [])
            ->willReturn($timestamps);

        $this->assertEquals($timestamps['section'], $this->dcs->getTimestamp('section'));
    }

    /**
     * Tests deleteTimestamp action
     */
    public function testDeleteTimestamp()
    {
        $timestamps['section']   = '1583322930';
        $timestamps['nosection'] = '1583322920';

        $this->settings->expects($this->once())
            ->method('get')
            ->with('dynamic_css', [])
            ->willReturn($timestamps);

        unset($timestamps['section']);

        $this->settings->expects($this->once())
            ->method('set')
            ->with('dynamic_css', $timestamps);

        $this->dcs->deleteTimestamp('section');
    }
}
