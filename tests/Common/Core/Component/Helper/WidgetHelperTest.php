<?php

namespace Common\Core\Component\Helper;

use Common\Core\Component\Helper\WidgetHelper;
use Common\Model\Entity\Content;

class WidgetHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->service = $this->getMockBuilder('Api\Service\V1\ContentService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getList' ])
            ->getMock();

        $this->oql = 'content_type_name = "widget" ' .
            'and in_litter = 0 and content_status = 1 ' .
            'and class = "InfiniteScroll"';

        $this->helper = new WidgetHelper($this->container);
    }

    /**
     * Tests the method widgetExists when the service throws an exception.
     */
    public function testWidgetExistsWhenException()
    {
        $this->container->expects($this->once())->method('get')
            ->with('api.service.content')
            ->willReturn($this->service);

        $this->service->expects($this->once())->method('getList')
            ->with($this->oql)
            ->will($this->throwException(new \Exception()));

        $this->assertFalse($this->helper->widgetExists('InfiniteScroll'));
    }

    /**
     * Tests the method widgetExists when success.
     */
    public function testWidgetExistsWhenSuccess()
    {
        $widget = new Content(
            [
                'content_type_name' => 'widget'
            ]
        );

        $this->container->expects($this->once())->method('get')
            ->with('api.service.content')
            ->willReturn($this->service);

        $this->service->expects($this->once())->method('getList')
            ->with($this->oql)
            ->willReturn([ 'items' => [ $widget ], 'total' => 1 ]);

        $this->assertTrue($this->helper->widgetExists('InfiniteScroll'));
    }
}
