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

use Common\Core\Component\Helper\ImageHelper;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for ImageHelper class.
 */
class ImageHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'bar' ]);

        $this->il = $this->getMockBuilder('Common\Core\Component\Loader\InstanceLoader')
            ->disableOriginalConstructor()
            ->setMethods([ 'getInstance' ])
            ->getMock();

        $this->fs = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->disableOriginalConstructor()
            ->setMethods([ 'copy', 'exists' ])
            ->getMock();

        $this->processor = $this->getMockBuilder('Common\Core\Component\Image\Processor')
            ->setMethods([
                'apply', 'close', 'getDescription', 'getHeight', 'getImageRotation', 'getSize',
                'getWidth', 'open', 'optimize', 'save', 'strip', 'setImageRotation'
            ])->getMock();

        $this->il->expects($this->any())->method('getInstance')
            ->willReturn($this->instance);

        $this->helper = $this->getMockBuilder('Common\Core\Component\Helper\ImageHelper')
            ->setConstructorArgs([ $this->il, '/waldo/grault', $this->processor ])
            ->setMethods([ 'getExtension' ])
            ->getMock();

        $property = new \ReflectionProperty($this->helper, 'fs');
        $property->setAccessible(true);

        $property->setValue($this->helper, $this->fs);
    }

    /**
     * Tests generatePath.
     */
    public function testGeneratePath()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper->expects($this->any())->method('getExtension')
            ->with($file)->willReturn('jpg');

        $this->assertRegexp(
            '/\/waldo\/grault\/media\/bar\/images\/[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/[0-9]{19}.jpg/',
            $this->helper->generatePath($file, new \DateTime())
        );

        $this->assertRegexp(
            '/\/waldo\/grault\/media\/bar\/images\/2010\/01\/01\/20100101152045[0-9]{5}.jpg/',
            $this->helper->generatePath($file, new \DateTime('2010-01-01 15:20:45'))
        );
    }

    /**
     * Tests applyRotation.
     */
    public function testApplyRotationWhenRotation()
    {
        $this->processor->expects($this->once())->method('open')
            ->with('/plugh/frog.jpg')->willReturn($this->processor);
        $this->processor->expects($this->once())->method('getImageRotation')
            ->willReturn('8');
        $this->processor->expects($this->once())->method('setImageRotation')
            ->willReturn($this->processor);
        $this->processor->expects($this->once())->method('strip')
            ->willReturn($this->processor);
        $this->processor->expects($this->once())->method('save')
            ->with('/plugh/frog.jpg')->willReturn($this->processor);

        $this->helper->applyRotation('/plugh/frog.jpg');
    }

    /**
     * Tests applyRotation.
     */
    public function testApplyRotationWhenNoRotation()
    {
        $this->processor->expects($this->once())->method('open')
            ->with('/plugh/frog.jpg')->willReturn($this->processor);
        $this->processor->expects($this->once())->method('getImageRotation')
            ->willReturn(null);

        $this->helper->applyRotation('/plugh/frog.jpg');
    }

    /**
     * Tests exists.
     */
    public function testExists()
    {
        $this->fs->expects($this->at(0))->method('exists')
            ->with('/glork/quux.foo')->willReturn(true);
        $this->fs->expects($this->at(1))->method('exists')
            ->with('/foo/wobble.bar')->willReturn(false);

        $this->assertTrue($this->helper->exists('/glork/quux.foo'));
        $this->assertFalse($this->helper->exists('/foo/wobble.bar'));
    }

    /**
     * Tests getInformation.
     */
    public function testGetInformation()
    {
        $this->processor->expects($this->once())->method('getHeight')
            ->willReturn(220);
        $this->processor->expects($this->once())->method('getSize')
            ->willReturn(23920);
        $this->processor->expects($this->once())->method('getWidth')
            ->willReturn(400);
        $this->processor->expects($this->once())->method('getDescription')
            ->willReturn('gorp');
        $this->processor->expects($this->once())->method('close');

        $this->assertEquals([
            'size'        => 23920 / 1024,
            'width'       => 400,
            'height'      => 220,
            'description' => 'gorp'
        ], $this->helper->getInformation('corge/quux/grault.jpg'));
    }

    /**
     * Tests isOptimizable with optimizable and non-optimizable values.
     */
    public function testIsOptimizable()
    {
        $this->assertFalse($this->helper->isOptimizable('corge/quux/grault.swf'));
        $this->assertTrue($this->helper->isOptimizable('corge/quux/grault.jpg'));
    }

    /**
     * Tests move when the original file is copied to target.
     */
    public function testMoveWhenCopy()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRealPath' ])
            ->getMock();

        $file->expects($this->once())->method('getRealPath')
            ->willReturn('/glork/quux.jpg');

        $this->fs->expects($this->once())->method('copy')
            ->with('/glork/quux.jpg', '/thud/fred/norf.jpg');

        $this->helper->move($file, '/thud/fred/norf.jpg', true);
    }

    /**
     * Tests move when the original file is moved to target.
     */
    public function testMoveWhenNoCopy()
    {
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\File')
            ->disableOriginalConstructor()
            ->setMethods([ 'move' ])
            ->getMock();

        $file->expects($this->once())->method('move')
            ->with('/thud/fred/', 'norf.jpg');

        $this->helper->move($file, '/thud/fred/norf.jpg');
    }

    /**
     * Tests optimize.
     */
    public function testOptimize()
    {
        $this->processor->expects($this->once())->method('open')
            ->with('/plugh/frog.jpg')->willReturn($this->processor);
        $this->processor->expects($this->once())->method('optimize')
            ->willReturn($this->processor);
        $this->processor->expects($this->once())->method('save')
            ->with('/plugh/frog.jpg')->willReturn($this->processor);

        $this->helper->optimize('/plugh/frog.jpg');
    }

    /**
     * Tests optimizeImage.
     */
    public function testOptimizeImage()
    {
        $this->processor->expects($this->once())->method('open')
            ->willReturn($this->processor);

        $this->processor->expects($this->once())->method('apply')
            ->with('thumbnail', $this->isType('array'))
            ->willReturn($this->processor);

        $this->processor->expects($this->once())->method('optimize')
            ->with($this->isType('array'))
            ->willReturn($this->processor);

        $this->processor->expects($this->once())->method('save')
            ->willReturn($this->processor);

        $this->processor->expects($this->once())->method('close')
            ->willReturn($this->processor);

        $this->helper->optimizeImage(
            '/plugh/frog.jpg',
            ['image_quality' => 80, 'image_resolution' => '1024x1024']
        );
    }
}
