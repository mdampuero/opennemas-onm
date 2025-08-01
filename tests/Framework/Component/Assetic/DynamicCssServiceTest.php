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

use Api\Exception\GetItemException;
use \Framework\Component\Assetic\DynamicCssService;
use Common\Model\Entity\Category;

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

        $this->cs = $this->getMockBuilder('Api\Service\V1\CategoryService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItemBySlug' ])
            ->getMock();

        $this->em->expects($this->any())
            ->method('getDataSet')
            ->willReturn($this->settings);

        $category = new Category([ 'id' => 1 ]);

        $this->cs->expects($this->any())
            ->method('getItemBySlug')
            ->willReturn($category);

        $this->dcs = new DynamicCssService($this->em, $this->cs);
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
        $timestamps['1'] = '1583322926';
        $this->settings->expects($this->once())
            ->method('get')
            ->with('dynamic_css', [])
            ->willReturn($timestamps);

        $this->assertEquals($timestamps['1'], $this->dcs->getTimestamp('section'));
    }

    /**
     * Tests getTimestamp when special category
     */
    public function testGetTimestampWhenSpecialCategory()
    {
        $this->settings->expects($this->once())->method('get')
            ->with('dynamic_css', [])
            ->willReturn(['home' => '1586787581']);

        $this->assertEquals('1586787581', $this->dcs->getTimestamp('home'));
    }

    /**
     * Tests getTimestamp when invalid section
     */
    public function testGetTimestampWhenInvalidSection()
    {
        $this->cs->expects($this->at(0))->method('getItemBySlug')
            ->will($this->throwException(new GetItemException()));

        $this->dcs->getTimestamp(null);
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
