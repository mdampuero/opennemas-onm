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

        $this->templateAdmin = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->view = $this->getMockBuilder('Common\Core\Component\Template\TemplateFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->view->expects($this->any())->method('get')
            ->with('backend')->willReturn($this->templateAdmin);

        $this->renderer = new HtmlRenderer($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'application.log':
                return $this->logger;

            case 'orm.manager':
                return $this->em;

            case 'entity_repository':
                return $this->em;

            case 'view':
                return $this->view;
        }

        return null;
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\HtmlRenderer::renderFia
     */
    public function testRenderFia()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';
        $ad->script  = '<script>foo bar baz</script>';

        $ad->params['sizes'] = [
            '0' => [
                'width' => 300,
                'height' => 300,
                'device' => 'phone'
            ],
        ];

        $params = [ 'op-ad-default' => true ];
        $output = '<figure class="op-ad op-ad-default">
            <iframe height="300" width="300">
                <script>foo bar baz</script>
            </iframe>
        </figure>';

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/fia/html.tpl', [
                'content' => $ad->script,
                'iframe'  => false,
                'width'   => 300,
                'height'  => 300,
                'default' => true,
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderFia($ad, $params)
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\HtmlRenderer::renderInline
     */
    public function testRenderInline()
    {
        $ad                  = new \Advertisement();
        $ad->params['sizes'] = [
            '0' => [
                'width' => 300,
                'height' => 600,
                'device' => 'desktop'
            ],
            '1' => [
                'width' => 300,
                'height' => 250,
                'device' => 'phone'
            ]
        ];

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\HtmlRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getHtml' ])
            ->getMock();

        $renderer->expects($this->once())->method('getHtml')
            ->willReturn('<script>foo bar baz</script>');


        $output = '<div class="ad-slot oat oat-visible oat-top " data-mark="Advertisement" '
            . 'style="height:600px;">'
            . '<script>foo bar baz</script></div>';

        $this->assertEquals($output, $renderer->renderInline($ad, []));
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\HtmlRenderer::renderInline
     */
    public function testRenderInlineWithFia()
    {
        $ad = new \Advertisement();

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\HtmlRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderFia' ])
            ->getMock();

        $renderer->expects($this->once())->method('renderFia')
            ->willReturn('foo');

        $this->assertEquals('foo', $renderer->renderInline($ad, [
            'ads_format' => 'fia'
        ]));
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
