<?php

namespace Tests\Common\Core\Components\Functions;

use Common\Model\Entity\Instance;
use Common\Model\Entity\Content;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Defines test cases for photo functions.
 */
class PhotoFunctionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('\Common\Core\Component\Helper\PhotoHelper')
            ->disableOriginalConstructor()
            ->setMethods([
                'getPhotoPath',
                'getPhotoSize',
                'getPhotoWidth',
                'getPhotoHeight',
                'getPhotoMimeType',
                'hasPhotoPath',
                'hasPhotoSize'
            ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->item = new Content([
            'content_status' => 1,
            'in_litter'      => 0,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->container->expects($this->any())->method('get')
            ->with('core.helper.photo')
            ->willReturn($this->helper);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Tests get_photo_path.
     */
    public function testGetPhotoPath()
    {
        $this->helper->expects($this->once())->method('getPhotoPath')
            ->with($this->item);

        get_photo_path($this->item);
    }

    /**
     * Tests get_photo_size.
     */
    public function testGetPhotoSize()
    {
        $this->helper->expects($this->once())->method('getPhotoSize')
            ->with($this->item);

        get_photo_size($this->item);
    }

    /**
     * Tests get_photo_width.
     */
    public function testGetPhotoWidth()
    {
        $this->helper->expects($this->once())->method('getPhotoWidth')
            ->with($this->item);

        get_photo_width($this->item);
    }

    /**
     * Tests get_photo_height.
     */
    public function testGetPhotoHeight()
    {
        $this->helper->expects($this->once())->method('getPhotoHeight')
            ->with($this->item);

        get_photo_height($this->item);
    }

    /**
     * Tests get_photo_mime_type.
     */
    public function testGetPhotoMimeType()
    {
        $this->helper->expects($this->once())->method('getPhotoMimeType')
            ->with($this->item);

        get_photo_mime_type($this->item);
    }

    /**
     * Tests has_photo_path.
     */
    public function testHasPhotoPath()
    {
        $this->helper->expects($this->once())->method('hasPhotoPath')
            ->with($this->item);

        has_photo_path($this->item);
    }

    /**
     * Tests has_photo_size.
     */
    public function testHasPhotoSize()
    {
        $this->helper->expects($this->once())->method('hasPhotoSize')
            ->with($this->item);

        has_photo_size($this->item);
    }
}
