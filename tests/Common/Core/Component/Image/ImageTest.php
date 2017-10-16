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

use Common\Core\Component\Image\Image;
use PHPUnit\Framework\TestCase;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;
use Tests\Common\Core\Component\Image\BoxMockTest;
use Tests\Common\Core\Component\Image\ImagineMockTest;

class ImageTest extends TestCase
{

    private function getMocketImage()
    {
        return $this->getMockBuilder('Common\Core\Component\Image\Image')
            ->setMethods([ 'getBox', 'getPoint' ])
            ->getMock();
    }

    /**
     *  Generete a new imagine
     */
    private function getMocketImagine()
    {
        return $this->getMockBuilder('Tests\Common\Core\Component\Image\ImagineMockTest')
            ->setMethods(['resize', 'crop', 'thumbnail', 'zoomCrop', 'getSize'])
            ->getMock();
    }

    private function getMocketBox($width, $height)
    {
        return new BoxMockTest($width, $height);
    }

    private function getMocketPoint($topX, $topY)
    {
        return new PointMockTest($topX, $topY);
    }

    /**
     * this method performs tests for the operation proccess. For that, check all method of image transformation
     * that you can call with the process method. The result of that is compare with the result of call the same method
     *
     */
    public function testProcess()
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
        $this->assertSame($image->process('fdasfdsa', $imagine, $parameters), $imagine);

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
        $this->assertSame($image->process('resize', $imagine, $parameters), $imagine);

        $parameters = [10, 10, 50, 100];
        $box        = $this->getMocketBox($parameters[2], $parameters[3]);
        $point      = $this->getMocketPoint($parameters[0], $parameters[1]);
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
        $this->assertSame($image->process('crop', $imagine, $parameters), $imagine);

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
        $this->assertSame($image->process('thumbnail', $imagine, $parameters), $imagine);

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
        $this->assertSame($image->process('thumbnail', $imagine, $parameters), $imagine);

        $parameters = [50, 100];
        $box        = $this->getMocketBox($parameters[0], $parameters[1], ImageInterface::THUMBNAIL_OUTBOUND);
        $box2       = $this->getMocketBox($parameters[0], $parameters[1]);
        $point      = $this->getMocketPoint($parameters[0], $parameters[1]);
        $image      = $this->getMocketImage();
        $image->expects($this->exactly(2))
            ->method('getBox')
            ->withConsecutive(
                [
                    $this->equalTo($parameters[0]),
                    $this->equalTo($parameters[1]),
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
            ->with($this->equalTo(0), $this->equalTo(0))
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
        $this->assertSame($image->process('zoomCrop', $imagine, $parameters), $imagine);
    }
}
