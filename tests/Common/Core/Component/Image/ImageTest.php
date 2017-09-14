<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Core\Component\Image;

use Common\Core\Component\Image\Image;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;

class ImageTest extends KernelTestCase
{

    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->imagine = new Imagine();
        $this->image = new Image();
    }

    /**
     *  Generete a new image
     */
    private function createTestImage()
    {
        $size  = new Box(1200, 1000);
        return $this->imagine->create($size);
    }


    /**
     * this method performs tests for the operation proccess. For that, check all method of image transformation
     * that you can call with the process method. The result of that is compare with the result of call the same method
     *
     */
    public function testProcess()
    {
        $parameters = [50, 100];
        $imageProcess = $this->image->process('fdasfdsa', $this->createTestImage(), $parameters);
        $resize = $this->image->resize($this->createTestImage(), $parameters);
        $this->assertEquals($resize->getImagick()->compareImages($imageProcess->getImagick(), 1)[1], 0);

        $parameters = [50, 100];
        $imageProcess = $this->image->process('resize', $this->createTestImage(), $parameters);
        $resize = $this->image->resize($this->createTestImage(), $parameters);
        $this->assertEquals($resize->getImagick()->compareImages($imageProcess->getImagick(), 1)[1], 0);

        $parameters = [10, 10, 50, 100];
        $imageProcess = $this->image->process('crop', $this->createTestImage(), $parameters);
        $resize = $this->image->crop($this->createTestImage(), $parameters);
        $this->assertEquals($resize->getImagick()->compareImages($imageProcess->getImagick(), 1)[1], 0);

        $parameters = [50, 100, 'out'];
        $imageProcess = $this->image->process('thumbnail', $this->createTestImage(), $parameters);
        $resize = $this->image->thumbnail($this->createTestImage(), $parameters);
        $this->assertEquals($resize->getImagick()->compareImages($imageProcess->getImagick(), 1)[1], 0);

        $parameters = [50, 100];
        $imageProcess = $this->image->process('zoomCrop', $this->createTestImage(), $parameters);
        $resize = $this->image->zoomCrop($this->createTestImage(), $parameters);
        $this->assertEquals($resize->getImagick()->compareImages($imageProcess->getImagick(), 1)[1], 0);
    }

    /**
     * this method performs tests for the crop operation. To do this compare the
     * metadata of the photo before and after, to check the changes made
     *
     */
    public function testCrop()
    {
        // topX, topY, width, height
        $parameters = [10, 10, 50, 100];
        $picture = $this->image->crop($this->createTestImage(), $parameters);
        $this->assertEquals($picture->getSize()->getWidth(), 50);
        $this->assertEquals($picture->getSize()->getHeight(), 100);
    }

    /**
     * this method performs tests for the thumbnail operation. To do this compare the
     * metadata of the photo before and after, to check the changes made.
     *
     */
    public function testThumbnail()
    {
        // width, height, type
        $parameters = [50, 100];
        $picture = $this->image->thumbnail($this->createTestImage(), $parameters);
        $this->assertEquals($picture->getSize()->getWidth(), 50);
        $this->assertEquals($picture->getSize()->getHeight(), 42);

        // width, height, type
        $parameters = [50, 100, 'out'];
        $picture = $this->image->thumbnail($this->createTestImage(), $parameters);
        $this->assertEquals($picture->getSize()->getWidth(), 50);
        $this->assertEquals($picture->getSize()->getHeight(), 42);
    }

    /**
     * this method performs tests for the thumbnail operation. To do this compare the
     * metadata of the photo before and after, to check the changes made.
     *
     */
    public function testZoomCrop()
    {
        // width, height
        $parameters = [50, 100];
        $picture = $this->image->zoomCrop($this->createTestImage(), $parameters);
        $this->assertEquals($picture->getSize()->getWidth(), 50);
        $this->assertEquals($picture->getSize()->getHeight(), 100);
    }

    /**
     * this method performs tests for the resize operation. To do this compare the
     * metadata of the photo before and after, to check the changes made.
     *
     */
    public function testResize()
    {
        // width, height
        $parameters = [50, 100];
        $picture = $this->image->resize($this->createTestImage(), $parameters);
        $this->assertEquals($picture->getSize()->getWidth(), 50);
        $this->assertEquals($picture->getSize()->getHeight(), 100);
    }

    /**
     * This method performs tests for the resize operation. To do this check if you put a icorrect path if return
     * something. The second test is check if we create one image we can retrive them from file system.
     *
     */
    public function testGetImage()
    {
        $picture = $this->image->getImage('/tmp/thumbnail.png');
        $this->assertNull($picture);

        $picture = $this->createTestImage();
        $picture->save('/tmp/thumbnail.png');
        $picture = $this->image->getImage('/tmp/thumbnail.png');
        $this->assertNotNull($picture);
        unlink('/tmp/thumbnail.png');
    }
}
