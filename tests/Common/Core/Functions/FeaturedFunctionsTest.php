<?php

namespace Tests\Common\Core\Functions;

use Common\Model\Entity\Content;

/**
 * Defines test cases for content functions.
 */
class FeaturedFunctionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('Common\Core\Component\Helper\FeaturedMediaHelper')
            ->disableOriginalConstructor()
            ->setMethods([
                'getFeaturedMedia',
                'getFeaturedMediaCaption',
                'hasFeaturedMedia',
                'hasFeaturedMediaCaption'
            ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->with('core.helper.featured_media')
            ->willReturn($this->helper);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->content  = new Content(
            [
                'content_type_name' => 'opinion',
                'related_contents'  => [
                    [
                        'source_id'         => 1,
                        'target_id'         => 2,
                        'type'              => 'featured_frontpage',
                        'content_type_name' => 'photo'
                    ]
                ]
            ]
        );
        $this->featured = new Content([ 'pk_content' => 2, 'content_type_name' => 'photo' ]);
        $this->caption  = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tempora, libero!';
    }

    /**
     * Tests get_featured_media.
     */
    public function testGetFeaturedMedia()
    {
        $this->helper->expects($this->once())->method('getFeaturedMedia')
            ->with($this->content, 'featured_frontpage', true)
            ->willReturn($this->featured);

        $this->assertEquals($this->featured, get_featured_media($this->content, 'featured_frontpage', true));
    }

    /**
     * Tests get_featured_media_caption.
     */
    public function testGetFeaturedMediaCaption()
    {
        $this->helper->expects($this->once())->method('getFeaturedMediaCaption')
            ->with($this->content, 'featured_frontpage')
            ->willReturn($this->caption);

        $this->assertEquals($this->caption, get_featured_media_caption($this->content, 'featured_frontpage', true));
    }

    /**
     * Tests has_featured_media.
     */
    public function testHasFeaturedMedia()
    {
        $this->helper->expects($this->once())->method('hasFeaturedMedia')
            ->with($this->content, 'featured_frontpage')
            ->willReturn(true);

        $this->assertEquals(true, has_featured_media($this->content, 'featured_frontpage', true));
    }

    /**
     * Tests has_featured_media_caption.
     */
    public function testHasFeaturedMediaCaption()
    {
        $this->helper->expects($this->once())->method('hasFeaturedMediaCaption')
            ->with($this->content, 'featured_frontpage')
            ->willReturn(true);

        $this->assertEquals(true, has_featured_media_caption($this->content, 'featured_frontpage', true));
    }
}
