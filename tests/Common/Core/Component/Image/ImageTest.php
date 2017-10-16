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
            ->setMethods(['resize', 'crop', 'thumbnail'])
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
                $this->equalTo(ImageInterface::THUMBNAIL_OUTBOUND))
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
                $this->equalTo(ImageInterface::THUMBNAIL_INSET))
            ->will($this->returnValue($box));
        $imagine = $this->getMocketImagine();
        $imagine->expects($this->once())
            ->method('thumbnail')
            ->with($box)
            ->will($this->returnValue($imagine));
        $this->assertSame($image->process('thumbnail', $imagine, $parameters), $imagine);

        $parameters = [50, 100];
        $box        = $this->getMocketBox($parameters[0], $parameters[1], ImageInterface::THUMBNAIL_INSET);
        $image      = $this->getMocketImage();
        $image->expects($this->once())
            ->method('getBox')
            ->with(
                $this->equalTo($parameters[0]),
                $this->equalTo($parameters[1]),
                $this->equalTo(ImageInterface::THUMBNAIL_INSET))
            ->will($this->returnValue($box));
        $imagine = $this->getMocketImagine();
        $imagine->expects($this->once())
            ->method('thumbnail')
            ->with($box)
            ->will($this->returnValue($imagine));
        $this->assertSame($image->process('zoomCrop', $imagine, $parameters), $imagine);

/*

        $parameters   = [50, 100];
        $imageProcess = $this->image->process('zoomCrop', $this->createTestImage(), $parameters);
        $resize       = $this->image->zoomCrop($this->createTestImage(), $parameters);
        $this->assertEquals($resize->getImagick()->compareImages($imageProcess->getImagick(), 1)[1], 0);
        */
    }

    /**
     * this method performs tests for the crop operation. To do this compare the
     * metadata of the photo before and after, to check the changes made
     *
     */
    public function testCrop()
    {
        /*
        // topX, topY, width, height
        $parameters = [10, 10, 50, 100];
        $picture    = $this->image->crop($this->createTestImage(), $parameters);
        $this->assertEquals($picture->getSize()->getWidth(), 50);
        $this->assertEquals($picture->getSize()->getHeight(), 100);
        */
    }

    /**
     * this method performs tests for the thumbnail operation. To do this compare the
     * metadata of the photo before and after, to check the changes made.
     *
     */
    public function testThumbnail()
    {
        /*/ width, height, type
        $parameters = [50, 100];
        $picture    = $this->image->thumbnail($this->createTestImage(), $parameters);
        $this->assertEquals($picture->getSize()->getWidth(), 50);
        $this->assertEquals($picture->getSize()->getHeight(), 42);

        // width, height, type
        $parameters = [50, 100, 'out'];
        $picture    = $this->image->thumbnail($this->createTestImage(), $parameters);
        $this->assertEquals($picture->getSize()->getWidth(), 50);
        $this->assertEquals($picture->getSize()->getHeight(), 42);
        */
    }

    /**
     * this method performs tests for the thumbnail operation. To do this compare the
     * metadata of the photo before and after, to check the changes made.
     *
     */
    public function testZoomCrop()
    {
        /*/ width, height
        $parameters = [50, 100];
        $picture    = $this->image->zoomCrop($this->createTestImage(), $parameters);
        $this->assertEquals($picture->getSize()->getWidth(), 50);
        $this->assertEquals($picture->getSize()->getHeight(), 100);
        */
    }

    /**
     * this method performs tests for the resize operation. To do this compare the
     * metadata of the photo before and after, to check the changes made.
     *
     */
    public function testResize()
    {
        /*/ width, height
        $parameters = [50, 100];
        $picture    = $this->image->resize($this->createTestImage(), $parameters);
        $this->assertEquals($picture->getSize()->getWidth(), 50);
        $this->assertEquals($picture->getSize()->getHeight(), 100);
        */
    }

    /**
     * This method performs tests for the resize operation. To do this check if you put a icorrect path if return
     * something. The second test is check if we create one image we can retrive them from file system.
     *
     */
    public function testGetImage()
    {
        /*
        $picture = $this->image->getImage('/tmp/thumbnail.png');
        $this->assertNull($picture);

        $picture = $this->createTestImage();
        $picture->save('/tmp/thumbnail.png');
        $picture = $this->image->getImage('/tmp/thumbnail.png');
        $this->assertNotNull($picture);
        unlink('/tmp/thumbnail.png');
        */
    }
}
