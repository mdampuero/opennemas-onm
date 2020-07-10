<?php

namespace Tests\Common\Core\Functions;

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

        $this->em = $this->getMockBuilder('Repository\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'find' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('Common\Core\Component\Helper\SubscriptionHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'isHidden', 'isRestricted' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('Common\Api\Service\TagService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getListByIds' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

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

            default:
                return null;
        }
    }

    /**
     * Tests get_body.
     */
    public function testGetBody()
    {
        $this->assertEquals('His ridens eu sed quod ignota.', get_body(
            new Content([ 'body' => 'His ridens eu sed quod ignota.' ])
        ));

        $this->assertEquals(
            'Percipit "mollis" at scriptorem usu.',
            get_body(new Content([
                'body' => 'Percipit "mollis" at scriptorem usu.'
            ]))
        );

        $this->assertNull(get_body(new Content([ 'flob' => 'wibble' ])));
    }

    /**
     * Tests get_content when a content is already provided as parameter.
     */
    public function testGetContentFromParameter()
    {
        $content = new Content([ 'wobble' => 'wubble' ]);

        $this->assertNull(get_content());
        $this->assertEquals($content, get_content($content));
    }

    /**
     * Tests get_content when the content id is provided as parameter.
     */
    public function testGetContentFromParameterWhenId()
    {
        $content = new Content([ 'wobble' => 'wubble' ]);

        $this->em->expects($this->once())->method('find')
            ->with('Photo', 43)->willReturn($content);

        $this->assertEquals($content, get_content(43, 'Photo'));
    }

    /**
     * Tests get_content when the item is extracted from template and it is a
     * content.
     */
    public function testGetContentFromTemplateWhenContent()
    {
        $content = new Content([ 'quux' => 'grault' ]);

        $this->template->expects($this->once())->method('getValue')
            ->with('item')->willReturn($content);

        $this->assertEquals($content, get_content());
    }

    /**
     * Tests get_creation_date.
     */
    public function testGetCreationDate()
    {
        $this->assertEquals(new \Datetime('2010-10-10 10:00:00'), get_creation_date(
            new Content([
                'created' => '2010-10-10 10:00:00',
            ])
        ));
    }

    /**
     * Tests get_description.
     */
    public function testGetDescription()
    {
        $this->assertEquals('His ridens eu sed quod ignota.', get_description(
            new Content([ 'description' => 'His ridens eu sed quod ignota.' ])
        ));

        $this->assertEquals(
            'Percipit &quot;mollis&quot; at scriptorem usu.',
            get_description(new Content([
                'description' => 'Percipit "mollis" at scriptorem usu.'
            ]))
        );

        $this->assertNull(get_description(new Content([ 'flob' => 'wibble' ])));
    }

    /**
     * Tests get_featured_media.
     */
    public function testGetFeaturedMedia()
    {
        $photo   = new Content([ 'id' => 893 ]);
        $content = new Content([
            'content_type_name' => 'article',
            'img1' => 893
        ]);

        $this->em->expects($this->once())->method('find')
            ->with('Photo', 893)->willReturn($photo);

        $this->assertNull(get_featured_media($content, 'baz'));
        $this->assertNull(get_featured_media($content, 'inner'));
        $this->assertEquals($photo, get_featured_media($content, 'frontpage'));
    }

    /**
     * Tests get_featured_media.
     */
    public function testGetFeaturedMediaCaption()
    {
        $content = new Content([
            'content_type_name' => 'article',
            'img1_footer'       => 'Rhoncus pretium'
        ]);

        $this->assertNull(get_featured_media_caption($content, 'baz'));
        $this->assertNull(get_featured_media_caption($content, 'inner'));
        $this->assertEquals('Rhoncus pretium', get_featured_media_caption($content, 'frontpage'));
    }

    /**
     * Tests get_inner_title.
     */
    public function testGetInnerTitle()
    {
        $this->assertEquals('His ridens eu sed quod ignota.', get_title(
            new Content([ 'title' => 'His ridens eu sed quod ignota.' ])
        ));

        $this->assertEquals(
            'Percipit &quot;mollis&quot; at scriptorem usu.',
            get_inner_title(new Content([
                'title_int' => 'Percipit "mollis" at scriptorem usu.'
            ]))
        );

        $this->assertNull(get_inner_title(new Content([ 'flob' => 'wibble' ])));
    }

    /**
     * Tests get_pretitle.
     */
    public function testGetPretitle()
    {
        $this->assertEquals('His ridens eu sed quod ignota.', get_pretitle(
            new Content([ 'pretitle' => 'His ridens eu sed quod ignota.' ])
        ));

        $this->assertEquals(
            'Percipit &quot;mollis&quot; at scriptorem usu.',
            get_pretitle(new Content([
                'pretitle' => 'Percipit "mollis" at scriptorem usu.'
            ]))
        );

        $this->assertNull(get_pretitle(new Content([ 'flob' => 'wibble' ])));
    }

    /**
     * Tests get_type when a content is already provided as parameter.
     */
    public function testGetProperty()
    {
        $this->assertEquals('wubble', get_property(new Content([
            'wobble' => 'wubble'
        ]), 'wobble'));

        $this->assertEquals('bar', get_property(new Content([
            'wubble' => 'bar'
        ]), 'wubble'));
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
     * Tests get_publication_date.
     */
    public function testGetPublicationDate()
    {
        $this->assertEquals(new \Datetime('2010-10-10 10:00:00'), get_publication_date(
            new Content([
                'created' => '2010-10-10 10:00:00',
                'starttime' => null
            ])
        ));

        $this->assertEquals(
            new \Datetime('2010-10-10 20:00:00'),
            get_publication_date(new Content([
                'created'   => '2010-10-10 10:00:00',
                'starttime' => '2010-10-10 20:00:00'
            ]))
        );

        $date = new \Datetime();

        $this->assertEquals($date, get_publication_date(new Content([
            'starttime' => $date
        ])));
    }

    /**
     * Tests get_summary.
     */
    public function testGetSummary()
    {
        $this->assertEquals('His ridens eu sed quod ignota.', get_summary(
            new Content([ 'summary' => 'His ridens eu sed quod ignota.' ])
        ));

        $this->assertEquals(
            'Percipit &quot;mollis&quot; at scriptorem usu.',
            get_summary(new Content([
                'summary' => '<p><b>Percipit</b> "mollis" at scriptorem usu.</p>'
            ]))
        );

        $this->assertNull(get_summary(new Content([ 'flob' => 'wibble' ])));
    }

    /**
     * Tests get_tags.
     */
    public function testGetTags()
    {
        $this->assertEquals([], get_tags(new Content([])));
        $this->assertEquals([], get_tags(new Content([
            'tags' => []
        ])));

        $tags = [ new Tag([ 'id' => 917 ]), new Tag([ 'id' => 837 ]) ];

        $this->ts->expects($this->once())->method('getListByIds')
            ->with([ 971, 837 ])
            ->willReturn([ 'items' => $tags ]);

        $this->assertEquals($tags, get_tags(new Content([
            'tags' => [ 971, 837 ]
        ])));
    }

    /**
     * Tests get_title.
     */
    public function testGetTitle()
    {
        $this->assertEquals('His ridens eu sed quod ignota.', get_title(
            new Content([ 'title' => 'His ridens eu sed quod ignota.' ])
        ));

        $this->assertEquals(
            'Percipit &quot;mollis&quot; at scriptorem usu.',
            get_title(new Content([
                'title' => 'Percipit "mollis" at scriptorem usu.'
            ]))
        );

        $this->assertNull(get_title(new Content([ 'flob' => 'wibble' ])));
    }

    /**
     * Tests get_type.
     */
    public function testGetType()
    {
        $this->assertEquals('article', get_type(new Content([
            'content_type_name' => 'article'
        ])));

        $this->assertEquals('Static page', get_type(new Content([
            'content_type_name' => 'static_page'
        ]), true));

        $this->assertNull(get_type(new Content([ 'flob' => 'wibble' ]), true));
    }

    /**
     * Tests has_body.
     */
    public function testHasBody()
    {
        $this->helper->expects($this->at(1))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse(has_body(new Content([])));
        $this->assertFalse(has_body(new Content([
            'body' => 'Percipit "mollis" at scriptorem usu.'
        ])));
        $this->assertTrue(has_body(new Content([
            'body' => 'Percipit "mollis" at scriptorem usu.'
        ])));
    }

    /**
     * Tests has_description.
     */
    public function testHasDescription()
    {
        $this->assertFalse(has_description(new Content([])));
        $this->assertTrue(has_description(new Content([
            'description' => 'Percipit "mollis" at scriptorem usu.'
        ])));
    }

    /**
     * Tests has_featured_media.
     */
    public function testHasFeaturedMedia()
    {
        $photo   = new Content([ 'id' => 893 ]);
        $content = new Content([
            'content_type_name' => 'article',
            'img1' => 893
        ]);

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
        $content = new Content([
            'content_type_name' => 'article',
            'img1_footer'       => 'Rhoncus pretium'
        ]);

        $this->helper->expects($this->at(2))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse(has_featured_media_caption($content, 'baz'));
        $this->assertFalse(has_featured_media_caption($content, 'inner'));
        $this->assertFalse(has_featured_media_caption($content, 'frontpage'));
        $this->assertTrue(has_featured_media_caption($content, 'frontpage'));
    }

    /**
     * Tests has_inner_title.
     */
    public function testHasInnerTitle()
    {
        $this->helper->expects($this->at(1))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse(has_inner_title(new Content([])));
        $this->assertFalse(has_inner_title(new Content([
            'title_int' => 'Percipit "mollis" at scriptorem usu.'
        ])));
        $this->assertTrue(has_inner_title(new Content([
            'title_int' => 'Percipit "mollis" at scriptorem usu.'
        ])));
    }

    /**
     * Tests has_pretitle.
     */
    public function testHasPretitle()
    {
        $this->helper->expects($this->at(1))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse(has_pretitle(new Content([])));
        $this->assertFalse(has_pretitle(new Content([
            'pretitle' => 'Percipit "mollis" at scriptorem usu.'
        ])));
        $this->assertTrue(has_pretitle(new Content([
            'pretitle' => 'Percipit "mollis" at scriptorem usu.'
        ])));
    }

    /**
     * Tests has_summary.
     */
    public function testHasSummary()
    {
        $this->helper->expects($this->at(1))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse(has_summary(new Content([])));
        $this->assertFalse(has_summary(new Content([
            'summary' => 'Percipit "mollis" at scriptorem usu.'
        ])));
        $this->assertTrue(has_summary(new Content([
            'summary' => 'Percipit "mollis" at scriptorem usu.'
        ])));
    }

    /**
     * Tests has_tags.
     */
    public function testHasTags()
    {
        $this->assertFalse(has_tags(new Content([])));
        $this->assertFalse(has_tags(new Content([ 'tags' => [] ])));

        $tags = [ new Tag([ 'id' => 917 ]), new Tag([ 'id' => 837 ]) ];

        $this->ts->expects($this->once())->method('getListByIds')
            ->with([ 971, 837 ])
            ->willReturn([ 'items' => $tags ]);

        $this->assertTrue(has_tags(new Content([ 'tags' => [ 971, 837 ] ])));
    }

    /**
     * Tests has_title.
     */
    public function testHasTitle()
    {
        $this->helper->expects($this->at(1))->method('isHidden')
            ->willReturn(true);

        $this->assertFalse(has_title(new Content([])));
        $this->assertFalse(has_title(new Content([
            'title' => 'Percipit "mollis" at scriptorem usu.'
        ])));
        $this->assertTrue(has_title(new Content([
            'title' => 'Percipit "mollis" at scriptorem usu.'
        ])));
    }

    /**
     * Tests is_restricted when no content provided or empty content provided..
     */
    public function testIsRestrictedWheContent()
    {
        $item = new Content();

        $this->helper->expects($this->once())->method('isRestricted')
            ->willReturn(true);
        $this->helper->expects($this->once())->method('isHidden')
            ->with('title', $item)->willReturn(false);

        $this->assertTrue(is_restricted($item));
        $this->assertFalse(is_restricted($item, 'title'));
    }

    /**
     * Tests is_restricted when no content provided or empty content provided..
     */
    public function testIsRestrictedWheNoContent()
    {
        $this->template->expects($this->once())->method('getValue')
            ->with('item')->willReturn(null);

        $this->assertFalse(is_restricted());
    }
}
