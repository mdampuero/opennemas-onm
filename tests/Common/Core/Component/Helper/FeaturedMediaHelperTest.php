<?php


namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\ContentHelper;
use Common\Core\Component\Helper\FeaturedMediaHelper;
use Common\Model\Entity\Content;

/**
 * Defines test cases for ContentMediaHelper class.
 */
class FeaturedMediaHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->content = new Content([
            'content_status' => 1,
            'in_litter'      => 0,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->em = $this->getMockBuilder('Repository\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'find' ])
            ->getMock();

        $this->subscriptionHelper = $this->getMockBuilder('Common\Core\Component\Helper\SubscriptionHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'isHidden' ])
            ->getMock();

        $this->relatedHelper = $this->getMockBuilder('Common\Core\Component\Helper\RelatedHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRelated' ])
            ->getMock();

        $this->videoHelper = $this->getMockBuilder('Common\Core\Component\Helper\VideoHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getVideoThumbnail' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale' . uniqid())
            ->setMethods([ 'getTimeZone' ])->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('Common\Api\Service\TagService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getListByIds' ])
            ->getMock();

        $this->locale->expects($this->any())->method('getTimeZone')
            ->willReturn(new \DateTimeZone('UTC'));

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->contentHelper = new ContentHelper($this->container);

        $this->helper = new FeaturedMediaHelper(
            $this->contentHelper,
            $this->relatedHelper,
            $this->subscriptionHelper,
            $this->template,
            $this->videoHelper
        );

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
            case 'api.service.tag':
                return $this->ts;

            case 'core.helper.content':
                return $this->contentHelper;

            case 'core.helper.related':
                return $this->relatedHelper;

            case 'core.helper.subscription':
                return $this->subscriptionHelper;

            case 'core.template.frontend':
                return $this->template;

            case 'entity_repository':
                return $this->em;

            case 'core.locale':
                return $this->locale;

            default:
                return null;
        }
    }

    /**
     * Tests getFeaturedMedia for an external content.
     */
    public function testGetFeaturedMediaForExternalContent()
    {
        $photo                    = new \Content();
        $photo->id                = 893;
        $photo->content_status    = 1;
        $photo->starttime         = '2020-01-01 00:00:00';
        $photo->content_type_name = 'photo';

        $this->template->expects($this->once())->method('getValue')
            ->with('related', [])->willReturn([ 893 => $photo ]);

        $this->assertNull($this->helper->getFeaturedMedia($this->content, 'baz'));
        $this->assertNull($this->helper->getFeaturedMedia($this->content, 'inner'));

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'book';
        $content->cover_id          = 893;
        $content->external          = 1;

        $this->assertEquals($photo, $this->helper->getFeaturedMedia($content, 'frontpage'));
    }

    /**
     * Tests getFeaturedMedia for events.
     */
    public function testGetFeaturedMediaForEvents()
    {
        $photo                    = new \Content();
        $photo->id                = 893;
        $photo->content_status    = 1;
        $photo->starttime         = '2020-01-01 00:00:00';
        $photo->content_type_name = 'photo';

        $this->content->content_type_name = 'event';
        $this->content->related_contents  = [ [
            'content_type_name' => 'photo',
            'source_id'         => 485,
            'target_id'         => 893,
            'type'              => 'featured_frontpage',
            'caption'           => 'Justo auctor vero probo pertinax',
            'position'          => 9
        ] ];

        $this->relatedHelper->expects($this->once())->method('getRelated')
            ->willReturn([ [
                'item'     => $photo,
                'caption'  => 'Justo auctor vero probo pertinax',
                'position' => 9
            ] ]);

        $this->assertEquals($photo, $this->helper->getFeaturedMedia($this->content, 'frontpage'));
    }

    /**
     * Tests getFeaturedMedia when the featured media is a photo.
     */
    public function testGetFeaturedMediaWhenFeaturedPhoto()
    {
        $photo                    = new \Content();
        $photo->id                = 893;
        $photo->content_status    = 1;
        $photo->starttime         = '2020-01-01 00:00:00';
        $photo->content_type_name = 'photo';

        $this->em->expects($this->once())->method('find')
            ->with('Photo', 893)->willReturn($photo);

        $this->assertNull($this->helper->getFeaturedMedia($this->content, 'baz'));
        $this->assertNull($this->helper->getFeaturedMedia($this->content, 'inner'));

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'book';
        $content->cover_id          = 893;

        $this->assertEquals($photo, $this->helper->getFeaturedMedia($content, 'frontpage'));
    }

    /**
     * Tests getFeaturedMedia when the featured media is a video and the
     * featured media for the video is a photo.
     */
    public function testGetFeaturedMediaWhenFeaturedVideoWithPhoto()
    {
        $photo                    = new \Content();
        $photo->id                = 893;
        $photo->content_status    = 1;
        $photo->starttime         = '2020-01-01 00:00:00';
        $photo->content_type_name = 'photo';

        $video                    = new \Content();
        $video->id                = 779;
        $video->content_status    = 1;
        $video->starttime         = '2020-01-01 00:00:00';
        $video->content_type_name = 'video';
        $video->related_contents  = [
            [
                'content_type_name' => 'photo',
                'type'              => 'featured_frontpage',
                'target_id'         => 893,
                'caption'           => null,
                'position'          => 0
            ]
        ];

        $this->relatedHelper->expects($this->once())->method('getRelated')
            ->willReturn([ [
                'item'     => $video,
                'caption'  => 'Lorem ipsum dolor sit amet.',
                'position' => 0
            ] ]);

        $this->videoHelper->expects($this->once())->method('getVideoThumbnail')
            ->willReturn($photo);

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->related_contents  = [
            [
                'type'              => 'featured_frontpage',
                'target_id'         => 779,
                'content_type_name' => 'video',
                'caption'           => 'Lorem ipsum dolor sit amet.',
                'position'          => 0
            ]
        ];

        $this->assertEquals($photo, $this->helper->getFeaturedMedia($content, 'frontpage'));
    }

    /**
     * Tests getFeaturedMedia when the featured media is a video and the
     * featured media for the video is the URL of the external photo.
     */
    public function testGetFeaturedMediaWhenFeaturedVideoWithUrl()
    {
        $video = new Content([
            'content_status'    => 1,
            'content_type_name' => 'video',
            'information'       => [ 'thumbnail' => 'http://waldo/thud.jpg' ],
            'pk_content'        => 779,
            'starttime'         => new \Datetime('2020-01-01 00:00:00')
        ]);

        $photo = new Content([
            'content_status'    => 1,
            'content_type_name' => 'photo',
            'description'       => null,
            'external_uri'      => 'http://waldo/thud.jpg'
        ]);

        $this->relatedHelper->expects($this->once())->method('getRelated')
            ->willReturn([ [
                'item'     => $video,
                'caption'  => 'Lorem ipsum dolor sit amet.',
                'position' => 0
            ] ]);

        $this->videoHelper->expects($this->once())->method('getVideoThumbnail')
            ->willReturn($photo);

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->related_contents  = [
            [
                'type'              => 'featured_frontpage',
                'target_id'         => 779,
                'content_type_name' => 'video',
                'caption'           => 'Lorem ipsum dolor sit amet.',
                'position'          => 0
            ]
        ];

        $this->assertEquals($photo, $this->helper->getFeaturedMedia($content, 'frontpage'));
    }

    /**
     * Tests getFeaturedMediaCaption.
     */
    public function testGetFeaturedMediaCaption()
    {
        $this->assertNull($this->helper->getFeaturedMediaCaption($this->content, 'baz'));
        $this->assertNull($this->helper->getFeaturedMediaCaption($this->content, 'inner'));


        $this->content->content_type_name = 'article';
        $this->content->related_contents  = [];

        $this->assertNull($this->helper->getFeaturedMediaCaption($this->content, 'frontpage'));

        $this->content->related_contents = [ [
            'type'    => 'featured_frontpage',
            'caption' => 'Rhoncus pretium'
        ] ];

        $this->assertEquals('Rhoncus pretium', $this->helper->getFeaturedMediaCaption($this->content, 'frontpage'));

        $content = new Content([
            'content_status'    => 1,
            'content_type_name' => 'event',
            'in_litter'         => 0,
            'starttime'         => new \Datetime('2020-01-01 00:00:00'),
            'related_contents'  => [ [
                'type'    => 'featured_inner',
                'caption' => 'glorp "foobar" <p>fubar</p>'
            ] ]
        ]);

        $this->assertEmpty($this->helper->getFeaturedMediaCaption($content, 'frontpage'));
        $this->assertEquals(
            'glorp &quot;foobar&quot; &lt;p&gt;fubar&lt;/p&gt;',
            $this->helper->getFeaturedMediaCaption($content, 'inner')
        );
    }

    /**
     * Tests getRelated.
     */
    public function testGetRelated()
    {
        $actual = [
            [
                'caption'           => 'Facilis, aperiam!',
                'content_type_name' => 'photo',
                'position'          => 0,
                'source_id'         => 10,
                'target_id'         => 20,
                'type'              => 'photo'
            ]
        ];

        $content = new Content([
            'pk_content'        => 1,
            'content_type_name' => 'photo',
            'description'       => 'Lorem ipsum dolor sit amet.'
         ]);

         $relationships = [ 'featured_frontpage', 'featured_inner' ];

        $result = [
                [
                    'caption'           => 'Facilis, aperiam!',
                    'content_type_name' => 'photo',
                    'position'          => 0,
                    'source_id'         => 10,
                    'target_id'         => 20,
                    'type'              => 'photo'
                ],
                [
                    'caption'           => 'Lorem ipsum dolor sit amet.',
                    'content_type_name' => 'photo',
                    'position'          => 0,
                    'target_id'         => 1,
                    'type'              => 'featured_frontpage'
                ],
                [
                    'caption'           => 'Lorem ipsum dolor sit amet.',
                    'content_type_name' => 'photo',
                    'position'          => 0,
                    'target_id'         => 1,
                    'type'              => 'featured_inner'
                ]
            ];

        $method = new \ReflectionMethod(
            get_class($this->helper),
            'getRelated'
        );
        $method->setAccessible(true);

        $this->assertEquals($result, $method->invokeArgs($this->helper, [ $content, $relationships, $actual ]));
    }

    /**
     * Tests hasFeaturedMedia.
     */
    public function testHasFeaturedMedia()
    {
        $photo                    = new \Content();
        $photo->id                = 893;
        $photo->content_status    = 1;
        $photo->in_litter         = 0;
        $photo->starttime         = '2020-01-01 00:00:00';
        $photo->content_type_name = 'photo';

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->related_contents  = [
            [
                'type'              => 'featured_frontpage',
                'target_id'         => 893,
                'content_type_name' => 'photo',
                'caption'           => 'Lorem ipsum dolor sit amet.',
                'position'          => 0
            ]
        ];

        $this->assertFalse($this->helper->hasFeaturedMedia($content, 'baz'));
        $this->assertFalse($this->helper->hasFeaturedMedia($content, 'inner'));

        $this->relatedHelper->expects($this->at(0))->method('getRelated')
            ->with($content, 'featured_frontpage')
            ->willReturn(
                [
                    'item'     => $photo,
                    'caption'  => 'Lorem ipsum dolor sit amet.',
                    'position' => 0
                ]
            );

        $this->assertTrue($this->helper->hasFeaturedMedia($content, 'frontpage'));
    }

    /**
     * Tests hasFeaturedMediaCaption.
     */
    public function testHasFeaturedMediaCaption()
    {
        $this->content->content_type_name = 'article';
        $this->content->related_contents  = [ [
            'type'    => 'featured_inner',
            'caption' => 'Rhoncus pretium'
        ] ];

        $this->assertFalse($this->helper->hasFeaturedMediaCaption($this->content, 'baz'));
        $this->assertFalse($this->helper->hasFeaturedMediaCaption($this->content, 'frontpage'));
        $this->assertTrue($this->helper->hasFeaturedMediaCaption($this->content, 'inner'));
    }
}
