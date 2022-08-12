<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\CategoryHelper;
use Common\Model\Entity\Category;
use Common\Model\Entity\Content;

/**
 * Defines test cases for ContentMediaHelper class.
 */
class CategoryHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->content = new Content([
            'content_status' => 1,
            'in_litter'      => 0,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->service = $this->getMockBuilder('Api\Service\V1\CategoryService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->photoService = $this->getMockBuilder('Api\Service\V1\PhotoService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Common\Model\Entity\Instance')
            ->disableOriginalConstructor()
            ->setMethods([ 'getMediaShortPath' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

        $this->ugh = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->instance->expects($this->any())->method('getMediaShortPath')
            ->willReturn('/media/foo');

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->helper = new CategoryHelper($this->container, $this->instance, $this->template, $this->ugh);
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
                return $this->service;

            case 'api.service.photo':
                return $this->photoService;

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
    * Tests getCategory when a category is already provided as parameter.
    */
    public function testGetCategoryFromParameterWhenCategory()
    {
        $category = new Category([ 'id' => 20 ]);

        $this->assertEquals($category, $this->helper->getCategory($category));
    }

    /**
    * Tests getCategory when a content is provided as parameter.
    */
    public function testGetCategoryFromParameterWhenContent()
    {
        $category = new Category([ 'id' => 20 ]);
        $content  = new \Content();

        $content->category_id = 20;

        $this->service->expects($this->once())->method('getItem')
            ->with(20)->willReturn($category);

        $this->assertEquals($category, $this->helper->getCategory($content));
    }

    /**
    * Tests getCategory when an error is thrown while searching the category.
    */
    public function testGetCategoryFromParameterWhenError()
    {
        $content = new \Content();

        $content->category_id = 20;

        $this->service->expects($this->once())->method('getItem')
            ->with(20)->will($this->throwException(new \Exception()));

        $this->assertNull($this->helper->getCategory($content));
    }

    /**
    * Tests getCategory when no content is provided as parameter.
    */
    public function testGetCategoryFromParameterWhenNoContent()
    {
        $this->assertNull($this->helper->getCategory('corge'));
        $this->assertNull($this->helper->getCategory(709));
        $this->assertNull($this->helper->getCategory(null));
    }

    /**
    * Tests getCategory when the item is extracted from template and it is a
    * content.
    */
    public function testGetCategoryFromTemplateWhenContent()
    {
        $category = new Category([ 'id' => 20 ]);
        $content  = new \Content();

        $content->category_id = 20;

        $this->template->expects($this->once())->method('getValue')
            ->with('item')->willReturn($content);

        $this->service->expects($this->once())->method('getItem')
            ->with(20)->willReturn($category);

        $this->assertEquals($category, $this->helper->getCategory());
    }

    /**
    * Tests getCategory when the item is extracted from template and it is
    * not a content.
    */
    public function testGetCategoryFromTemplateWhenNoContent()
    {
        $this->template->expects($this->at(0))->method('getValue')
            ->with('item')->willReturn(445);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('item')->willReturn('thud');

        $this->assertNull($this->helper->getCategory());
        $this->assertNull($this->helper->getCategory());
    }

    /**
    * Tests getCategoryColor.
    */
    public function testGetCategoryColor()
    {
        $category = new Category([ 'color' => '#dc2127' ]);

        $this->assertNull($this->helper->getCategoryColor(131));
        $this->assertEquals('#dc2127', $this->helper->getCategoryColor($category));
    }

    /**
    * Tests getCategoryDescription.
    */
    public function testGetCategoryDescription()
    {
        $category = new Category([ 'description' => 'Consul risus commodo' ]);

        $this->assertNull($this->helper->getCategoryDescription(131));
        $this->assertEquals('Consul risus commodo', $this->helper->getCategoryDescription($category));
    }

    /**
    * Tests getCategoryId.
    */
    public function testGetCategoryId()
    {
        $category = new Category([ 'id' => 436 ]);

        $this->assertNull($this->helper->getCategoryId(131));
        $this->assertEquals(436, $this->helper->getCategoryId($category));
    }

    /**
    * Tests getCategoryLogo.
    */
    public function testGetCategoryLogo()
    {
        $category = new Category([ 'id' => 436, 'logo_id' => 123 ]);

        $photo             = new \Content();
        $photo->pk_content = 123;

        $this->photoService->expects($this->any())->method('getItem')
            ->willReturn($photo);

        $this->assertNull($this->helper->getCategoryLogo(131));
        $this->assertEquals(
            $photo,
            $this->helper->getCategoryLogo($category)
        );
    }

    /**
    * Tests getCategoryLogo when exception.
    */
    public function testGetCategoryLogoWhenException()
    {
        $category = new Category([ 'id' => 436, 'logo_id' => 123 ]);

        $this->photoService->expects($this->any())->method('getItem')
            ->with(123)
            ->will($this->throwException(new \Exception()));

        $this->assertNull($this->helper->getCategoryLogo($category));
    }

    /**
    * Tests getCategoryName.
    */
    public function testGetCategoryName()
    {
        $category = new Category([ 'title' => 'gorp' ]);

        $this->assertNull($this->helper->getCategoryName(131));
        $this->assertEquals('gorp', $this->helper->getCategoryName($category));
    }

    /**
    * Tests getCategorySlug.
    */
    public function testGetCategorySlug()
    {
        $category = new Category([ 'name' => 'thud' ]);

        $this->assertNull($this->helper->getCategorySlug(131));
        $this->assertEquals('thud', $this->helper->getCategorySlug($category));
    }

    /**
    * Tests getCategoryUrl.
    */
    public function testGetCategoryUrl()
    {
        $category = new Category([ 'title' => 'gorp' ]);

        $this->ugh->expects($this->once())->method('generate')
            ->with($category)->willReturn('/foo/glork');

        $this->assertNull($this->helper->getCategoryUrl(131));
        $this->assertEquals('/foo/glork', $this->helper->getCategoryUrl($category));
    }

    /**
    * Tests hasCategoryDescription.
    */
    public function testHasCategoryDescription()
    {
        $category = new Category([ 'description' => 'Consul risus commodo' ]);

        $this->assertFalse($this->helper->hasCategoryDescription(131));
        $this->assertTrue($this->helper->hasCategoryDescription($category));
    }

    /**
    * Tests hasCategoryLogo.
    */
    public function testHasCategoryLogo()
    {
        $category = new Category([ 'id' => 436, 'logo_id' => 123 ]);

        $photo             = new \Content();
        $photo->pk_content = 123;

        $this->photoService->expects($this->any())->method('getItem')
            ->willReturn($photo);

        $this->assertFalse($this->helper->hasCategoryLogo(131));
        $this->assertTrue($this->helper->hasCategoryLogo($category));
    }

    /**
    * Tests isManualCategory.
    */
    public function testIsManualCategory()
    {
        $category = new Category([ 'id' => 436, 'logo_id' => 123, 'params' => ['manual' => '1']]);

        $this->assertNull($this->helper->isManualCategory(131));
        $this->assertTrue($this->helper->isManualCategory($category));
    }
}
