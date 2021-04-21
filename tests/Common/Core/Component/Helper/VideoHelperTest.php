<?php

namespace Tests\Common\Core\Functions;

use Common\Core\Component\Helper\VideoHelper;
use Common\Model\Entity\Content;
use ReflectionProperty;

/**
 * Defines test cases for video helper functions.
 */
class VideoHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->contentHelper = $this->getMockBuilder('Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods(['isReadyForPublish'])
            ->getMock();

        $this->em = $this->getMockBuilder('Repository\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'find' ])
            ->getMock();

        $this->filter = $this->getMockBuilder('Opennemas\Data\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'set', 'filter', 'get' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->contentHelper->expects($this->any())->method('isReadyForPublish')
            ->willReturn(true);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->helper = new VideoHelper($this->contentHelper, $this->template, $this->filter);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Returns a mocked service based on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.template.admin':
                return $this->template;

            case 'core.helper.content':
                return $this->contentHelper;

            case 'data.manager.filter':
                return $this->filter;

            case 'entity_repository':
                return $this->em;

            default:
                return null;
        }
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

        $this->assertEquals($video->type, $this->helper->getVideoType($video));
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

        $this->assertEquals($video->path, $this->helper->getVideoPath($video));
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

        $this->assertEquals($video->information['embedHTML'], $this->helper->getVideoEmbedHtml($video));
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

        $this->assertEquals($video->information['embedUrl'], $this->helper->getVideoEmbedUrl($video));
    }

    /**
     * Tests getVideoHtml.
     */
    public function testGetVideoHtml()
    {
        $externalOutput = '<video controls>' .
            '<source src="https://www.learningcontainer.com/wp-content/uploads/2020/05/sample-mp4-file.mp4"' .
            'type=video/mp4>' .
            '</video>';

        $webSourceOutput = '<div class="video-container">' .
            '<iframe width=560 height=320 src="https://www.youtube.com/watch?v=WQn-D-i5lyM"' .
            'frameborder="0" allowfullscreen>' .
            '</iframe></div>';

        $video = new Content(
            [
                'type' => 'script',
                'body' => '<video width="400" controls>' .
                    '<source src="mov_bbb.mp4" type="video/mp4">' .
                    '<source src="mov_bbb.ogg" type="video/ogg">' .
                    'Your browser does not support HTML video.' .
                    '</video>'
            ]
        );

        $video1 = new Content(
            [
                'type'        => 'external',
                'information' =>
                [ 'source' =>
                    ['mp4' => 'https://www.learningcontainer.com/wp-content/uploads/2020/05/sample-mp4-file.mp4' ]
                ]
            ]
        );

        $video2 = new Content(
            [
                'type'        => 'Youtube',
                'information' =>
                [ 'embedUrl' =>
                    ['mp4' => 'https://www.youtube.com/watch?v=WQn-D-i5lyM' ]
                ]
            ]
        );

        $this->template->expects($this->at(0))->method('fetch')
            ->with('video/render/external.tpl', ['info' => $video1->information, 'height' => 320, 'width' => 560])
            ->willReturn($externalOutput);

        $this->template->expects($this->at(1))->method('fetch')
            ->with('video/render/web-source.tpl', ['info' => $video2->information, 'height' => 320, 'width' => 560])
            ->willReturn($webSourceOutput);

        $this->filter->expects($this->at(0))->method('set')->willReturn($this->filter);
        $this->filter->expects($this->at(1))->method('filter')->with('amp')->willReturn($this->filter);
        $this->filter->expects($this->at(2))->method('get')->willReturn(sprintf('<div>%s</div>', $video->body));

        $this->assertEquals(sprintf('<div>%s</div>', $video->body), $this->helper->getVideoHtml($video));
        $this->assertEquals($externalOutput, $this->helper->getVideoHtml($video1));
        $this->assertEquals($webSourceOutput, $this->helper->getVideoHtml($video2));
        $this->assertEquals(
            sprintf('<div>%s</div>', $video->body),
            $this->helper->getVideoHtml($video, 560, 320, true)
        );
    }

    /**
     * Tests hasVideoEmbedHtml.
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

        $this->assertTrue($this->helper->hasVideoEmbedHtml($video));
    }

    /**
     * Tests hasVideoEmbedUrl.
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

        $this->assertTrue($this->helper->hasVideoEmbedUrl($video));
    }

    /**
     * Tests hasVideoPath.
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

        $this->assertTrue($this->helper->hasVideoPath($video));
    }

    /**
     * Tests getVideoThumbnail.
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

        $property = new ReflectionProperty(get_class($this->contentHelper), 'entityManager');
        $property->setAccessible(true);

        $property->setValue($this->contentHelper, $this->em);

        $this->em->expects($this->at(0))->method('find')
            ->with('photo', 126)
            ->willReturn($photo);

        $this->assertEquals($photo, $this->helper->getVideoThumbnail($video, 'glorp'));
    }

    /**
     * Tests getVideoThumbnail.
     */
    public function testGetVideoThumbnailWhenExternalPhoto()
    {
        $video = new Content([
            'title'       => 'Aliquam viderer cu graeco ius.',
            'information' => [ 'thumbnail' => 'http://glorp.xxzz/path.jpg' ]
        ]);

        $this->assertEquals(new Content([
            'content_status'    => 1,
            'content_type_name' => 'photo',
            'description'       => 'Aliquam viderer cu graeco ius.',
            'external_uri'      => 'http://glorp.xxzz/path.jpg'
        ]), $this->helper->getVideoThumbnail($video, 'thumbnail'));
    }
}
