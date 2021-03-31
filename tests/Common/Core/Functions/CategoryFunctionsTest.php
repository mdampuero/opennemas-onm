<?php

namespace Tests\Common\Core\Functions;

use Common\Model\Entity\Category;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for categories functions.
 */
class CategoryFunctionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->cs = $this->getMockBuilder('Api\Service\V1\CategoryService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->instance = new Instance([ 'internal_name' => 'foo' ]);

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

        $this->ugh = $this->getMockBuilder('Common\Component\Helper\UrlGenerator')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
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
            case 'api.service.category':
                return $this->cs;

            case 'core.instance':
                return $this->instance;

            case 'core.template.frontend':
                return $this->template;

            case 'core.helper.url_generator':
                return $this->ugh;

            default:
                return null;
        }
    }

    /**
     * Tests get_category when a category is already provided as parameter.
     */
    public function testGetCategoryFromParameterWhenCategory()
    {
        $category = new Category([ 'id' => 20 ]);

        $this->assertEquals($category, get_category($category));
    }

    /**
     * Tests get_category when a content is provided as parameter.
     */
    public function testGetCategoryFromParameterWhenContent()
    {
        $category = new Category([ 'id' => 20 ]);
        $content  = new \Content();

        $content->category_id = 20;

        $this->cs->expects($this->once())->method('getItem')
            ->with(20)->willReturn($category);

        $this->assertEquals($category, get_category($content));
    }

    /**
     * Tests get_category when an error is thrown while searching the category.
     */
    public function testGetCategoryFromParameterWhenError()
    {
        $content = new \Content();

        $content->category_id = 20;

        $this->cs->expects($this->once())->method('getItem')
            ->with(20)->will($this->throwException(new \Exception()));

        $this->assertNull(get_category($content));
    }

    /**
     * Tests get_category when no content is provided as parameter.
     */
    public function testGetCategoryFromParameterWhenNoContent()
    {
        $this->assertNull(get_category('corge'));
        $this->assertNull(get_category(709));
        $this->assertNull(get_category(null));
    }

    /**
     * Tests get_category when the item is extracted from template and it is a
     * content.
     */
    public function testGetCategoryFromTemplateWhenContent()
    {
        $category = new Category([ 'id' => 20 ]);
        $content  = new \Content();

        $content->category_id = 20;

        $this->template->expects($this->once())->method('getValue')
            ->with('item')->willReturn($content);

        $this->cs->expects($this->once())->method('getItem')
            ->with(20)->willReturn($category);

        $this->assertEquals($category, get_category());
    }

    /**
     * Tests get_category when the item is extracted from template and it is
     * not a content.
     */
    public function testGetCategoryFromTemplateWhenNoContent()
    {
        $this->template->expects($this->at(0))->method('getValue')
            ->with('item')->willReturn(445);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('item')->willReturn('thud');

        $this->assertNull(get_category());
        $this->assertNull(get_category());
    }

    /**
     * Tests get_category_color.
     */
    public function testGetCategoryColor()
    {
        $category = new Category([ 'color' => '#dc2127' ]);

        $this->assertNull(get_category_color(131));
        $this->assertEquals('#dc2127', get_category_color($category));
    }

    /**
     * Tests get_category_description.
     */
    public function testGetCategoryDescription()
    {
        $category = new Category([ 'description' => 'Consul risus commodo' ]);

        $this->assertNull(get_category_description(131));
        $this->assertEquals('Consul risus commodo', get_category_description($category));
    }

    /**
     * Tests get_category_id.
     */
    public function testGetCategoryId()
    {
        $category = new Category([ 'id' => 436 ]);

        $this->assertNull(get_category_id(131));
        $this->assertEquals(436, get_category_id($category));
    }

    /**
     * Tests get_category_logo.
     */
    public function testGetCategoryLogo()
    {
        $category = new Category([ 'logo_path' => 'images/bar/qux.jpg' ]);

        $this->assertNull(get_category_logo(131));
        $this->assertEquals(
            '/media/foo/images/bar/qux.jpg',
            get_category_logo($category)
        );
    }

    /**
     * Tests get_category_name.
     */
    public function testGetCategoryName()
    {
        $category = new Category([ 'title' => 'gorp' ]);

        $this->assertNull(get_category_name(131));
        $this->assertEquals('gorp', get_category_name($category));
    }

    /**
     * Tests get_category_slug.
     */
    public function testGetCategorySlug()
    {
        $category = new Category([ 'name' => 'thud' ]);

        $this->assertNull(get_category_slug(131));
        $this->assertEquals('thud', get_category_slug($category));
    }

    /**
     * Tests get_category_url.
     */
    public function testGetCategoryUrl()
    {
        $category = new Category([ 'title' => 'gorp' ]);

        $this->ugh->expects($this->once())->method('generate')
            ->with($category)->willReturn('/foo/glork');

        $this->assertNull(get_category_url(131));
        $this->assertEquals('/foo/glork', get_category_url($category));
    }

    /**
     * Tests has_category_description.
     */
    public function testHasCategoryDescription()
    {
        $category = new Category([ 'description' => 'Consul risus commodo' ]);

        $this->assertFalse(has_category_description(131));
        $this->assertTrue(has_category_description($category));
    }

    /**
     * Tests has_category_logo.
     */
    public function testHasCategoryLogo()
    {
        $category = new Category([ 'logo_path' => 'images/bar/qux.jpg' ]);

        $this->assertFalse(has_category_logo(131));
        $this->assertTrue(has_category_logo($category));
    }
}
