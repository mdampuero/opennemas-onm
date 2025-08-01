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
use Common\Model\Entity\Content;

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

        $params = [
            'contents'             => new Content(['pk_content' => 1]),
            'contentPositionByPos' => '23'
        ];

        $this->assertEquals("norf\ngorp", $manager->render($params));
    }

    /**
     * Tests renderContent.
     */
    public function testRenderContent()
    {
        $method = new \ReflectionMethod($this->manager, 'renderContent');
        $method->setAccessible(true);

        $content = new Content([
            'pk_content' => 1,
            'content_type_name' => 'album'
        ]);

        $this->assertEmpty($method->invokeArgs($this->manager, [ $content ]));
    }

    /**
     * Tests renderContentsForPlaceholder.
     */
    public function testRenderContentsForPlaceholder()
    {
        $manager = $this->getMockBuilder('Common\Core\Component\Template\Layout\LayoutManager')
            ->setConstructorArgs([ $this->view ])
            ->setMethods([ 'renderContent' ])
            ->getMock();

        $manager->expects($this->at(0))->method('renderContent')
            ->willReturn('norf');

        $method = new \ReflectionMethod($manager, 'renderContentsForPlaceholder');
        $method->setAccessible(true);

        $property = new \ReflectionProperty($manager, 'positions');
        $property->setAccessible(true);
        $positions [ 'Static' ] = [ (object) ['pk_fk_content' => 1 ] ];
        $property->setValue($manager, $positions);

        $content = new Content([
            'pk_content' => 1,
            'content_type_name' => 'album'
        ]);

        $property = new \ReflectionProperty($manager, 'contents');
        $property->setAccessible(true);
        $contents [ '1' ] = [ (object) $content ];
        $property->setValue($manager, $contents);

        $this->assertEquals('norf', $method->invokeArgs($manager, [ 'Static' ]));
    }

    /**
     * Tests renderElement.
     */
    public function testRenderElement()
    {
        $xmlA = new \SimpleXmlElement('<wubble></wubble>');

        $method = new \ReflectionMethod($this->manager, 'renderElement');
        $method->setAccessible(true);

        $this->assertEquals(
            '<div class="static clearfix  span-"></div><!-- end static -->',
            trim(preg_replace('/\R+/', '', $method->invokeArgs($this->manager, [ 'Static', $xmlA, false ])))
        );
    }

    /**
     * Tests renderPlaceholder.
     */
    public function testRenderPlaceholder()
    {
        $xmlA = new \SimpleXmlElement('<wubble></wubble>');

        $xmlA ['description'] = 'Description';
        $xmlA ['name']        = 'Static';
        $xmlA ['width']       = 480;

        $method = new \ReflectionMethod($this->manager, 'renderPlaceholder');
        $method->setAccessible(true);

        $this->assertEquals(
            '<div class="placeholder clearfix  span-480" data-placeholder="Static">' .
            '<div class="title">Description</div><div class="content"><!-- ' .
            '{placeholder-content-Static} --></div></div><!-- end wrapper -->',
            $method->invokeArgs($this->manager, [ $xmlA, false ])
        );
    }

    /**
     * Tests renderStatic.
     */
    public function testRenderStatic()
    {
        $xmlA = new \SimpleXmlElement('<wubble></wubble>');

        $xmlA ['description'] = 'Description';

        $method = new \ReflectionMethod($this->manager, 'renderStatic');
        $method->setAccessible(true);

        $this->assertEquals(
            '<div class="static clearfix  span-"><div class="title">' .
            'Description</div></div><!-- end static -->',
            $method->invokeArgs($this->manager, [ $xmlA, false ])
        );
    }

    /**
     * Tests renderWrapper.
     */
    public function testRenderWrapper()
    {
        $xmlA = new \SimpleXmlElement('<foo><nitf></nitf></foo>');

        $method = new \ReflectionMethod($this->manager, 'renderWrapper');
        $method->setAccessible(true);

        $this->assertEquals(
            '<div class="wrapper clearfix span-"></div><!-- end wrapper -->',
            trim(preg_replace('/\R+/', '', $method->invokeArgs($this->manager, [ $xmlA, false ])))
        );
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
