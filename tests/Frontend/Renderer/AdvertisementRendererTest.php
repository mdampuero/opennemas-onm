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

use PHPUnit\Framework\TestCase;
use Frontend\Renderer\AdvertisementRenderer;

/**
 * Defines test cases for AdvertisementRenderer class.
 */
class AdvertisementRendererTest extends TestCase
{
    /**
     * @var AdvertisementRenderer
     */
    protected $renderer;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet', 'find' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger')
            ->setMethods([ 'info', 'error' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->templateAdmin = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->view = $this->getMockBuilder('Common\Core\Component\Template\TemplateFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'getBaseUrl' ])
            ->getMock();

        $this->instance->expects($this->any())->method('getBaseUrl')
            ->willReturn('thud.opennemas.com');
        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);
        $this->view->expects($this->any())->method('get')
            ->with('backend')->willReturn($this->templateAdmin);

        $this->renderer = $this->getMockBuilder('Frontend\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getRendererClass' ])
            ->getMock();
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'application.log':
                return $this->logger;

            case 'error.log':
                return $this->logger;

            case 'core.instance':
                return $this->instance;

            case 'orm.manager':
                return $this->em;

            case 'entity_repository':
                return $this->em;

            case 'router':
                return $this->router;

            case 'view':
                return $this->view;
        }

        return null;
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::__construct
     */
    public function testConstruct()
    {
        $this->assertEquals($this->router, $this->renderer->router);
        $this->assertEquals($this->templateAdmin, $this->renderer->tpl);
        $this->assertEquals($this->instance, $this->renderer->instance);
        $this->assertEquals($this->ds, $this->renderer->ds);
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::getInlineFormats
     */
    public function testGetInlineFormats()
    {
        $this->assertEquals(
            [ 'amp', 'fia', 'newsletter' ],
            $this->renderer->getInlineFormats()
        );
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::getDeviceCSSClasses
     */
    public function testGetDeviceCSSClasses()
    {
        $ad         = new \Advertisement();
        $ad->params = [];

        $this->assertEquals('', $this->renderer->getDeviceCSSClasses($ad));

        $ad         = new \Advertisement();
        $ad->params = [
            'devices' => [
            ]
        ];

        $this->assertEquals('', $this->renderer->getDeviceCSSClasses($ad));

        $ad         = new \Advertisement();
        $ad->params = [
            'devices' => [
                'desktop' => 1,
                'tablet'  => 0,
                'phone'   => 1,
            ]
        ];

        $this->assertEquals('hidden-tablet', $this->renderer->getDeviceCSSClasses($ad));
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::getMark
     */
    public function testGetMark()
    {
        $ad         = new \Advertisement();
        $ad->params = [];

        $this->ds->expects($this->any())->method('get')
            ->with('ads_settings')
            ->willReturn([]);

        $this->assertEquals('Advertisement', $this->renderer->getMark($ad));

        $ad         = new \Advertisement();
        $ad->params = [ 'mark_text' => 'Custom ad mark'];
        $this->assertEquals('Custom ad mark', $this->renderer->getMark($ad));
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::getMark
     */
    public function testGetMarkWithCustomDefaultMark()
    {
        $ad         = new \Advertisement();
        $ad->params = [];

        $this->ds->expects($this->any())->method('get')
            ->with('ads_settings')
            ->willReturn([ 'default_mark' => 'Custom mark']);

        $this->assertEquals('Custom mark', $this->renderer->getMark($ad));
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::getRequestedAd
     */
    public function testGetRequestedAd()
    {
        $ad = new \Advertisement();

        $method = new \ReflectionMethod($this->renderer, 'setRequestedAd');
        $method->setAccessible(true);
        $method->invokeArgs($this->renderer, [ $ad ]);

        $this->assertEquals([ $ad ], $this->renderer->getRequestedAd());
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::setRequestedAd
     */
    public function testSetRequestedAd()
    {
        $ad       = new \Advertisement();
        $property = new \ReflectionProperty($this->renderer, 'requestedAd');
        $property->setAccessible(true);

        $method = new \ReflectionMethod($this->renderer, 'setRequestedAd');
        $method->setAccessible(true);
        $method->invokeArgs($this->renderer, [ $ad ]);

        $this->assertEquals([ $ad ], $property->getValue($this->renderer));
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::getSlot
     */
    public function testGetSlot()
    {
        $method = new \ReflectionMethod($this->renderer, 'getSlot');
        $method->setAccessible(true);

        $ad             = new \Advertisement();
        $ad->pk_content = 123;
        $ad->id         = 123;
        $ad->positions  = [ 37 ];
        $ad->params     = [ 'width' => 300, 'floating' => true ];

        $output = '<div class="ad-slot oat oat-visible oat-top "'
            . ' data-mark="Advertisement">foo</div>';

        $content = 'foo';

        $this->assertEquals(
            $output,
            $method->invokeArgs($this->renderer, [ $ad, $content ])
        );
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::render
     */
    public function testRenderWithSafeFrameMode()
    {
        $ad             = new \Advertisement();
        $ad->pk_content = 123;
        $ad->id         = 123;
        $ad->positions  = [ 37 ];
        $ad->params     = [ 'placeholder' => 'placeholder1_1' ];

        $returnValue = '<div class="ad-slot oat" data-id="123"'
            . ' data-type="37"></div>';

        $this->ds->expects($this->any())->method('get')
            ->with('ads_settings')
            ->willReturn([ 'safe_frame' => '1' ]);

        $this->assertEquals(
            $returnValue,
            $this->renderer->render($ad, $ad->params)
        );
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::render
     */
    public function testRenderWithInlineMode()
    {
        $ad              = new \Advertisement();
        $ad->pk_content  = 123;
        $ad->id          = 123;
        $ad->positions   = [ 37 ];
        $ad->params      = [ 'width' => 300, 'floating' => true ];
        $ad->with_script = 3;

        $this->ds->expects($this->any())->method('get')
            ->with('ads_settings')
            ->willReturn([ 'safe_frame' => 0 ]);

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\DfpRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderInline' ])
            ->getMock();

        $renderer->expects($this->any())->method('renderInline')
            ->willReturn('foo');

        $this->renderer->expects($this->once())->method('getRendererClass')
            ->with(3)
            ->willReturn($renderer);

        $this->assertEquals(
            'foo',
            $this->renderer->render($ad, $ad->params)
        );
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::renderInlineHeaders
     */
    public function testRenderInlineHeadersWithNoAds()
    {
        $this->assertEmpty($this->renderer->renderInlineHeaders([], []));
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::renderInlineHeaders
     */
    public function testRenderInlineHeaders()
    {
        $ad = new \Advertisement();

        $rendererDfp = $this->getMockBuilder('Frontend\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderDfpHeaders' ])
            ->getMock();

        $rendererDfp->expects($this->any())->method('renderDfpHeaders')
            ->willReturn('foo');

        $this->assertEquals('foo', $rendererDfp->renderInlineHeaders([ $ad ], []));

        $rendererRevive = $this->getMockBuilder('Frontend\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderReviveHeaders' ])
            ->getMock();

        $rendererRevive->expects($this->any())->method('renderReviveHeaders')
            ->willReturn('bar');

        $this->assertEquals('bar', $rendererRevive->renderInlineHeaders([ $ad ], []));

        $rendererSmart = $this->getMockBuilder('Frontend\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderSmartHeaders' ])
            ->getMock();

        $rendererSmart->expects($this->any())->method('renderSmartHeaders')
            ->willReturn('baz');

        $this->assertEquals('baz', $rendererSmart->renderInlineHeaders([ $ad ], []));
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::renderDfpHeaders
     */
    public function testRenderDfpHeaders()
    {
        $method = new \ReflectionMethod($this->renderer, 'renderDfpHeaders');
        $method->setAccessible(true);

        $ad              = new \Advertisement();
        $ad->params      = [ 'googledfp_unit_id' => 321 ];
        $ad->with_script = 3;

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\DfpRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderInlineHeader' ])
            ->getMock();

        $renderer->expects($this->any())->method('renderInlineHeader')
            ->willReturn('foo');

        $this->renderer->expects($this->once())->method('getRendererClass')
            ->with(3)
            ->willReturn($renderer);

        $this->assertEquals(
            'foo',
            $method->invokeArgs($this->renderer, [ [ $ad ], [] ])
        );
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::renderReviveHeaders
     */
    public function testRenderReviveHeaders()
    {
        $method = new \ReflectionMethod($this->renderer, 'renderReviveHeaders');
        $method->setAccessible(true);

        $ad              = new \Advertisement();
        $ad->params      = [ 'openx_zone_id' => 321 ];
        $ad->with_script = 2;

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\ReviveRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderInlineHeader' ])
            ->getMock();

        $renderer->expects($this->any())->method('renderInlineHeader')
            ->willReturn('foo');

        $this->renderer->expects($this->once())->method('getRendererClass')
            ->with(2)
            ->willReturn($renderer);

        $this->assertEquals(
            'foo',
            $method->invokeArgs($this->renderer, [ [ $ad ], [] ])
        );
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::renderSmartHeaders
     */
    public function testRenderSmartHeaders()
    {
        $method = new \ReflectionMethod($this->renderer, 'renderSmartHeaders');
        $method->setAccessible(true);

        $ad              = new \Advertisement();
        $ad->params      = [ 'smart_format_id' => 321 ];
        $ad->with_script = 4;

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\SmartRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderInlineHeader' ])
            ->getMock();

        $renderer->expects($this->any())->method('renderInlineHeader')
            ->willReturn('foo');

        $this->renderer->expects($this->once())->method('getRendererClass')
            ->with(4)
            ->willReturn($renderer);

        $this->assertEquals(
            'foo',
            $method->invokeArgs($this->renderer, [ [ $ad ], [] ])
        );
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::renderInlineInterstitial
     */
    public function testRenderInlineInterstitial()
    {
        $ad                  = new \Advertisement();
        $ad->positions       = [ 1, 2, 50 ];
        $ad->with_script     = 3;
        $ad->params['sizes'] = [
            '0' => [
                'width' => 980,
                'height' => 250,
                'device' => 'desktop'
            ],
            '1' => [
                'width' => 980,
                'height' => 250,
                'device' => 'tablet'
            ],
            '2' => [
                'width' => 320,
                'height' => 100,
                'device' => 'phone'
            ]
        ];

        $ad->params['device'] = [
            'phone' => 1,
            'desktop' => 1
        ];

        $output = '<div class="interstitial">'
            . '<div class="interstitial-wrapper" style="width: 980px;">'
                . '<div class="interstitial-header">'
                    . '<span class="interstitial-header-title">'
                        . 'Entering on the requested page'
                    . '</span>'
                    . '<a class="interstitial-close-button" href="#" title="'
                        . 'Skip advertisement' . '">'
                        . '<span>' . 'Skip advertisement' . '</span>'
                    . '</a>'
                . '</div>'
                . '<div class="interstitial-content" style="height: 250px;">'
                    . '<div class="ad-slot oat oat-visible oat-top" data-id=""'
                        . ' data-timeout="5" data-type="1,2,50">foo</div>'
                . '</div>'
            . '</div>'
        . '</div>';

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\DfpRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderInline' ])
            ->getMock();

        $renderer->expects($this->any())->method('renderInline')
            ->willReturn('foo');

        $this->renderer->expects($this->once())->method('getRendererClass')
            ->with(3)
            ->willReturn($renderer);

        $this->templateAdmin->expects($this->once())->method('fetch')
            ->with('advertisement/helpers/inline/interstitial.tpl', [
                'size'        => $ad->params['sizes']['0'],
                'orientation' => 'top',
                'ad'          => $ad,
                'content'     => 'foo',
            ])
            ->willReturn($output);


        $this->assertEquals(
            $output,
            $this->renderer->renderInlineInterstitial([ $ad ], [])
        );
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::renderInlineInterstitial
     */
    public function testRenderInlineInterstitialWithEmptySizes()
    {
        $ad                   = new \Advertisement();
        $ad->positions        = [ 1, 2, 50 ];
        $ad->params['sizes']  = [];
        $ad->params['device'] = [ 'phone' => 1, 'desktop' => 1 ];

        $this->assertEmpty(
            $this->renderer->renderInlineInterstitial([ $ad ], [])
        );
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::renderInlineInterstitial
     */
    public function testRenderInlineInterstitialWithNoInterstitials()
    {
        $ads = [ ];

        $returnValue = '';
        $this->assertEquals(
            $returnValue,
            $this->renderer->renderInlineInterstitial($ads, [])
        );

        $ad1            = new \Advertisement();
        $ad1->positions = [ 1, 2 ];
        $ads            = [ $ad1 ];

        $returnValue = '';
        $this->assertEquals(
            $returnValue,
            $this->renderer->renderInlineInterstitial($ads, [])
        );
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::getDeviceAdvertisementSize
     */
    public function testGetDeviceAdvertisementSize()
    {
        $method = new \ReflectionMethod($this->renderer, 'getDeviceAdvertisementSize');
        $method->setAccessible(true);

        $ad                  = new \Advertisement();
        $ad->params['sizes'] = [
            '0' => [
                'width' => 980,
                'height' => 250,
                'device' => 'desktop'
            ],
            '1' => [
                'width' => 980,
                'height' => 250,
                'device' => 'tablet'
            ],
            '2' => [
                'width' => 320,
                'height' => 100,
                'device' => 'phone'
            ]
        ];

        $this->assertEquals(
            [ 'width' => 320, 'height' => 100, 'device' => 'phone' ],
            $method->invokeArgs($this->renderer, [ $ad, 'phone' ])
        );
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::renderSafeFrameSlot
     */
    public function testRenderSafeFrameSlot()
    {
        $method = new \ReflectionMethod($this->renderer, 'renderSafeFrameSlot');
        $method->setAccessible(true);

        $ad     = new \Advertisement();
        $ad->id = 123;

        $returnValue = '<div class="ad-slot oat" data-id="123" data-type="37"></div>';
        $this->assertEquals(
            $returnValue,
            $method->invokeArgs($this->renderer, [ $ad ])
        );
    }

    /**
     * @covers \Frontend\Renderer\AdvertisementRenderer::getRendererClass
     */
    public function testGetRendererClass()
    {
        $renderer = new AdvertisementRenderer($this->container);

        $method = new \ReflectionMethod($renderer, 'getRendererClass');
        $method->setAccessible(true);

        $ad              = new \Advertisement();
        $ad->with_script = 3;

        $types     = [ 'Image', 'Html', 'Revive', 'Dfp', 'Smart' ];
        $class     = $types[$ad->with_script] . 'Renderer';
        $classPath = 'Frontend\Renderer\Advertisement\\' . $class;

        $returnValue = new $classPath($this->container);
        $this->assertEquals(
            $returnValue,
            $method->invokeArgs($renderer, [ $ad->with_script ])
        );
    }

    /**
     * Tests isFloating when is a floating advertisement.
     */
    public function testIsFloatingWhenTrue()
    {
        $renderer = new AdvertisementRenderer($this->container);
        $params   = [ 'placeholder' => 'placeholder_1_1' ];

        $method = new \ReflectionMethod($renderer, 'isFloating');
        $method->setAccessible(true);

        $this->assertTrue(
            $method->invokeArgs($renderer, [ &$params ])
        );
    }

    /**
     * Tests isFloating when is a floating advertisement.
     */
    public function testIsFloatingWhenFalse()
    {
        $renderer = new AdvertisementRenderer($this->container);
        $params   = [];

        $method = new \ReflectionMethod($renderer, 'isFloating');
        $method->setAccessible(true);

        $this->assertFalse(
            $method->invokeArgs($renderer, [ &$params ])
        );
    }
}
