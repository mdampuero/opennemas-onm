<?php

namespace Tests\Frontend\Renderer\Widget;

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

        $this->renderer->expects($this->any())->method('renderletSmarty')
            ->willReturn('Smarty Renderlet Code');

        $this->renderer->expects($this->any())->method('renderletIntelligentWidget')
            ->willReturn('Intelligent Renderlet Code');
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
}
