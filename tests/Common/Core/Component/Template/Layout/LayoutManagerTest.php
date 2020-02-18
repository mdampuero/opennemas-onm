<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Template\Layout;

use Common\Core\Component\Template\Layout\LayoutManager;

/**
 * Defines test cases for LayoutManager class.
 */
class LayoutManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->view = $this->getMockBuilder('Common\Core\Component\Template\TemplateFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->manager = new LayoutManager($this->view);
    }

    /**
     * Tests addLayout, addLayouts, getLayout and getLayout methods.
     */
    public function testAddAndGet()
    {
        $this->manager->addLayouts([ 'norf' => [ 'fubar' => 'norf' ] ]);

        $this->assertEquals(
            [ 'name' => 'Layout name', 'menu' => 'frontpage', 'fubar' => 'norf' ],
            $this->manager->getLayout('norf')
        );

        $this->assertFalse($this->manager->getLayout('wibble'));
        $this->assertCount(1, $this->manager->getLayouts());
    }

    /**
     * Tests render.
     */
    public function testRender()
    {
        $xmlA = new \SimpleXmlElement('<wubble></wubble>');
        $xmlB = new \SimpleXmlElement('<norf></norf>');

        $manager = $this->getMockBuilder('Common\Core\Component\Template\Layout\LayoutManager')
            ->setConstructorArgs([ $this->view ])
            ->setMethods([ 'renderElement' ])
            ->getMock();

        $property = new \ReflectionProperty($this->manager, 'layoutDoc');
        $property->setAccessible(true);
        $property->setValue($manager, [ 'glorp' => $xmlA, 'thud' => $xmlB ]);

        $manager->expects($this->at(0))->method('renderElement')
            ->with('glorp', $xmlA, false)->willReturn('norf');
        $manager->expects($this->at(1))->method('renderElement')
            ->with('thud', $xmlB, false)->willReturn('gorp');

        $this->assertEquals("norf\ngorp", $manager->render());
    }

    /**
     * Tests setPath.
     */
    public function testSetPath()
    {
        $property = new \ReflectionProperty($this->manager, 'path');
        $property->setAccessible(true);

        $this->manager->setPath('/mumble/fubar');

        $this->assertEquals('/mumble/fubar', $property->getValue($this->manager));
    }
}
