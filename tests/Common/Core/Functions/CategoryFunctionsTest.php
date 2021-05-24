<?php

namespace Tests\Common\Core\Functions;

use Common\Model\Entity\Category;
use Common\Model\Entity\Content;

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
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->content = new Content([
            'content_status' => 1,
            'in_litter'      => 0,
            'starttime'      => new \Datetime('2020-01-01 00:00:00'),
            'category_id'    => 20,
        ]);

        $this->category = new Category([
            'id'          => 1,
            'name'        => 'sports',
            'color'       => '#dc2127',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Fuga, voluptatum!',
            'logo_id'     => 123
        ]);

        $this->logo = new Content([
            'content_type_name' => 'photo',
            'content_status'    => 1,
            'in_litter'         => 0,
            'pk_content'        => 123
        ]);

        $this->helper = $this->getMockBuilder('Common\Core\Component\Helper\CategoryHelper')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getCategory',
                    'getCategoryColor',
                    'getCategoryDescription',
                    'getCategoryId',
                    'getCategoryLogo',
                    'getCategoryName',
                    'getCategorySlug',
                    'getCategoryUrl',
                    'hasCategoryDescription',
                    'hasCategoryLogo'
                ]
            )
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->with('core.helper.category')
            ->willReturn($this->helper);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Tests get_category.
     */
    public function testGetCategory()
    {
        $this->helper->expects($this->once())->method('getCategory')
            ->with($this->content)
            ->willReturn($this->category);

        $this->assertEquals($this->category, get_category($this->content));
    }

    /**
     * Tests get_category_color.
     */
    public function testGetCategoryColor()
    {
        $this->helper->expects($this->once())->method('getCategoryColor')
            ->with($this->content)
            ->willReturn($this->category->color);

        $this->assertEquals($this->category->color, get_category_color($this->content));
    }

    /**
     * Tests get_category_description.
     */
    public function testGetCategoryDescription()
    {
        $this->helper->expects($this->once())->method('getCategoryDescription')
            ->with($this->content)
            ->willReturn($this->category->description);

        $this->assertEquals($this->category->description, get_category_description($this->content));
    }

    /**
     * Tests get_category_id.
     */
    public function testGetCategoryId()
    {
        $this->helper->expects($this->once())->method('getCategoryId')
            ->with($this->content)
            ->willReturn($this->category->id);

        $this->assertEquals($this->category->id, get_category_id($this->content));
    }

    /**
     * Tests get_category_logo.
     */
    public function testGetCategoryLogo()
    {
        $this->helper->expects($this->once())->method('getCategoryLogo')
            ->with($this->category)
            ->willReturn($this->logo);

        $this->assertEquals($this->logo, get_category_logo($this->category));
    }

    /**
     * Tests get_category_name.
     */
    public function testGetCategoryName()
    {
        $this->helper->expects($this->once())->method('getCategoryName')
            ->with($this->content)
            ->willReturn($this->category->name);

        $this->assertEquals($this->category->name, get_category_name($this->content));
    }

    /**
     * Tests get_category_slug.
     */
    public function testGetCategorySlug()
    {
        $this->helper->expects($this->once())->method('getCategorySlug')
            ->with($this->content)
            ->willReturn($this->category->slug);

        $this->assertEquals($this->category->slug, get_category_slug($this->content));
    }

    /**
     * Tests get_category_url.
     */
    public function testGetCategoryUrl()
    {
        $this->helper->expects($this->once())->method('getCategoryUrl')
            ->with($this->content)
            ->willReturn('/blog/section/sports');

        $this->assertEquals('/blog/section/sports', get_category_url($this->content));
    }

    /**
     * Tests has_category_description.
     */
    public function testHasCategoryDescription()
    {
        $this->helper->expects($this->once())->method('hasCategoryDescription')
            ->with($this->content)
            ->willReturn(true);

        $this->assertEquals(true, has_category_description($this->content));
    }

    /**
     * Tests has_category_description.
     */
    public function testHasCategoryLogo()
    {
        $this->helper->expects($this->once())->method('hasCategoryLogo')
            ->with($this->content)
            ->willReturn(true);

        $this->assertEquals(true, has_category_logo($this->content));
    }
}
