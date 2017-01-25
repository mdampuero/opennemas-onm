<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Image;

use Common\Core\Component\Image\Editor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for Editor class.
 */
class EditorTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->image = $this->getMockBuilder('Image')
            ->setMethods([ 'getSize', 'resize', 'save' ])
            ->getMock();

        $this->imagine = $this->getMockBuilder('Imagine')
            ->setMethods([ 'open' ])
            ->getMock();

        $this->editor = new Editor();

        $property = new \ReflectionProperty($this->editor, 'imagine');
        $property->setAccessible(true);

        $property->setValue($this->editor, $this->imagine);
    }

    /**
     * Tests open.
     */
    public function testOpen()
    {
        $property = new \ReflectionProperty($this->editor, 'img');
        $property->setAccessible(true);

        $property->setValue($this->editor, $this->image);

        $this->imagine->expects($this->once())->method('open')->with('mumble/bar');

        $this->editor->open('mumble/bar');
    }

    /**
     * Tests resize.
     */
    public function testResize()
    {
        $size = $this->getMockBuilder('Size')
            ->setMethods([ 'getWidth', 'getHeight' ])
            ->getMock();

        $size->expects($this->exactly(2))->method('getWidth')->willReturn('1600');
        $size->expects($this->exactly(2))->method('getHeight')->willReturn('1200');
        $this->image->expects($this->exactly(2))->method('getSize')->willReturn($size);
        $this->image->expects($this->once())->method('resize');

        $property = new \ReflectionProperty($this->editor, 'img');
        $property->setAccessible(true);

        $property->setValue($this->editor, $this->image);

        $this->editor->resize(1280, 1024);
        $this->editor->resize(1600, 1600);
    }

    /**
     * Tests save.
     */
    public function testSave()
    {
        $property = new \ReflectionProperty($this->editor, 'img');
        $property->setAccessible(true);

        $property->setValue($this->editor, $this->image);

        $this->image->expects($this->once())->method('save')
            ->with('glork/flob', [ 'grault' => 'garply' ]);

        $this->editor->save('glork/flob', [ 'grault' => 'garply' ]);
        $this->editor->save('wobble/wibble');
    }

    /**
     * Tests getDimensions.
     */
    public function testGetDimensions()
    {
        $method = new \ReflectionMethod($this->editor, 'getDimensions');
        $method->setAccessible(true);

        $this->assertEquals(
            [ 1280, 853 ],
            $method->invokeArgs($this->editor, [ 2400, 1600, 1280, 1280 ])
        );

        $this->assertEquals(
            [ 880, 1280 ],
            $method->invokeArgs($this->editor, [ 1100, 1600, 1280, 1280 ])
        );
    }
}
