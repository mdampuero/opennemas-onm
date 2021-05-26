<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\SettingHelper;
use Common\Core\Component\Helper\ContentHelper;
use Common\Model\Entity\Content;

/**
 * Defines test cases for SettingHelper class.
 */
class SettingHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' , 'getParameter'])
            ->getMock();

        $this->ch = $this->getMockBuilder('ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContent' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->orm = $this->getMockBuilder('OrmEntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->orm->expects($this->any())->method('getDataSet')
            ->willReturn($this->ds);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->helper = new SettingHelper($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.helper.content':
                return $this->ch;

            case 'orm.manager':
                return $this->orm;
        }

        return null;
    }

    public function testGetLogoWhenExists()
    {
        $photo = new Content(['id' => 1]);

        $this->ds->expects($this->at(0))->method('get')
            ->with('logo_enabled')->willReturn(true);

        $this->ds->expects($this->at(1))->method('get')
            ->with('site_logo')->willReturn(1);

        $this->ch->expects($this->once())->method('getContent')
            ->with(1, 'photo')->willReturn($photo);

        $this->assertEquals($photo, $this->helper->getLogo('default'));
    }

    public function testGetLogoWhenNoExists()
    {
        $this->ds->expects($this->at(0))->method('get')
            ->with('logo_enabled')->willReturn(true);

        $this->assertEquals(null, $this->helper->getLogo('default'));
    }

    public function testGetLogoWhenDisabledLogo()
    {
        $this->ds->expects($this->at(0))->method('get')
            ->with('logo_enabled')->willReturn(false);

        $this->assertEquals(null, $this->helper->getLogo('default'));
    }
}
