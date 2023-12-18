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

use Common\Core\Component\Image\Processor;
use Imagine\Image\Box;
use Imagine\Image\Point;

/**
 * Defines test cases for Processor class.
 */
class ProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->fs = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->setMethods([ 'exists' ])
            ->getMock();

        $this->image = $this->getMockBuilder('Image')
            ->setMethods([
                'crop', 'get', 'getHeight','getImagick', 'getSize', 'getWidth',
                'metadata', 'resize', 'save', 'strip', 'thumbnail', 'rotate'
            ])->getMock();

        $this->imagick = $this->getMockBuilder('Imagick')
            ->setMethods([
                'clear', 'getImageFilename', 'getImageFormat', 'getImageLength',
                'getImageMimeType', 'getImageProperties'
            ])->getMock();

        $this->imagine = $this->getMockBuilder('Imagine')
            ->setMethods([ 'open' ])
            ->getMock();

        $this->image->expects($this->any())->method('getImagick')
            ->willReturn($this->imagick);

        $this->im = $this->getMockBuilder('Common\Core\Component\Image\Processor')
            ->setConstructorArgs([])
            ->setMethods([ 'getImagine' ])
            ->getMock();

        $this->im->expects($this->any())->method('getImagine')
            ->willReturn($this->imagine);

        $property = new \ReflectionProperty($this->im, 'fs');
        $property->setAccessible(true);
        $property->setValue($this->im, $this->fs);

        $property = new \ReflectionProperty($this->im, 'image');
        $property->setAccessible(true);
        $property->setValue($this->im, $this->image);

        $property = new \ReflectionProperty($this->im, 'imagine');
        $property->setAccessible(true);
        $property->setValue($this->im, $this->imagine);
    }

    /**
     * Tests apply when provided method is implemented in Processor
     */
    public function testApplyWhenMethodExists()
    {
        $this->im = $this->getMockBuilder('Common\Core\Component\Image\Processor')
            ->setConstructorArgs([])
            ->setMethods([ 'glorp', 'getFormat' ])
            ->getMock();

        $this->im->expects($this->once())->method('getFormat')
            ->willReturn('foo');

        $this->im->expects($this->once())->method('glorp')
            ->with([ 'flob', 22474 ]);

        $this->im->apply('glorp', [ 'flob', 22474 ]);
    }

    /**
     * Tests process when provided image is a gif
     */
    public function testApplyWhenImageIsGif()
    {
        $this->im = $this->getMockBuilder('Common\Core\Component\Image\Processor')
            ->setConstructorArgs([])
            ->setMethods([ 'getFormat' ])
            ->getMock();

        $this->im->expects($this->once())->method('getFormat')
            ->willReturn('gif');

        $this->im->apply('glorp', [ 'flob', 22474 ]);
    }

    /**
     * Tests process when provided method is implemented in Processor
     *
     * @expectedException \InvalidArgumentException
     */
    public function testApplyWhenMethodNotExists()
    {
        $this->im->apply('glorp', [ 'flob', 22474 ]);
    }

    /**
     * Tests close.
     */
    public function testClose()
    {
        $this->imagick->expects($this->once())->method('clear');

        $this->im->close();
    }

    /**
     * Tests getContent.
     */
    public function testGetContent()
    {
        $params = [ 'quality' => 75 ];

        $this->imagick->expects($this->once())->method('getImageFormat')
            ->willReturn('glorp');
        $this->image->expects($this->once())->method('get')
            ->with('glorp', $params)->willReturn('quux mumble');

        $this->assertEquals('quux mumble', $this->im->getContent($params));
    }

    /**
     * Tests getDescription.
     */
    public function testGetDescriptionWithExif()
    {
        $this->image->expects($this->once())->method('metadata')
            ->willReturn([ 'ifd0.ImageDescription' => 'glorp']);

        $this->assertEquals('glorp', $this->im->getDescription());
    }

    /**
     * Tests getDescription.
     */
    public function testGetDescriptionWithoutExif()
    {
        $this->image->expects($this->once())->method('metadata')
            ->willReturn(null);

        $this->assertEquals(null, $this->im->getDescription());
    }

    /**
     * Tests getImageRotation.
     */
    public function testGetImageRotationWithRotation()
    {
        $this->image->expects($this->once())->method('metadata')
            ->willReturn([ 'ifd0.Orientation' => '8']);

        $this->assertEquals('8', $this->im->getImageRotation());
    }

    /**
     * Tests getImageRotation
     */
    public function testGetImageRotationWithoutRotation()
    {
        $this->image->expects($this->once())->method('metadata')
            ->willReturn(null);

        $this->assertEquals(null, $this->im->getImageRotation());
    }

    /**
     * Tests setImageRotation.
     */
    public function testSetImageRotationWithExifRotateValue8()
    {
        $this->image->expects($this->once())->method('metadata')
            ->willReturn([ 'ifd0.Orientation' => '8']);

        $this->im->setImageRotation();
    }

    /**
     * Tests setImageRotation.
     */
    public function testSetImageRotationWithExifRotateValue3()
    {
        $this->image->expects($this->once())->method('metadata')
            ->willReturn([ 'ifd0.Orientation' => '3']);

        $this->im->setImageRotation();
    }

    /**
     * Tests setImageRotation.
     */
    public function testSetImageRotationWithExifRotateValue6()
    {
        $this->image->expects($this->once())->method('metadata')
            ->willReturn([ 'ifd0.Orientation' => '6']);

        $this->im->setImageRotation();
    }

    /**
     * Tests getDescription.
     */
    public function testSetImageRotationWithoutExif()
    {
        $this->image->expects($this->once())->method('metadata')
            ->willReturn([]);

        $this->im->setImageRotation();
    }

    /**
     * Tests getFormat.
     */
    public function testGetFormat()
    {
        $this->imagick->expects($this->once())->method('getImageFormat')
            ->willReturn('CoRgE');

        $this->assertEquals('corge', $this->im->getFormat());
    }

    /**
     * Tests getMimeType.
     */
    public function testGetMimeType()
    {
        $this->imagick->expects($this->once())->method('getImageMimeType')
            ->willReturn('image/wibble');

        $this->assertEquals('image/wibble', $this->im->getMimeType());
    }

    /**
     * Tests getHeight.
     */
    public function testGetHeight()
    {
        $size = $this->getMockBuilder('Size')
            ->setMethods([ 'getHeight' ])
            ->getMock();

        $this->image->expects($this->any())->method('getSize')
            ->willReturn($size);

        $size->expects($this->any())->method('getHeight')
            ->willReturn(9688);

        $this->assertEquals(9688, $this->im->getHeight());
    }

    /**
     * Tests getSize.
     */
    public function testGetSize()
    {
        $this->imagick->expects($this->once())->method('getImageLength')
            ->willReturn(22626);

        $this->assertEquals(22626, $this->im->getSize());
    }

    /**
     * Tests getWidth.
     */
    public function testGetWidth()
    {
        $size = $this->getMockBuilder('Size')
            ->setMethods([ 'getWidth' ])
            ->getMock();

        $this->image->expects($this->any())->method('getSize')
            ->willReturn($size);

        $size->expects($this->any())->method('getWidth')
            ->willReturn(180);

        $this->assertEquals(180, $this->im->getWidth());
    }


    /**
     * Tests open when file exists.
     */
    public function testOpenWhenFileExists()
    {
        $this->fs->expects($this->once())->method('exists')
            ->with('xyzzy/grault.flob')->willReturn(true);
        $this->imagine->expects($this->once())->method('open')
            ->with('xyzzy/grault.flob');

         $this->im->open('xyzzy/grault.flob');
    }

    /**
     * Tests open when file does not exist.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testOpenWhenFileNotExists()
    {
        $this->fs->expects($this->once())->method('exists')
            ->with('xyzzy/grault.flob')->willReturn(false);

         $this->im->open('xyzzy/grault.flob');
    }

    /**
     * Tests open when exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testOpenWhenException()
    {
        $this->fs->expects($this->once())->method('exists')
            ->with('xyzzy/grault.flob')->willReturn(true);
        $this->imagine->expects($this->once())->method('open')
            ->will($this->throwException(new \Exception()));

        $this->im->open('xyzzy/grault.flob');
    }

    /**
     * Tests optimize when called with and without arguments.
     */
    public function testOptimize()
    {
        $optimization = new \ReflectionProperty($this->im, 'optimization');
        $defaults     = new \ReflectionProperty($this->im, 'defaults');

        $optimization->setAccessible(true);
        $defaults->setAccessible(true);

        $this->im->optimize();
        $this->assertEquals(
            $defaults->getValue($this->im),
            $optimization->getValue($this->im)
        );

        $this->im->optimize([ 'frog' => 12230 ]);
        $this->assertEquals([ 'frog' => 12230 ], $optimization->getValue($this->im));
    }

    /**
     * Tests save.
     */
    public function testSave()
    {
        $this->image->expects($this->once())->method('save')
            ->with('norf/thud.jpg', []);

        $this->im->save('norf/thud.jpg');
    }

    /**
     * Tests strip.
     */
    public function testStrip()
    {
        $this->image->expects($this->once())->method('strip');

        $this->im->strip();
    }

    /**
     *  Tests crop.
     */
    public function testCrop()
    {
        $method = new \ReflectionMethod($this->im, 'crop');
        $method->setAccessible(true);

        $this->image->expects($this->once())->method('crop');

        $method->invokeArgs($this->im, [ [ 4298, 8456, 9353, 18217 ] ]);
    }

    /**
     *  Tests resize.
     */
    public function testResize()
    {
        $method = new \ReflectionMethod($this->im, 'resize');
        $method->setAccessible(true);

        $this->image->expects($this->once())->method('resize')
            ->with(new Box(4298, 8456));

        $method->invokeArgs($this->im, [ [ 4298, 8456 ] ]);
    }

    /**
     *  Tests thumbnail.
     */
    public function testThumbnail()
    {
        $method = new \ReflectionMethod($this->im, 'thumbnail');
        $method->setAccessible(true);

        $this->image->expects($this->once())->method('thumbnail')
            ->with(new Box(4298, 8456));

        $method->invokeArgs($this->im, [ [ 4298, 8456 ] ]);
    }

    /**
     *  Tests zoomcrop when the height after calculations is lesser than or
     *  equals to image height.
     */
    public function testZoomCropWhenValidHeight()
    {
        $method = new \ReflectionMethod($this->im, 'zoomCrop');
        $method->setAccessible(true);

        $size = $this->getMockBuilder('Size')
            ->setMethods([ 'getHeight', 'getWidth' ])
            ->getMock();

        $this->image->expects($this->any())->method('getSize')
            ->willReturn($size);

        $size->expects($this->any())->method('getWidth')
            ->willReturn(500);

        $size->expects($this->any())->method('getHeight')
            ->willReturn(1000);

        $this->image->expects($this->once())->method('crop')
            ->with(new Point(0, 250), new Box(500, 500));
        $this->image->expects($this->once())->method('resize')
            ->with(new Box(200, 200));

        $method->invokeArgs($this->im, [ [ 200, 200 ] ]);
    }

    /**
     *  Tests zoomcrop when the height after calculations is greater than image
     *  height.
     */
    public function testZoomCropWhenInvalidHeight()
    {
        $method = new \ReflectionMethod($this->im, 'zoomCrop');
        $method->setAccessible(true);

        $size = $this->getMockBuilder('Size')
            ->setMethods([ 'getHeight', 'getWidth' ])
            ->getMock();

        $this->image->expects($this->any())->method('getSize')
            ->willReturn($size);

        $size->expects($this->any())->method('getWidth')
            ->willReturn(1000);

        $size->expects($this->any())->method('getHeight')
            ->willReturn(500);

        $this->image->expects($this->once())->method('crop')
            ->with(new Point(250, 0), new Box(500, 500));
        $this->image->expects($this->once())->method('resize')
            ->with(new Box(200, 200));

        $method->invokeArgs($this->im, [ [ 200, 200 ] ]);
    }
}
