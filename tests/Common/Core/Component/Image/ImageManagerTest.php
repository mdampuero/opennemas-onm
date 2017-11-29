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

use Common\Core\Component\Image\ImageManager;
use PHPUnit\Framework\TestCase;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;
use Tests\Common\Core\Component\Image\BoxMockTest;
use Tests\Common\Core\Component\Image\ImagineMockTest;

class ImageManagerTest extends TestCase
{

    /**
     *  Method for get mock some methods of the ImageManager for the process method.
     */
    private function getMocketImageForProcess()
    {
        return $this->getMockBuilder('Common\Core\Component\Image\ImageManager')
            ->setMethods([ 'crop', 'resize', 'thumbnail', 'zoomCrop' ])
            ->getMock();
    }

    /**
     * Method for get mock some methods of the ImageManager.
     */
    private function getMocketImage()
    {
        return $this->getMockBuilder('Common\Core\Component\Image\ImageManager')
            ->setMethods([ 'getBox', 'getPoint', 'getImage' ])
            ->getMock();
    }

    /**
     *  Method for get mock some methods of the Imagine.
     */
    private function getMocketImagine()
    {
        return $this->getMockBuilder('ImagineMockTest')
            ->setMethods(['resize', 'crop', 'thumbnail', 'zoomCrop', 'getSize', 'strip', 'get', 'getImagick'])
            ->getMock();
    }

    /**
     *  Method for get mock the element box.
     */
    private function getMocketBox()
    {
        return $this->getMockBuilder('BoxMockTest')
            ->setMethods(['getWidth', 'getHeight'])
            ->getMock();
    }

    /**
     *  Method for get mock some methods of the Point element.
     */
    private function getMocketPoint()
    {
        return $this->getMockBuilder('PointMockTest')
            ->getMock();
    }

    /**
     *  Method for get mock some methods of the Imagick.
     */
    private function getMocketImagick()
    {
        return $this->getMockBuilder('ImagickMockTest')
            ->setMethods(['getImageFormat'])
            ->getMock();
    }



    /**
     * Here we proceed to test all the methods that we have to transform images. The system is check the method called
     * directly with the response gived for this method.
     *
     */
    public function testProcess()
    {
        //Check if the process method call the method resize if the method name not exist.
        $parameters = [];
        $image      = $this->getMocketImageForProcess();
        $imagine    = $this->getMocketImagine();
        $image->expects($this->once())
            ->method('resize')
            ->with($imagine, $parameters)
            ->will($this->returnValue($imagine));
        $this->assertSame($image->process('fdsa', $imagine, $parameters), $imagine);

        //Check if the process method call the method resize.
        $parameters = [];
        $image      = $this->getMocketImageForProcess();
        $imagine    = $this->getMocketImagine();
        $image->expects($this->once())
            ->method('resize')
            ->with($imagine, $parameters)
            ->will($this->returnValue($imagine));
        $this->assertSame($image->process('resize', $imagine, $parameters), $imagine);
    }

    /**
     *  Check Crop method.
     */
    public function testCrop()
    {
        $parameters = [10, 10, 50, 100];
        $box        = $this->getMocketBox();
        $point      = $this->getMocketPoint();
        $image      = $this->getMocketImage();
        $image->expects($this->once())
            ->method('getBox')
            ->with($this->equalTo($parameters[2]), $this->equalTo($parameters[3]))
            ->will($this->returnValue($box));
        $image->expects($this->once())
            ->method('getPoint')
            ->with($this->equalTo($parameters[0]), $this->equalTo($parameters[1]))
            ->will($this->returnValue($point));
        $imagine = $this->getMocketImagine();
        $imagine->expects($this->once())
            ->method('crop')
            ->with($point, $box)
            ->will($this->returnValue($imagine));
        $this->assertSame($image->crop($imagine, $parameters), $imagine);
    }

    /**
     *  Check Thumbnail method.
     */
    public function testThumbnail()
    {
        $parameters = [50, 100, 'out'];
        $box        = $this->getMocketBox($parameters[0], $parameters[1], ImageInterface::THUMBNAIL_OUTBOUND);
        $image      = $this->getMocketImage();
        $image->expects($this->once())
            ->method('getBox')
            ->with(
                $this->equalTo($parameters[0]),
                $this->equalTo($parameters[1]),
                $this->equalTo(ImageInterface::THUMBNAIL_OUTBOUND)
            )
            ->will($this->returnValue($box));
        $imagine = $this->getMocketImagine();
        $imagine->expects($this->once())
            ->method('thumbnail')
            ->with($box)
            ->will($this->returnValue($imagine));
        $this->assertSame($image->thumbnail($imagine, $parameters), $imagine);

        $parameters = [50, 100, 'in'];
        $box        = $this->getMocketBox($parameters[0], $parameters[1], ImageInterface::THUMBNAIL_INSET);
        $image      = $this->getMocketImage();
        $image->expects($this->once())
            ->method('getBox')
            ->with(
                $this->equalTo($parameters[0]),
                $this->equalTo($parameters[1]),
                $this->equalTo(ImageInterface::THUMBNAIL_INSET)
            )
            ->will($this->returnValue($box));
        $imagine = $this->getMocketImagine();
        $imagine->expects($this->once())
            ->method('thumbnail')
            ->with($box)
            ->will($this->returnValue($imagine));
        $this->assertSame($image->thumbnail($imagine, $parameters), $imagine);
    }

    /**
     *  Check ZoomCrop method.
     */
    public function testZoomCrop()
    {
        //[size width, size height, Image width, Image height, x point, y point, width resize, height resize]
        $parameters = [50, 1000000, 10, 4000, 50, 400, 50, 20000, 0, 0];
        $this->zoomCropTest($parameters);
        $parameters = [1000000, 50, 400, 50, 50, 400, 400, 50, 0, 0];
        $this->zoomCropTest($parameters);
    }

    /**
     *  Check ZoomCrop method.
     */
    private function zoomCropTest($parameters)
    {
        $box = $this->getMocketBox($parameters[2], $parameters[3], ImageInterface::THUMBNAIL_OUTBOUND);
        $box->expects($this->once())
            ->method('getWidth')
            ->will($this->returnValue($parameters[2]));
        $box->expects($this->once())
            ->method('getHeight')
            ->will($this->returnValue($parameters[3]));
        $box2  = $this->getMocketBox($parameters[0], $parameters[1]);
        $point = $this->getMocketPoint($parameters[4], $parameters[5]);
        $image = $this->getMocketImage();
        $image->expects($this->exactly(2))
            ->method('getBox')
            ->withConsecutive(
                [
                    $this->equalTo($parameters[6]),
                    $this->equalTo($parameters[7]),
                    $this->equalTo(ImageInterface::THUMBNAIL_OUTBOUND)
                ],
                [
                    $this->equalTo($parameters[0]),
                    $this->equalTo($parameters[1])
                ]
            )
            ->willReturnOnConsecutiveCalls($this->returnValue($box), $this->returnValue($box2));

        $image->expects($this->once())
            ->method('getPoint')
            ->with($this->equalTo($parameters[8]), $this->equalTo($parameters[9]))
            ->will($this->returnValue($point));

        $imagine = $this->getMocketImagine();
        $imagine->expects($this->once())
            ->method('resize')
            ->with($box)
            ->will($this->returnValue($imagine));
        $imagine->expects($this->once())
            ->method('crop')
            ->with($point, $box2)
            ->will($this->returnValue($imagine));
        $imagine->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue($box));
        $this->assertSame($image->zoomCrop($imagine, $parameters), $imagine);
    }

    /**
     *  Check Resize method.
     */
    public function testResize()
    {
        $parameters = [50, 100];
        $box        = $this->getMocketBox($parameters[0], $parameters[1]);
        $image      = $this->getMocketImage();
        $image->expects($this->once())
            ->method('getBox')
            ->with($this->equalTo(50), $this->equalTo(100))
            ->will($this->returnValue($box));
        $imagine = $this->getMocketImagine();
        $imagine->expects($this->once())
            ->method('resize')
            ->with($box)
            ->will($this->returnValue($imagine));
        $this->assertSame($image->resize($imagine, $parameters), $imagine);
    }

    /**
     * Check Strip method.
     *
     */
    public function testStrip()
    {
        $imagine = $this->getMocketImagine();
        $imagine->expects($this->once())
            ->method('strip');
        $image = $this->getMocketImage();
        $image->strip($imagine);
    }

    /**
     * Check get method.
     *
     */
    public function testGet()
    {
        $parameters = [];
        $imagick    = $this->getMocketImagick();
        $imagick->expects($this->once())
            ->method('getImageFormat')
            ->will($this->returnValue('hh'));
        $imagine = $this->getMocketImagine();
        $imagine->expects($this->once())
            ->method('getImagick')
            ->will($this->returnValue($imagick));
        $imagine->expects($this->once())
            ->method('get')
            ->with('hh', $parameters)
            ->will($this->returnValue(true));
        $image = $this->getMocketImage();
        $image->get($imagine, $parameters);
    }

    /**
     * Check getImageFormat method.
     *
     */
    public function testGetImageFormat()
    {
        $parameters = [];
        $imagick    = $this->getMocketImagick();
        $imagick->expects($this->once())
            ->method('getImageFormat')
            ->will($this->returnValue('HH'));
        $imagine = $this->getMocketImagine();
        $imagine->expects($this->once())
            ->method('getImagick')
            ->will($this->returnValue($imagick));
        $image = $this->getMocketImage();
        $this->assertSame($image->getImageFormat($imagine), 'hh');
    }

    /**
     *  Check getImage method.
     */
    public function testGetImage()
    {
        $image   = $this->getMocketImage();
        $imagick = $this->getMocketImagick();
        $image->expects($this->once())
            ->method('getImage')
            ->with($this->equalTo('prueba'))
            ->will($this->returnValue($imagick));

        $this->assertSame($image->getImage('prueba'), $imagick);
    }
}
