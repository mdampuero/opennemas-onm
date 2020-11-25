<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Functions;

use Api\Exception\GetItemException;
use Common\Model\Entity\Content;
use Common\Model\Entity\Tag;

/**
 * Defines test cases for content functions.
 */
class ContentFunctionsTest extends \PHPUnit\Framework\TestCase
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

        $this->helper = $this->getMockBuilder('Common\Core\Component\Helper\SubscriptionHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'isHidden' ])
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

            case 'core.helper.subscription':
                return $this->helper;

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
     * Tests get_content when a content is already provided as parameter.
     */
    public function testGetContentFromParameter()
    {
        $this->assertNull(get_content());
        $this->assertEquals($this->content, get_content($this->content));
    }

    /**
     * Tests get_content when item is not found.
     */
    public function testGetContentWhenNotFound()
    {
        $this->em->expects($this->once())->method('find')
            ->with('Photo', 43)
            ->will($this->throwException(new GetItemException()));

        $this->assertNull(get_content(43, 'Photo'));
    }

    /**
     * Tests get_content when the content id is provided as parameter.
     */
    public function testGetContentFromParameterWhenId()
    {
        $this->em->expects($this->once())->method('find')
            ->with('Photo', 43)->willReturn($this->content);

        $this->assertEquals($this->content, get_content(43, 'Photo'));
    }

    /**
     * Tests get_content when the item is extracted from template and it is a
     * content.
     */
    public function testGetContentFromTemplateWhenContent()
    {
        $this->template->expects($this->once())->method('getValue')
            ->with('item')->willReturn($this->content);

        $this->assertEquals($this->content, get_content());
    }

    /**
     * Tests get_creation_date.
     */
    public function testGetCreationDate()
    {
        $this->content->created = '2010-10-10 10:00:00';

        $this->assertEquals(
            new \Datetime('2010-10-10 10:00:00'),
            get_creation_date($this->content)
        );
    }

    /**
     * Tests get_description.
     */
    public function testGetDescription()
    {
        $this->assertNull(get_description($this->content));

        $this->content->description = 'His ridens eu sed quod ignota.';
        $this->assertEquals('His ridens eu sed quod ignota.', get_description($this->content));

        $this->content->description = 'Percipit "mollis" at scriptorem usu.';
        $this->assertEquals('Percipit &quot;mollis&quot; at scriptorem usu.', get_description($this->content));
    }

    /**
     * Tests get_featured_media for an external content.
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

        $this->assertNull(get_featured_media($this->content, 'baz'));
        $this->assertNull(get_featured_media($this->content, 'inner'));

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->img1              = 893;
        $content->external          = 1;

        $this->assertEquals($photo, get_featured_media($content, 'frontpage'));
    }

    /**
     * Tests get_featured_media for events.
     */
    public function testGetFeaturedMediaForEvents()
    {
        $photo                    = new \Content();
        $photo->id                = 893;
        $photo->content_status    = 1;
        $photo->starttime         = '2020-01-01 00:00:00';
        $photo->content_type_name = 'photo';

        $this->em->expects($this->once())->method('find')
            ->with('photo', 893)->willReturn($photo);

        $this->content->content_type_name = 'event';
        $this->content->related_contents  = [ [
            'content_type_name' => 'photo',
            'source_id'         => 485,
            'target_id'         => 893,
            'type'              => 'featured_frontpage'
        ] ];

        $this->assertEquals($photo, get_featured_media($this->content, 'frontpage'));
    }


    /**
     * Tests get_featured_media when the featured media is a photo.
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

        $this->assertNull(get_featured_media($this->content, 'baz'));
        $this->assertNull(get_featured_media($this->content, 'inner'));

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->img1              = 893;

        $this->assertEquals($photo, get_featured_media($content, 'frontpage'));
    }

    /**
     * Tests get_featured_media when the featured media is a video and the
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
                'type' => 'featured_frontpage',
                'target_id' => 893
            ]
        ];

        $this->em->expects($this->at(0))->method('find')
            ->with('Video', 779)->willReturn($video);
        $this->em->expects($this->at(1))->method('find')
            ->with('photo', 893)->willReturn($photo);

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->fk_video          = 779;

        $this->assertEquals($photo, get_featured_media($content, 'frontpage'));
    }

    /**
     * Tests get_featured_media when the featured media is a video and the
     * featured media for the video is the URL of the external photo.
     */
    public function testGetFeaturedMediaWhenFeaturedVideoWithUrl()
    {
        $video                    = new \Content();
        $video->id                = 779;
        $video->content_status    = 1;
        $video->starttime         = '2020-01-01 00:00:00';
        $video->content_type_name = 'video';
        $video->information       = [ 'thumbnail' => 'http://waldo/thud.jpg' ];

        $this->em->expects($this->once())->method('find')
            ->with('Video', 779)->willReturn($video);

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->fk_video          = 779;

        $this->assertEquals('http://waldo/thud.jpg', get_featured_media($content, 'frontpage'));
    }

    /**
     * Tests get_featured_media_caption.
     */
    public function testGetFeaturedMediaCaption()
    {
        $this->assertNull(get_featured_media_caption($this->content, 'baz'));
        $this->assertNull(get_featured_media_caption($this->content, 'inner'));


        $this->content->content_type_name = 'article';

        $this->assertNull(get_featured_media_caption($this->content, 'frontpage'));

        $this->content->img1_footer = 'Rhoncus pretium';

        $this->assertEquals('Rhoncus pretium', get_featured_media_caption($this->content, 'frontpage'));

        $content = new Content([
            'content_status'    => 1,
            'content_type_name' => 'event',
            'in_litter'         => 0,
            'starttime'         => new \Datetime('2020-01-01 00:00:00'),
            'related_contents'  => [
                [ 'type' => 'featured_inner', 'caption' => 'glorp' ]
            ]
        ]);

        $this->assertEmpty(get_featured_media_caption($content, 'frontpage'));
        $this->assertEquals('glorp', get_featured_media_caption($content, 'inner'));
    }

    /**
     * Tests get_publication_date.
     */
    public function testGetPublicationDate()
    {
        $this->assertTrue(new \Datetime() <= get_publication_date(new Content()));

        $this->content->created   = new \Datetime('2010-10-10 10:00:00');
        $this->content->starttime = null;

        $this->assertEquals(
            new \Datetime('2010-10-10 10:00:00'),
            get_publication_date($this->content)
        );

        $this->content->created   = new \Datetime('2010-10-10 10:00:00');
        $this->content->starttime = new \Datetime('2010-10-10 20:00:00');

        $this->assertEquals(
            new \Datetime('2010-10-10 20:00:00'),
            get_publication_date($this->content)
        );
    }

    /**
     * Tests get_type when a content is already provided as parameter.
     */
    public function testGetProperty()
    {
        $this->content->wobble = 'wubble';

        $this->assertNull(get_property($this->content, 'corge'));
        $this->assertEquals('wubble', get_property($this->content, 'wobble'));
    }

    /**
     * Tests get_pretitle.
     */
    public function testGetPretitle()
    {
        $this->assertNull(get_pretitle($this->content));

        $this->content->pretitle = 'His ridens eu sed quod ignota.';
        $this->assertEquals('His ridens eu sed quod ignota.', get_pretitle($this->content));

        $this->content->pretitle = 'Percipit "mollis" at scriptorem usu.';
        $this->assertEquals('Percipit &quot;mollis&quot; at scriptorem usu.', get_pretitle($this->content));
    }

    /**
     * Tests get_property when the item is extracted from template and it is
     * not a content.
     */
    public function testGetPropertyFromTemplateWhenNoContent()
    {
        $this->template->expects($this->once())->method('getValue')
            ->with('item')->willReturn(null);

        $this->assertNull(get_property(null, 'flob'));
    }

    /**
     * Tests get_related when
     */
    public function testGetRelated()
    {
        $article                 = new \Content();
        $article->id             = 893;
        $article->content_status = 1;
        $article->starttime      = '2020-01-01 00:00:00';

        $this->em->expects($this->once())->method('find')
            ->with('article', 205)->willReturn($article);

        $content                 = new \Content();
        $content->content_status = 1;
        $content->in_litter      = 0;
        $content->starttime      = '2020-01-01 00:00:00';

        $this->assertEmpty(get_related($content, 'inner'));

        $content->related_contents = [ [
            'content_type_name' => 'article',
            'target_id'         => 205,
            'type'              => 'related_inner'
        ] ];

        $this->assertEmpty(get_related($content, 'inner'));

        $this->assertEquals(
            [ $article ],
            get_related($content, 'related_inner')
        );
    }

    /**
     * Tests get_related when for an external content
     */
    public function testGetRelatedForExternal()
    {
        $article                 = new \Content();
        $article->id             = 205;
        $article->content_status = 1;
        $article->starttime      = '2020-01-01 00:00:00';

        $this->template->expects($this->once())->method('getValue')
            ->with('related')->willReturn([ 205 => $article ]);

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->external          = 1;
        $content->related_contents  = [ [
            'content_type_name' => 'article',
            'target_id'         => 205,
            'type'              => 'related_inner',
        ] ];

        $this->assertEquals(
            [ $article ],
            get_related($content, 'related_inner')
        );
    }

    /**
     * Tests get_related_contents.
     */
    public function testGetRelatedContents()
    {
        $article                 = new \Content();
        $article->id             = 893;
        $article->content_status = 1;
        $article->starttime      = '2020-01-01 00:00:00';

        $this->em->expects($this->any())->method('find')
            ->with('article', 205)->willReturn($article);

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->target_id         = 205;
        $content->related_contents  = [ [
            'content_type_name' => 'article',
            'target_id'         => 205,
            'type'              => 'related_inner',
        ] ];

        $this->assertEmpty(get_related_contents($content, 'mumble'));
        $this->assertEquals([ $article ], get_related_contents($content, 'inner'));
    }

    /**
     * Tests get_summary.
     */
    public function testGetSummary()
    {
        $this->assertNull(get_summary($this->content));

        $this->content->summary = 'His ridens eu sed quod ignota.';
        $this->assertEquals('His ridens eu sed quod ignota.', get_summary($this->content));

        $this->content->summary = 'Percipit "mollis" at scriptorem usu.';
        $this->assertEquals('Percipit &quot;mollis&quot; at scriptorem usu.', get_summary($this->content));
    }

    /**
     * Tests get_tags.
     */
    public function testGetTags()
    {
        $this->assertEquals([], get_tags($this->content));

        $this->content->tags = [];
        $this->assertEquals([], get_tags($this->content));

        $tags = [ new Tag([ 'id' => 917 ]), new Tag([ 'id' => 837 ]) ];

        $this->ts->expects($this->once())->method('getListByIds')
            ->with([ 971, 837 ])
            ->willReturn([ 'items' => $tags ]);

        $this->content->tags = [ 971, 837 ];
        $this->assertEquals($tags, get_tags($this->content));
    }

    /**
     * Tests get_title.
     */
    public function testGetTitle()
    {
        $this->assertNull(get_title($this->content));

        $this->content->title = 'His ridens eu sed quod ignota.';
        $this->assertEquals('His ridens eu sed quod ignota.', get_title($this->content));

        $this->content->title = 'Percipit "mollis" at scriptorem usu.';
        $this->assertEquals('Percipit &quot;mollis&quot; at scriptorem usu.', get_title($this->content));
    }

    /**
     * Tests get_type.
     */
    public function testGetType()
    {
        $this->assertNull(get_type(new Content([ 'flob' => 'wibble' ])));

        $this->content->content_type_name = 'article';
        $this->assertEquals('article', get_type($this->content));

        $this->content->content_type_name = 'static_page';
        $this->assertEquals('Static page', get_type($this->content, true));
    }

    /**
     * Tests has_description.
     */
    public function testHasDescription()
    {
        $this->assertFalse(has_description($this->content));

        $this->content->description = 'Percipit "mollis" at scriptorem usu.';
        $this->assertTrue(has_description($this->content));
    }

    /**
     * Tests has_featured_media.
     */
    public function testHasFeaturedMedia()
    {
        $photo                    = new \Content();
        $photo->id                = 893;
        $photo->content_status    = 1;
        $photo->in_litter         = 0;
        $photo->starttime         = '2020-01-01 00:00:00';
        $photo->content_type_name = 'photo';

        $this->em->expects($this->once())->method('find')
            ->with('Photo', 893)->willReturn($photo);

        $this->assertNull(get_featured_media($this->content, 'baz'));
        $this->assertNull(get_featured_media($this->content, 'inner'));

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->img1              = 893;

        $this->em->expects($this->once())->method('find')
            ->with('Photo', 893)->willReturn($photo);

        $this->assertFalse(has_featured_media($content, 'baz'));
        $this->assertFalse(has_featured_media($content, 'inner'));
        $this->assertTrue(has_featured_media($content, 'frontpage'));
    }

    /**
     * Tests has_featured_media_caption.
     */
    public function testHasFeaturedMediaCaption()
    {
        $this->content->content_type_name = 'article';
        $this->content->img1_footer       = 'Rhoncus pretium';

        $this->helper->expects($this->at(0))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse(has_featured_media_caption($this->content, 'baz'));
        $this->assertFalse(has_featured_media_caption($this->content, 'frontpage'));
        $this->assertTrue(has_featured_media_caption($this->content, 'frontpage'));
    }

    /**
     * Tests has_pretitle.
     */
    public function testHasPretitle()
    {
        $this->helper->expects($this->at(0))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse(has_pretitle($this->content));

        $this->content->pretitle = 'Percipit "mollis" at scriptorem usu.';
        $this->assertFalse(has_pretitle($this->content));
        $this->assertTrue(has_pretitle($this->content));
    }

    /**
     * Tests has_related_contents.
     */
    public function testHasRelatedContents()
    {
        $article                 = new \Content();
        $article->id             = 893;
        $article->content_status = 1;
        $article->starttime      = '2020-01-01 00:00:00';

        $this->em->expects($this->any())->method('find')
            ->with('article', 205)->willReturn($article);

        $this->helper->expects($this->at(0))->method('isHidden')
            ->willReturn(true);

        $content                    = new \Content();
        $content->content_status    = 1;
        $content->in_litter         = 0;
        $content->starttime         = '2020-01-01 00:00:00';
        $content->content_type_name = 'article';
        $content->target_id         = 205;
        $content->related_contents  = [ [
            'content_type_name' => 'article',
            'target_id'         => 205,
            'type'              => 'related_inner',
        ] ];

        $this->assertFalse(has_related_contents($content, 'mumble'));
        $this->assertFalse(has_related_contents($content, 'inner'));
        $this->assertTrue(has_related_contents($content, 'inner'));
    }

    /**
     * Tests has_summary.
     */
    public function testHasSummary()
    {
        $this->helper->expects($this->at(0))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse(has_summary($this->content));

        $this->content->summary = 'Percipit "mollis" at scriptorem usu.';
        $this->assertFalse(has_summary($this->content));
        $this->assertTrue(has_summary($this->content));
    }

    /**
     * Tests has_tags.
     */
    public function testHasTags()
    {
        $this->assertFalse(has_tags($this->content));

        $this->content->tags = [];
        $this->assertFalse(has_tags($this->content));

        $tags = [ new Tag([ 'id' => 917 ]), new Tag([ 'id' => 837 ]) ];

        $this->ts->expects($this->once())->method('getListByIds')
            ->with([ 971, 837 ])
            ->willReturn([ 'items' => $tags ]);

        $this->content->tags = [ 971, 837 ];
        $this->assertTrue(has_tags($this->content));
    }

    /**
     * Tests has_title.
     */
    public function testHasTitle()
    {
        $this->helper->expects($this->at(0))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse(has_title($this->content));

        $this->content->title = 'Percipit "mollis" at scriptorem usu.';
        $this->assertFalse(has_title($this->content));
        $this->assertTrue(has_title($this->content));
    }
}
