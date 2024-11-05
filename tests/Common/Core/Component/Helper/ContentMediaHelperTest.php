<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\ContentMediaHelper;
use Common\Model\Entity\Content;

/**
 * Defines test cases for ContentMediaHelper class.
 */
class ContentMediaHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->authorHelper = $this->getMockBuilder('Common\Core\Component\Helper\AuthorHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'hasAuthorAvatar', 'getAuthorAvatar' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' , 'getParameter'])
            ->getMock();

        $this->contentHelper = $this->getMockBuilder('Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContent', 'getType' ])
            ->getMock();

        $this->featuredHelper = $this->getMockBuilder('Common\Core\Component\Helper\FeaturedMediaHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'hasFeaturedMedia', 'getFeaturedMedia' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Common\Model\Entity\Instance')
            ->setMethods([ 'getMediaShortPath' ])
            ->getMock();

        $this->imageHelper = $this->getMockBuilder('ImageHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getInformation' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->orm = $this->getMockBuilder('OrmEntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->ph = $this->getMockBuilder('PhotoHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPhotoPath', 'getPhotoWidth', 'getPhotoHeight' ])
            ->getMock();

        $this->sh = $this->getMockBuilder('SettingHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getLogo', 'hasLogo' ])
            ->getMock();

        $this->container->expects($this->any())->method('getParameter')
            ->with('core.paths.public')->willReturn('/gorp/qux');

        $this->orm->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->helper = new ContentMediaHelper($this->container);
    }

    /**
     * Callback funcion to return different services based on string.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.helper.author':
                return $this->authorHelper;

            case 'core.helper.content':
                return $this->contentHelper;

            case 'core.helper.featured_media':
                return $this->featuredHelper;

            case 'core.helper.photo':
                return $this->ph;

            case 'core.helper.setting':
                return $this->sh;

            case 'core.helper.image':
                return $this->imageHelper;

            case 'core.instance':
                return $this->instance;

            case 'orm.manager':
                return $this->orm;
        }

        return null;
    }

    /**
     * Tests getMedia method when the content has featured media.
     */
    public function testGetMediaWhenFeatured()
    {
        $content = new Content([
            'pk_content'        => 1,
            'content_type_name' => 'opinion',
            'related_contents'  => [
                [
                    'source_id'         => 1,
                    'target_id'         => 2,
                    'content_type_name' => 'photo',
                    'type'              => 'featured_inner'
                ]
            ]
        ]);

        $photo = new Content([
            'pk_content'        => 2,
            'content_type_name' => 'photo',
            'width'             => 1920,
            'height'            => 1080
        ]);

        $this->featuredHelper->expects($this->once())->method('hasFeaturedMedia')
            ->with($content)
            ->willReturn(true);

        $this->featuredHelper->expects($this->once())->method('getFeaturedMedia')
            ->with($content)
            ->willReturn($photo);

        $this->contentHelper->expects($this->once())->method('getContent')
            ->with($photo)
            ->willReturn($photo);

        $this->assertEquals($photo, $this->helper->getMedia($content));
    }

    /**
     * Tests getMedia method when performing a deep search.
     */
    public function testGetMediaWhenDeep()
    {
        $content = new Content([
            'pk_content'        => 1,
            'content_type_name' => 'opinion',
            'related_contents'  => [
                [
                    'source_id'         => 1,
                    'target_id'         => 2,
                    'content_type_name' => 'video',
                    'type'              => 'featured_inner'
                ]
            ]
        ]);

        $video = new Content(
            [
                'pk_content'        => 2,
                'target_id'         => 3,
                'content_type_name' => 'video',
                'type'              => 'featured_frontpage'
            ]
        );

        $photo = new Content([
            'pk_content'        => 3,
            'content_type_name' => 'photo',
            'width'             => 1920,
            'height'            => 1080
        ]);

        $this->featuredHelper->expects($this->at(0))->method('hasFeaturedMedia')
            ->with($content, 'social')
            ->willReturn(false);

        $this->featuredHelper->expects($this->at(1))->method('hasFeaturedMedia')
            ->with($content, 'inner')
            ->willReturn(true);

        $this->featuredHelper->expects($this->at(2))->method('getFeaturedMedia')
            ->with($content, 'inner')
            ->willReturn($video);

        $this->featuredHelper->expects($this->at(3))->method('hasFeaturedMedia')
            ->with($video, 'frontpage')
            ->willReturn(true);

        $this->featuredHelper->expects($this->at(4))->method('getFeaturedMedia')
            ->with($video, 'frontpage')
            ->willReturn($photo);

        $this->contentHelper->expects($this->once())->method('getContent')
            ->with($photo, 'photo')
            ->willReturn($photo);

        $this->helper->getMedia($content);
    }

    /**
     * Tests getMedia method when the content has author avatar.
     */
    public function testGetMediaWhenAvatar()
    {
        $content = new Content([
            'pk_content'        => 1,
            'content_type_name' => 'opinion',
            'avatar_img_id'     => 2
        ]);

        $photo = new Content([
            'pk_content'        => 2,
            'content_type_name' => 'photo',
            'width'             => 1920,
            'height'            => 1080
        ]);

        $this->featuredHelper->expects($this->at(0))->method('hasFeaturedMedia')
            ->with($content, 'social')
            ->willReturn(false);

        $this->contentHelper->expects($this->at(0))->method('getType')
            ->with($content)
            ->willReturn('opinion');

        $this->authorHelper->expects($this->at(0))->method('hasAuthorAvatar')
            ->with($content)
            ->willReturn(true);

        $this->authorHelper->expects($this->at(1))->method('getAuthorAvatar')
            ->with($content)
            ->willReturn(2);

        $this->contentHelper->expects($this->at(1))->method('getContent')
            ->with(2)
            ->willReturn($photo);

        $this->assertEquals($photo, $this->helper->getMedia($content));
    }

    /**
     * Tests getMediaFromLogo.
     */
    public function testGetMediaWhenLogo()
    {
        $content = new Content([
            'pk_content'        => 1,
            'content_type_name' => 'article',
        ]);

        $media = new Content(
            [
                'pk_content'        => 2,
                'content_type_name' => 'photo',
                'path'   => '/media/frog/sections/sn_default_img.jpg',
                'width'             => 1920,
                'height'            => 1080
            ]
        );

        $this->featuredHelper->expects($this->at(0))->method('hasFeaturedMedia')
            ->with($content)
            ->willReturn(false);

        $this->featuredHelper->expects($this->at(1))->method('hasFeaturedMedia')
            ->with($content, 'inner')
            ->willReturn(false);

        $this->sh->expects($this->once())->method('hasLogo')
            ->with('embed')
            ->willReturn($media);

        $this->sh->expects($this->once())->method('getLogo')
            ->with('embed')
            ->willReturn($media);

        $this->contentHelper->expects($this->once())->method('getContent')
            ->with($media)
            ->willReturn($media);

        $this->assertEquals($media, $this->helper->getMedia($content));
    }

    /**
     * Tests getMedia when no media.
     */
    public function testGetMediaWhenNoMedia()
    {
        $this->assertNull($this->helper->getMedia(null));
    }
}
