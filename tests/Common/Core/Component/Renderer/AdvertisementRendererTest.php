<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Renderer;

use PHPUnit\Framework\TestCase;
use Common\Core\Component\Renderer\AdvertisementRenderer;

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

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->sm = $this->getMockBuilder('SettingsManager')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->templateAdmin = $this->getMockBuilder('TemplateAdmin')
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger')
            ->setMethods([ 'info' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->renderer = new AdvertisementRenderer($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'router':
                return $this->router;

            case 'setting_repository':
                return $this->sm;

            case 'core.template.admin':
                return $this->templateAdmin;

            case 'application.log':
                return $this->logger;
        }
    }

    /**
     * @covers Common\Core\Component\Renderer\AdvertisementRenderer::__construct
     */
    public function testConstruct()
    {
        $this->assertEquals($this->router, $this->renderer->router);
        $this->assertEquals($this->sm, $this->renderer->sm);
        $this->assertEquals($this->templateAdmin, $this->renderer->tpl);
    }

    /**
     * @covers Common\Core\Component\Renderer\AdvertisementRenderer::getDeviceCSSClasses
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
     * @covers Common\Core\Component\Renderer\AdvertisementRenderer::getMark
     */
    public function testGetMark()
    {
        $ad         = new \Advertisement();
        $ad->params = [];

        $this->sm->expects($this->any())->method('get')
            ->with('ads_settings')
            ->willReturn([]);

        $this->assertEquals('Advertisement', $this->renderer->getMark($ad));

        $ad         = new \Advertisement();
        $ad->params = [ 'mark_text' => 'Custom ad mark'];
        $this->assertEquals('Custom ad mark', $this->renderer->getMark($ad));
    }

    /**
     * @covers Common\Core\Component\Renderer\AdvertisementRenderer::getMark
     */
    public function testGetMarkWithCustomDefaultMark()
    {
        $ad         = new \Advertisement();
        $ad->params = [];

        $this->sm->expects($this->any())->method('get')
            ->with('ads_settings')
            ->willReturn([ 'default_mark' => 'Custom mark']);

        $this->assertEquals('Custom mark', $this->renderer->getMark($ad));
    }

    /**
     * @covers Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineReviveSlot
     */
    public function testRenderInlineReviveSlot()
    {
        $ad             = new \Advertisement();
        $ad->pk_content = $ad->id = 123;
        $ad->positions  = [ 50 ];
        $ad->params     = [];

        $url         = '/ads/get/123';
        $returnValue = '<iframe src="' . $url . '"></iframe>
  <script type="text/javascript" data-id="{$id}">
    <!--// <![CDATA[
      OA_show(\'zone_' . $ad->pk_content . '\');
    // ]]> -->
  </script>
{/if}';

        $this->router->expects($this->any())->method('generate')
            ->with('frontend_ad_show', [ 'id' => $ad->pk_content ])
            ->willReturn($url);

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/revive.slot.tpl', [
                'id'     => $ad->pk_content,
                'iframe' => false,
                'url'    => $url,
            ])
            ->willReturn($returnValue);

        $this->assertEquals($returnValue, $this->renderer->renderInlineReviveSlot($ad));
    }

    /**
     * @covers Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineInterstitial
     */
    public function testRenderInlineInterstitial()
    {
        $ad1                  = new \Advertisement();
        $ad1->positions       = [ 1, 2, 50];
        $ad1->params['sizes'] = [
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
        $ad1->params['device'] = [ 'phone' => 1, 'desktop' => 1 ];

        $ads = [ $ad1 ];

        $returnValue = '<div class="interstitial"><div class="interstitial-wrapper" '
            . 'style="width: 980px;"><div class="interstitial-header"><span '
            . 'class="interstitial-header-title">Entering on the requested page</span>'
            . '<a class="interstitial-close-button" href="#" title="Skip advertisement">'
            . '<span>Skip advertisement</span></a></div><div '
            . 'class="interstitial-content" style="height:'
            . ' 250px;"><div class="ad-slot oat oat-visible oat-top" data-id="" data-timeout="5"'
            . ' data-type="1,2,50"></div></div></div></div>';

        $this->assertEquals($returnValue, $this->renderer->renderInlineInterstitial($ads));
    }


    /**
     * @covers Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineInterstitial
     */
    public function testRenderInlineInterstitialWithNoInterstitials()
    {
        $ads = [ ];

        $returnValue = '';

        $this->assertEquals($returnValue, $this->renderer->renderInlineInterstitial($ads));

        $ad1            = new \Advertisement();
        $ad1->positions = [ 1, 2];
        $ads            = [ $ad1 ];

        $returnValue = '';

        $this->assertEquals($returnValue, $this->renderer->renderInlineInterstitial($ads));
    }

    /**
     * @covers Common\Core\Component\Renderer\AdvertisementRenderer::renderSafeFrameSlot
     * @todo   Implement testRenderSafeFrameSlot().
     */
    public function testRenderSafeFrameSlot()
    {
        $ad            = new \Advertisement();
        $ad->id        = 1;
        $ad->positions = 1;
        $params        = [];

        $returnValue = '<div class="ad-slot oat" data-type="1"></div>';

        $this->assertEquals($returnValue, $this->renderer->renderSafeFrameSlot($ad, $params));

        $params = [ 'floating' => true ];

        $returnValue = '<div class="ad-slot oat" data-id=""  data-type="37"></div>';

        $this->assertEquals($returnValue, $this->renderer->renderSafeFrameSlot($ad, $params));


        $params = [ 'width' => 620 ];

        $returnValue = '<div class="ad-slot oat" data-type="1" data-width="620"></div>';

        $this->assertEquals($returnValue, $this->renderer->renderSafeFrameSlot($ad, $params));
    }
}
