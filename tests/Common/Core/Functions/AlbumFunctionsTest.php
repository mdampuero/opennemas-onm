<?php

namespace Tests\Common\Core\Functions;

use Common\Model\Entity\Content;

/**
 * Defines test cases for album functions.
 */
class AlbumFunctionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('\Common\Core\Component\Helper\AlbumHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getAlbumPhotos', 'hasAlbumPhotos' ])
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
            ->with('core.helper.album')
            ->willReturn($this->helper);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Tests the get_album_photos function.
     */
    public function testGetAlbumPhotos()
    {
        $this->helper->expects($this->once())->method('getAlbumPhotos')
            ->with($this->item)
            ->willReturn([]);

        $this->assertEquals([], get_album_photos($this->item));
    }

    /**
     * Tests the has_album_photos function.
     */
    public function testHasAlbumPhotos()
    {
        $this->helper->expects($this->once())->method('hasAlbumPhotos')
            ->with($this->item)
            ->willReturn(false);

        $this->assertEquals(false, has_album_photos($this->item));
    }
}
