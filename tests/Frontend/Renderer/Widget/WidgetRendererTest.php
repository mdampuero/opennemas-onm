<?php

namespace Tests\Frontend\Renderer\Widget;

use Common\Model\Entity\Content;
use Frontend\Renderer\Widget\WidgetRenderer;
use PHPUnit\Framework\TestCase;

class WidgetRendererTest extends TestCase
{

    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->renderer = $this->getMockBuilder('Frontend\Renderer\Widget\WidgetRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getWidget' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Symfony\Component\Routing\Router')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->loader = $this->getMockBuilder('Common\Core\Component\Loader\WidgetLoader')
            ->disableOriginalConstructor()
            ->setMethods([ 'loadWidget' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.template':
                return $this->template;

            case 'core.loader.widget':
                return $this->loader;

            case 'router':
                return $this->router;
        }

        return null;
    }

    /**
     * Tests the render function.
     */
    public function testRender()
    {
        $this->router->expects($this->once())->method('generate')
            ->with('frontend_widget_render', [ 'widget_id' => 890, 'responsive' => 'mobile' ])
            ->willReturn('render/widget?widget_id=890&responsive=mobile');

        $this->assertEquals(
            '<esi:include src="render/widget?widget_id=890&responsive=mobile" />',
            $this->renderer->render(new Content([ 'pk_content' => 890 ]), [ 'responsive' => 'mobile' ])
        );
    }

    /**
     * Tests getWidget when empty content.
     */
    public function testgetWidgetWhenEmpty()
    {
        $widget        = new \Content();
        $widget->class = 'ContentListing';

        $renderer = new WidgetRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getWidget');
        $method->setAccessible(true);

        $this->assertEquals(null, $method->invokeArgs($renderer, [ $widget ]));
    }

    /**
     * Tests getWidget when class doesn't exists.
     */
    public function testgetWidgetWhenNoClass()
    {
        $widget        = new \Content();
        $widget->class = 'AllHeadlines';

        $renderer = new WidgetRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getWidget');
        $method->setAccessible(true);

        $this->loader->expects($this->at(0))->method('loadWidget')
            ->with($widget->class);

        $this->assertEquals(null, $method->invokeArgs($renderer, [ $widget ]));
    }
}
