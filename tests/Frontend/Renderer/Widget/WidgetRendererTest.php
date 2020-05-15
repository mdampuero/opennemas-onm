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
            ->setMethods([ 'renderletSmarty', 'renderletIntelligentWidget', 'factoryWidget' ])
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
        $widget            = new \Widget();
        $widget->renderlet = 'html';
        $widget->content   = 'Html Renderlet Code';
        $expected          = '<div class="widget">' . $widget->content . '</div>';

        $this->assertEquals($expected, $this->renderer->render($widget, []));
    }

    /**
     * Tests render when widget smarty.
     */
    public function testRenderWhenSmarty()
    {
        $widget            = new \Widget();
        $widget->renderlet = 'smarty';
        $expected          = '<div class="widget">Smarty Renderlet Code</div>';

        $this->assertEquals($expected, $this->renderer->render($widget, []));
    }

    /**
     * Tests render when intelligent widget.
     */
    public function testRenderWhenIntelligentWidget()
    {
        $widget            = new \Widget();
        $widget->renderlet = 'intelligentwidget';
        $expected          = '<div class="widget">Intelligent Renderlet Code</div>';

        $this->assertEquals($expected, $this->renderer->render($widget, []));
    }

    /**
     * Tests render when no valid widget.
     */
    public function testRenderWhenNoValid()
    {
        $widget            = new \Widget();
        $widget->renderlet = '';
        $expected          = '<div class="widget"></div>';

        $this->assertEquals($expected, $this->renderer->render($widget, []));
    }

    /**
     * Tests renderletSmarty.
     */
    public function testRenderletSmarty()
    {
        $widget          = new \Widget();
        $widget->content = "smarty/renderlet/widget.tpl";

        $renderer = new WidgetRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'renderletSmarty');
        $method->setAccessible(true);

        $this->template->expects($this->once())->method('fetch')
            ->with('string:' . $widget->content, [ 'widget' => $widget ])
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
     * Tests factoryWidget when empty content.
     */
    public function testFactoryWidgetWhenEmpty()
    {
        $widget          = new \Widget();
        $widget->content = null;

        $renderer = new WidgetRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'factoryWidget');
        $method->setAccessible(true);

        $this->assertEquals(null, $method->invokeArgs($renderer, [ $widget ]));
    }

    /**
     * Tests factoryWidget when class doesn't exists.
     */
    public function testFactoryWidgetWhenNoClass()
    {
        $widget          = new \Widget();
        $widget->content = 'AllHeadlines';

        $renderer = new WidgetRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'factoryWidget');
        $method->setAccessible(true);

        $this->loader->expects($this->at(0))->method('loadWidget')
            ->with($widget->content);

        $this->assertEquals(null, $method->invokeArgs($renderer, [ $widget ]));
    }
}
