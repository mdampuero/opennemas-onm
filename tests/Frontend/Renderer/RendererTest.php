<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Frontend\Renderer;

use Common\Model\Entity\Content;
use Frontend\Renderer\Renderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Widget;

/**
 * Defines test cases for Renderer class.
 */
class RendererTest extends TestCase
{

    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->stats = $this->getMockBuilder('Frontend\Renderer\StatisticsRenderer')
            ->disableOriginalConstructor()
            ->setMethods([ 'render' ])
            ->getMock();

        $this->content = $this->getMockBuilder('Frontend\Renderer\Content\ContentRenderer')
            ->disableOriginalConstructor()
            ->setMethods([ 'render' ])
            ->getMock();

        $this->widget = $this->getMockBuilder('Frontend\Renderer\WidgetRenderer')
            ->disableOriginalConstructor()
            ->setMethods([ 'render' ])
            ->getMock();

        $this->stats->expects($this->any())->method('render')
            ->willReturn('StatisticsRenderer Code');

        $this->content->expects($this->any())->method('render')
            ->willReturn('ContentRenderer Code');

        $this->widget->expects($this->any())->method('render')
            ->willReturn('WidgetRenderer Code');

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->renderer = new Renderer($this->container);
    }


    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'frontend.renderer.statistics':
                return $this->stats;

            case 'frontend.renderer.content':
                return $this->content;

            case 'frontend.renderer.widget':
                return $this->widget;
        }

        return null;
    }

    /**
     * Tests the constructor of the class.
     */
    public function testConstructor()
    {
        $renderer = $this->getMockBuilder('Frontend\Renderer\Renderer')
            ->setConstructorArgs([ $this->container ])
            ->getMockForAbstractClass();

        $this->assertNotEmpty($renderer);
    }

    /**
     * Tests render when statistics.
     */
    public function testRenderWhenStatistics()
    {
        $params = [ 'types' => [ 'GAnalytics' ] ];

        $this->assertEquals('StatisticsRenderer Code', $this->renderer->render(null, $params));
    }

    /**
     * Tests render when content with specific renderer.
     */
    public function testRenderWhenSpecificContent()
    {
        $params  = [];
        $content = new Widget();

        $this->assertEquals('WidgetRenderer Code', $this->renderer->render($content, $params));
    }

    /**
     * Tests render when content with no specific renderer.
     */
    public function testRenderWhenNoSpecificContent()
    {
        $params  = [];
        $content = new Content();

        $this->container->expects($this->at(0))->method('get')
            ->will($this->throwException(new ServiceNotFoundException(1)));

        $this->assertEquals('ContentRenderer Code', $this->renderer->render($content, $params));
    }
}
