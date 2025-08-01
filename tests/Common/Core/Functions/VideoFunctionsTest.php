<?php

namespace Tests\Common\Core\Functions;

use Common\Model\Entity\Content;

/**
 * Defines test cases for content functions.
 */
class VideoFunctionsTests extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('\Common\Core\Component\Helper\VideoHelper')
            ->disableOriginalConstructor()
            ->setMethods([
                'getVideoEmbedHtml',
                'getVideoEmbedUrl',
                'getVideoHtml',
                'getVideoType',
                'getVideoPath',
                'getVideoThumbnail',
                'hasVideoEmbedHtml',
                'hasVideoEmbedUrl',
                'hasVideoPath'
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
            ->with('core.helper.video')
            ->willReturn($this->helper);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Tests getVideoType.
     */
    public function testGetVideoType()
    {
        $video = new Content([
            'content_status' => 1,
            'type'           => 'external',
            'starttime'      => new \DateTime()
            ]);

        $this->helper->expects($this->once())->method('getVideoType')
            ->with($video)
            ->willReturn($video->type);

        $this->assertEquals($video->type, get_video_type($video));
    }

    /**
     * Tests getVideoPath.
     */
    public function testGetVideoPath()
    {
        $video = new Content([
            'content_status' => 1,
            'type'           => 'external',
            'starttime'      => new \DateTime(),
            'path' => 'https://www.youtube.com/watch?v=OqrkMcvZg9A'
        ]);

        $this->helper->expects($this->once())->method('getVideoPath')
            ->with($video)
            ->willReturn($video->path);

        $this->assertEquals($video->path, get_video_path($video));
    }

    /**
     * Tests getVideoEmbedHtml.
     */
    public function testGetVideoEmbedHtml()
    {
        $video = new Content(
            [
                'content_status' => 1,
                'type'           => 'external',
                'starttime'      => new \DateTime(),
                'information' =>
                [
                    'embedHTML' =>
                    "<iframe type='text/html'" .
                    "src='http://www.youtube.com/embed/QGWew64soYo'" .
                    "width='560' height='349' frameborder='0'" .
                    "allowfullscreen='true'></iframe>"
                ]
            ]
        );

        $this->helper->expects($this->once())->method('getVideoEmbedHtml')
            ->with($video)
            ->willReturn($video->information['embedHTML']);

        $this->assertEquals($video->information['embedHTML'], get_video_embed_html($video));
    }

    /**
     * Tests getVideoEmbedUrl.
     */
    public function testGetVideoEmbedUrl()
    {
        $video = new Content(
            [
                'content_status' => 1,
                'type'           => 'external',
                'starttime'      => new \DateTime(),
                'information' =>
                [
                    'embedUrl' => 'http://www.youtube.com/embed/QGWew64soYo'
                ]
            ]
        );

        $this->helper->expects($this->once())->method('getVideoEmbedUrl')
            ->with($video)
            ->willReturn($video->information['embedUrl']);

        $this->assertEquals($video->information['embedUrl'], get_video_embed_url($video));
    }

    /**
     * Tests getVideoHtml.
     */
    public function testGetVideoHtml()
    {
        $video = new Content(
            [
                'content_status' => 1,
                'type'           => 'external',
                'starttime'      => new \DateTime(),
                'information' =>
                [
                    'embedUrl' => 'http://www.youtube.com/embed/QGWew64soYo'
                ]
            ]
        );

        $this->helper->expects($this->once())->method('getVideoHtml')
            ->with($video);

        get_video_html($video);
    }

    /**
     * Tests has_video_embed_html.
     */
    public function testHasVideoEmbedHtml()
    {
        $video = new Content(
            [
                'content_status' => 1,
                'type'           => 'external',
                'starttime'      => new \DateTime(),
                'information' =>
                [
                    'embedHTML' =>
                    "<iframe type='text/html'" .
                    "src='http://www.youtube.com/embed/QGWew64soYo'" .
                    "width='560' height='349' frameborder='0'" .
                    "allowfullscreen='true'></iframe>"
                ]
            ]
        );

        $this->helper->expects($this->once())->method('hasVideoEmbedHtml')
            ->with($video)
            ->willReturn(true);

        $this->assertTrue(has_video_embed_html($video));
    }

    /**
     * Tests has_video_embed_url.
     */
    public function testHasVideoEmbedUrl()
    {
        $video = new Content(
            [
                'content_status' => 1,
                'type'           => 'external',
                'starttime'      => new \DateTime(),
                'information' =>
                [
                    'embedUrl' => 'http://www.youtube.com/embed/QGWew64soYo'
                ]
            ]
        );

        $this->helper->expects($this->once())->method('hasVideoEmbedUrl')
            ->with($video)
            ->willReturn(true);

        $this->assertTrue(has_video_embed_url($video));
    }

    /**
     * Tests has_video_path.
     */
    public function testHasVideoPath()
    {
        $video = new Content(
            [
                'content_status' => 1,
                'type'           => 'external',
                'starttime'      => new \DateTime(),
                'path' => 'https://www.youtube.com/watch?v=OqrkMcvZg9A'
            ]
        );

        $this->helper->expects($this->once())->method('hasVideoPath')
            ->with($video)
            ->willReturn(true);

        $this->assertTrue(has_video_path($video));
    }

    /**
     * Tests get_video_thumbnail.
     */
    public function testGetVideoThumbnailWhenInternalPhoto()
    {
        $video = new Content([
            'related_contents' => [ [
                'content_type_name' => 'photo',
                'type'              => 'featured_frontpage',
                'target_id'         => 126,
                'caption'           => null,
                'position'          => 0
            ] ]
        ]);

        $photo = new Content([
            'content_status' => 1,
            'starttime'      => new \DateTime()
        ]);

        $this->helper->expects($this->once())->method('getVideoThumbnail')
            ->willReturn($photo);

        $this->assertEquals($photo, get_video_thumbnail($video, 'glorp'));
    }

    /**
     * Tests get_video_thumbnail.
     */
    public function testGetVideoThumbnailWhenExternalPhoto()
    {
        $video = new Content([
            'title'       => 'Aliquam viderer cu graeco ius.',
            'information' => [ 'thumbnail' => 'http://glorp.xxzz/path.jpg' ]
        ]);

        $externalPhoto = new Content([
            'content_status'    => 1,
            'content_type_name' => 'photo',
            'description'       => 'Aliquam viderer cu graeco ius.',
            'external_uri'      => 'http://glorp.xxzz/path.jpg'
        ]);

        $this->helper->expects($this->once())->method('getVideoThumbnail')
            ->willReturn($externalPhoto);

        $this->assertEquals($externalPhoto, get_video_thumbnail($video, 'thumbnail'));
    }
}
