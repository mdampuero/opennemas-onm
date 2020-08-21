<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Components\Functions;

use Common\Model\Entity\Content;

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

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
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
            case 'core.template.frontend':
                return $this->template;

            case 'entity_repository':
                return $this->em;

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
     * Tests get_featured_media.
     */
    public function testGetFeaturedMedia()
    {
        $photo   = new Content([
            'id'             => 893,
            'content_status' => 1,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->em->expects($this->once())->method('find')
            ->with('Photo', 893)->willReturn($photo);

        $this->assertNull(get_featured_media($this->content, 'baz'));
        $this->assertNull(get_featured_media($this->content, 'inner'));

        $this->content->content_type_name = 'article';
        $this->content->img1              = 893;

        $this->assertEquals($photo, get_featured_media($this->content, 'frontpage'));
    }

    /**
     * Tests get_featured_media.
     */
    public function testGetFeaturedMediaCaption()
    {
        $this->assertNull(get_featured_media_caption($this->content, 'baz'));
        $this->assertNull(get_featured_media_caption($this->content, 'inner'));

        $this->content->content_type_name = 'article';
        $this->content->img1_footer       = 'Rhoncus pretium';

        $this->assertEquals('Rhoncus pretium', get_featured_media_caption($this->content, 'frontpage'));
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
        $photo = new Content([
            'id'             => 893,
            'content_status' => 1,
            'in_litter'      => 0,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->content->content_type_name = 'article';
        $this->content->img1              = 893;

        $this->em->expects($this->once())->method('find')
            ->with('Photo', 893)->willReturn($photo);

        $this->assertFalse(has_featured_media($this->content, 'baz'));
        $this->assertFalse(has_featured_media($this->content, 'inner'));
        $this->assertTrue(has_featured_media($this->content, 'frontpage'));
    }

    /**
     * Tests has_featured_media_caption.
     */
    public function testHasFeaturedMediaCaption()
    {
        $this->content->content_type_name = 'article';
        $this->content->img1_footer       = 'Rhoncus pretium';

        $this->assertFalse(has_featured_media_caption($this->content, 'baz'));
        $this->assertFalse(has_featured_media_caption($this->content, 'inner'));
        $this->assertTrue(has_featured_media_caption($this->content, 'frontpage'));
    }

    /**
     * Tests has_pretitle.
     */
    public function testHasPretitle()
    {
        $this->assertFalse(has_pretitle($this->content));

        $this->content->pretitle = 'Percipit "mollis" at scriptorem usu.';
        $this->assertTrue(has_pretitle($this->content));
    }

    /**
     * Tests has_summary.
     */
    public function testHasSummary()
    {
        $this->assertFalse(has_summary($this->content));

        $this->content->summary = 'Percipit "mollis" at scriptorem usu.';
        $this->assertTrue(has_summary($this->content));
    }

    /**
     * Tests has_title.
     */
    public function testHasTitle()
    {
        $this->assertFalse(has_title($this->content));

        $this->content->title = 'Percipit "mollis" at scriptorem usu.';
        $this->assertTrue(has_title($this->content));
    }
}
