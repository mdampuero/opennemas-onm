<?php

namespace Tests\Frontend\Renderer\Widget;

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
            ->setMethods([ 'renderletSmarty', 'renderletIntelligentWidget', 'getWidget' ])
            ->getMock();

        $this->loader = $this->getMockBuilder('Common\Core\Component\Loader\WidgetLoader')
            ->disableOriginalConstructor()
            ->setMethods([ 'loadWidget' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->renderer->expects($this->any())->method('renderletSmarty')
            ->willReturn('Smarty Renderlet Code');

        $this->renderer->expects($this->any())->method('renderletIntelligentWidget')
            ->willReturn('Intelligent Renderlet Code');

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
        }

        return null;
    }

    /**
     * Tests render when widget html.
     */
    public function testRenderWhenHtml()
    {
        $widget       = new \Widget();
        $widget->type = null;
        $widget->body = 'Html Renderlet Code';
        $expected     = '<div class="widget">' . $widget->body . '</div>';

        $this->assertEquals($expected, $this->renderer->render($widget, []));
    }

    /**
     * Tests render when widget smarty.
     */
    public function testRenderWhenSmarty()
    {
        $widget       = new \Widget();
        $widget->type = 'smarty';
        $expected     = '<div class="widget">Smarty Renderlet Code</div>';

        $this->assertEquals($expected, $this->renderer->render($widget, []));
    }

    /**
     * Tests render when intelligent widget.
     */
    public function testRenderWhenIntelligentWidget()
    {
        $widget       = new \Widget();
        $widget->type = 'intelligentwidget';
        $expected     = '<div class="widget">Intelligent Renderlet Code</div>';

        $this->assertEquals($expected, $this->renderer->render($widget, []));
    }

    /**
     * Tests render when no valid widget.
     */
    public function testRenderWhenNoValid()
    {
        $widget       = new \Widget();
        $widget->type = 'other';
        $expected     = '<div class="widget"></div>';

        $this->assertEquals($expected, $this->renderer->render($widget, []));
    }

    /**
     * Tests renderletSmarty.
     */
    public function testRenderletSmarty()
    {
        $widget       = new \Widget();
        $widget->body = "smarty/renderlet/widget.tpl";

        $renderer = new WidgetRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'renderletSmarty');
        $method->setAccessible(true);

        $this->template->expects($this->once())->method('fetch')
            ->with('string:' . $widget->body, [ 'widget' => $widget->body ])
            ->willReturn('Output');

        $this->assertEquals('Output', $method->invokeArgs($renderer, [ $widget ]));
    }

    /**
     * Tests renderletIntelligentWidget when widget doesn't exists.
     */
    public function testRenderletIntelligentWidgetWhenNotExists()
    {
        $content  = new \Content();
        $expected = sprintf(_('Widget %s not available'), $content->content);

        $renderer = new WidgetRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'renderletIntelligentWidget');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invokeArgs($renderer, [ $content ]));
    }

    /**
     * Tests renderletIntelligentWidget when widget exists.
     */
    public function testRenderletIntelligentWidgetWhenExists()
    {
        $widget = $this->getMockBuilder('\Widget')
            ->disableOriginalConstructor()
            ->setMethods([ 'render' ])
            ->getMock();

        $renderer = $this->getMockBuilder('Frontend\Renderer\Widget\WidgetRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getWidget' ])
            ->getMock();

        $method = new \ReflectionMethod($renderer, 'renderletIntelligentWidget');
        $method->setAccessible(true);

        $renderer->expects($this->once())->method('getWidget')
            ->willReturn($widget);

        $widget->expects($this->once())->method('render')
            ->with([]);

        $method->invokeArgs($renderer, [ null, [] ]);
    }

    /**
     * Tests getWidget when empty content.
     */
    public function testgetWidgetWhenEmpty()
    {
        $widget          = new \Widget();
        $widget->content = null;

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
        $widget       = new \Widget();
        $widget->body = 'AllHeadlines';

        $renderer = new WidgetRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getWidget');
        $method->setAccessible(true);

        $this->loader->expects($this->at(0))->method('loadWidget')
            ->with($widget->body);

        $this->assertEquals(null, $method->invokeArgs($renderer, [ $widget ]));
    }
}
