<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Frontend\Renderer\Advertisement;

use PHPUnit\Framework\TestCase;
use Frontend\Renderer\Advertisement\HtmlRenderer;

/**
 * Defines test cases for HtmlRenderer class.
 */
class HtmlRendererTest extends TestCase
{
    /**
     * @var HtmlRenderer
     */
    protected $renderer;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->container = $this->getMockForAbstractClass(
            'Symfony\Component\DependencyInjection\ContainerInterface'
        );

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet', 'find' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger')
            ->setMethods([ 'info', 'error' ])
            ->getMock();

        $this->templateAdmin = $this->getMockBuilder('TemplateAdmin')
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->renderer = new HtmlRenderer($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'application.log':
                return $this->logger;

            case 'core.template.admin':
                return $this->templateAdmin;

            case 'orm.manager':
                return $this->em;

            case 'entity_repository':
                return $this->em;
        }

        return null;
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\HtmlRenderer::renderInline
     */
    public function testRenderInline()
    {
        $ad = new \Advertisement();

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\HtmlRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getHtml' ])
            ->getMock();

        $renderer->expects($this->once())->method('getHtml')
            ->willReturn('<script>foo bar baz</script>');

        $this->assertEquals(
            '<script>foo bar baz</script>',
            $renderer->renderInline($ad, [])
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\HtmlRenderer::RenderSafeFrame
     */
    public function testRenderSafeFrame()
    {
        $ad     = new \Advertisement();
        $output = '<html><style>body { margin: 0; overflow: hidden;'
            . ' padding: 0; text-align: center; } img { max-width: 100% }'
            . '</style><body><div class="content"><script>foo bar baz</script>'
            . '</div></body>';

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\HtmlRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getHtml' ])
            ->getMock();

        $renderer->expects($this->once())->method('getHtml')
            ->willReturn('<script>foo bar baz</script>');

        $this->assertEquals(
            $output,
            $renderer->renderSafeFrame($ad, [])
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\HtmlRenderer::getHtml
     */
    public function testGetHtml()
    {
        $ad         = new \Advertisement();
        $ad->script = '<script>foo bar baz</script>';

        $method = new \ReflectionMethod($this->renderer, 'getHtml');
        $method->setAccessible(true);

        $this->assertEquals(
            $ad->script,
            $method->invokeArgs($this->renderer, [ $ad ])
        );

        $ad->script = '';
        $this->assertEmpty($method->invokeArgs($this->renderer, [ $ad ]));
    }
}
