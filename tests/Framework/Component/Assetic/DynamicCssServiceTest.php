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

use \Framework\Component\Assetic\DynamicCssService;

class DynamicCssServiceTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'find', 'getDataSet' ])
            ->getMock();

        $this->settings = $this->getMockBuilder('DataSet')
            ->disableOriginalConstructor()
            ->setMethods([ 'get', 'set' ])
            ->getMock();

        $this->em->expects($this->any())
            ->method('getDataSet')
            ->willReturn($this->settings);

        $this->dcs = new DynamicCssService($this->em);
    }

    /**
     * Tests getTimestamp when empty timestamp settings
     */
    public function testGetTimestampWhenEmpty()
    {
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
