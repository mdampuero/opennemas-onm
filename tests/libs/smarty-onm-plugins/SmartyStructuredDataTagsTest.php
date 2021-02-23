<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Libs\Smarty;

use Common\Model\Entity\Category;
use Common\Model\Entity\Content;

/**
 * Defines test cases for SmartyStructuredDataTagsTest class.
 */
class SmartyStructuredDataTagsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.structured_data_tags.php';

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', 'getValue', 'hasValue' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->smarty->expects($this->any())
            ->method('getContainer')
            ->willReturn($this->container);

        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->request->expects($this->any())
            ->method('getUri')
            ->willReturn('http://route/to/page');

        $this->structuredData = $this->getMockBuilder('Common\Core\Component\Helper\StructuredData')
            ->disableOriginalConstructor()
            ->setMethods([ 'generateJsonLDCode' ])
            ->getMock();

        $this->structuredData->expects($this->any())
            ->method('generateJsonLDCode')
            ->willReturn('Structured-Data');

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'request_stack':
                return $this->requestStack;

            case 'core.helper.structured_data':
                return $this->structuredData;
        }

        return null;
    }

    /**
     * Test smarty_function_structured_data_tags when no content provided nor frontpage
     */
    public function testStructuredDataWhenNoContentNorFrontpage()
    {
        $this->smarty->expects($this->at(0))->method('getValue')
            ->with('app')
            ->willReturn(null);

        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn(null);
        $this->assertEquals(
            '',
            smarty_function_structured_data_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_structured_data_tags when content
     * Uses generateJsonLDCode
     */
    public function testStructuredData()
    {
        $content  = new Content([ 'id' => 1 ]);
        $category = new Category([ 'id' => 3750 ]);

        $this->smarty->expects($this->at(0))->method('getValue')
            ->with('app')
            ->willReturn(['extension' => 'article']);
        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn($content);
        $this->smarty->expects($this->at(2))->method('getValue')
            ->with('o_category')
            ->willReturn($category);

        $this->assertEquals(
            'Structured-Data',
            smarty_function_structured_data_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_structured_data_tags when content album
     * Uses generateJsonLDCode
     */
    public function testStructuredDataWithAlbum()
    {
        $content  = new Content();
        $category = new Category([ 'id' => 3750 ]);

        $content->content_type_name = 'album';


        $this->smarty->expects($this->at(0))->method('getValue')
            ->with('app')
            ->willReturn(['extension' => 'article']);
        $this->smarty->expects($this->at(1))->method('getValue')
            ->with('content')
            ->willReturn($content);
        $this->smarty->expects($this->at(2))->method('getValue')
            ->with('o_category')
            ->willReturn($category);
        // $this->smarty->expects($this->at(3))->method('getValue')
        //     ->with('photos')
        //     ->willReturn([]);

        $this->assertEquals(
            'Structured-Data',
            smarty_function_structured_data_tags(null, $this->smarty)
        );
    }
}
