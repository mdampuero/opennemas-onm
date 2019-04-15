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

        $this->templateAdmin = $this->getMockBuilder('TemplateAdmin')
            ->setMethods([ 'fetch' ])
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

        $this->renderer = new AdvertisementRenderer($this->container);
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

            case 'core.template.admin':
                return $this->templateAdmin;

            case 'orm.manager':
                return $this->em;

            case 'entity_repository':
                return $this->em;

            case 'router':
                return $this->router;
        }

        return null;
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::__construct
     */
    public function testConstruct()
    {
        $this->assertEquals($this->router, $this->renderer->router);
        $this->assertEquals($this->ds, $this->renderer->ds);
        $this->assertEquals($this->templateAdmin, $this->renderer->tpl);
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::getDeviceCSSClasses
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
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::getMark
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
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::getMark
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
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInline
     */
    public function testRenderInlineWithImage()
    {
        $ad      = new \Advertisement();
        $ad->img = '123';

        $renderer = $this->getMockBuilder('Common\Core\Component\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderInlineImage', 'getImage' ])
            ->getMock();

        $renderer->expects($this->once())->method('getImage')
            ->willReturn(new \Photo());

        $renderer->expects($this->once())->method('renderInlineImage')
            ->willReturn('foo');

        $this->assertEquals(
            'foo',
            $renderer->renderInline($ad)
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInline
     */
    public function testRenderInlineWithEmptyImage()
    {
        $ad = new \Advertisement();

        $this->assertEmpty($this->renderer->renderInline($ad));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInline
     */
    public function testRenderInlineWithHtml()
    {
        $ad = new \Advertisement();

        $renderer = $this->getMockBuilder('Common\Core\Component\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getHtml' ])
            ->getMock();

        $renderer->expects($this->once())->method('getHtml')
            ->willReturn('foo');

        $ad->with_script = 1;
        $this->assertEquals('foo', $renderer->renderInline($ad));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInline
     */
    public function testRenderInlineWithRevive()
    {
        $ad = new \Advertisement();

        $renderer = $this->getMockBuilder('Common\Core\Component\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderInlineReviveSlot' ])
            ->getMock();

        $renderer->expects($this->once())->method('renderInlineReviveSlot')
            ->willReturn('foo');

        $ad->with_script = 2;
        $this->assertEquals('foo', $renderer->renderInline($ad));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInline
     */
    public function testRenderInlineWithDFP()
    {
        $ad = new \Advertisement();

        $renderer = $this->getMockBuilder('Common\Core\Component\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderInlineDFPSlot' ])
            ->getMock();

        $renderer->expects($this->once())->method('renderInlineDFPSlot')
            ->willReturn('foo');

        $ad->with_script = 3;
        $this->assertEquals('foo', $renderer->renderInline($ad));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInline
     */
    public function testRenderInlineWithSmart()
    {
        $ad     = new \Advertisement();
        $params = [];

        $renderer = $this->getMockBuilder('Common\Core\Component\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderInlineSmartSlot' ])
            ->getMock();

        $renderer->expects($this->once())->method('renderInlineSmartSlot')
            ->willReturn('foo');

        $ad->with_script = 4;
        $this->assertEquals('foo', $renderer->renderInline($ad, $params));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::render
     */
    public function testRenderWithSafeFrameMode()
    {
        $ad             = new \Advertisement();
        $ad->pk_content = 123;
        $ad->id         = 123;
        $ad->positions  = [ 37 ];
        $ad->params     = [ 'width' => 300, 'floating' => true ];

        $returnValue = '<div class="ad-slot oat" data-id="123"'
            . ' data-type="37" data-width="300"></div>';

        $this->ds->expects($this->any())->method('get')
            ->with('ads_settings')
            ->willReturn([ 'safe_frame' => '1' ]);

        $this->assertEquals(
            $returnValue,
            $this->renderer->render($ad, $ad->params)
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::render
     */
    public function testRenderWithInlineMode()
    {
        $ad             = new \Advertisement();
        $ad->pk_content = 123;
        $ad->id         = 123;
        $ad->positions  = [ 37 ];
        $ad->params     = [ 'width' => 300, 'floating' => true ];

        $returnValue = '<div class="ad-slot oat oat-visible oat-top "'
            . ' data-mark="Advertisement"></div>';

        $this->ds->expects($this->any())->method('get')
            ->with('ads_settings')
            ->willReturn([ 'safe_frame' => 0 ]);

        $this->assertEquals(
            $returnValue,
            $this->renderer->render($ad, $ad->params)
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineDFPHeader
     */
    public function testRenderInlineDFPHeaderWithNoAd()
    {
        $ads = [];
        $this->assertEquals('', $this->renderer->renderInlineDFPHeader($ads, []));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineDFPHeader
     */
    public function testRenderInlineDFPHeaderWithNoAdAfterFilter()
    {
        $ad     = new \Advertisement();
        $ad->id = 123;

        $ads = [ $ad ];
        $this->assertEquals('', $this->renderer->renderInlineDFPHeader($ads, []));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineDFPHeader
     */
    public function testRenderInlineDFPHeader()
    {
        $ad                   = new \Advertisement();
        $ad->id               = 1;
        $ad->created          = '2019-03-28 18:40:32';
        $ad->params           = [ 'googledfp_unit_id' => 321 ];
        $ad->with_script      = 3;
        $ad->params['device'] = [ 'phone' => 1, 'desktop' => 1 ];
        $ad->params['sizes']  = [
            '0' => [
                'width' => 300,
                'height' => 300,
                'device' => 'desktop'
            ],
            '1' => [
                'width' => 1,
                'height' => 1,
                'device' => 'tablet'
            ]
        ];

        $content     = new \stdClass();
        $content->id = 123;

        $params = [
            'category'  => '',
            'extension' => '',
            'content'   => $content
        ];

        $zones[] = [
            'id'    => 1,
            'dfpId' => 321,
            'sizes' => '[ [ 300, 300 ], [ 1, 1 ] ]'
        ];

        $output = '<script async="async" src="https://www.googletagservices.com/tag/js/gpt.js"></script>
            <script>
            var googletag = googletag || {};
            googletag.cmd = googletag.cmd || [];
            </script>
            <script>
            googletag.cmd.push(function() {
                googletag.defineSlot(321, [ [300, 300], [1, 1] ], \'zone_1\').addService(googletag.pubads());

                googletag.pubads().enableSingleRequest();
                googletag.pubads().collapseEmptyDivs();
                googletag.enableServices();
            });
            </script>';

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/dfp.header.tpl', [
                'category'      => $params['category'],
                'extension'     => $params['extension'],
                'customCode'    => '',
                'options'       => null,
                'targetingCode' => '',
                'zones'         => $zones
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderInlineDFPHeader([ $ad ], $params)
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineDFPSlot
     */
    public function testRenderInlineDFPSlot()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $output = '<div id="zone_1">
            <script>
            googletag.cmd.push(function() { googletag.display(\'zone_1\'); });
            </script>
        </div>';

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/dfp.slot.tpl', [
                'id' => 1,
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderInlineDFPSlot($ad)
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineImage
     */
    public function testRenderInlineImage()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $photo                = new \Photo();
        $photo->category_name = 'foo';
        $photo->width         = 300;
        $photo->height        = 300;
        $photo->path_file     = '/path/';
        $photo->name          = 'foo.png';
        $photo->url           = '/ads/get/123';

        $output = '<a target="_blank" href="/ads/get/123" rel="nofollow">
            <img src="thud.opennemas.com/media/opennemas/images/path/foo.png" width="300" height="300" />
        </a>';

        $this->router->expects($this->any())->method('generate')
            ->with('frontend_ad_redirect', [ 'id' => '20190328184032000000' ])
            ->willReturn('/ads/get/123');

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/image.tpl', [
                'mediaUrl' => '/path/',
                'width'    => 300,
                'height'   => 300,
                'src'      => 'thud.opennemas.com/media/opennemas/images/path/foo.png',
                'url'      => 'thud.opennemas.com/ads/get/123'
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderInlineImage($ad, $photo)
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineImage
     */
    public function testRenderInlineImageWithFlash()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $photo                = new \Photo();
        $photo->category_name = 'foo';
        $photo->width         = 300;
        $photo->height        = 300;
        $photo->path_file     = '/path/';
        $photo->name          = 'foo.png';
        $photo->url           = '/ads/get/123';
        $photo->type_img      = 'swf';

        $output = '<object width="300" height="300">
            <param name="wmode" value="transparent" />
            <param name="movie" value="/ads/get/123" />
            <param name="width" value="300" />
            <param name="height" value="300" />
            <embed src="thud.opennemas.com/media/opennemas/images/path/foo.png"'
            . ' width="300" height="300" SCALE="exactfit" wmode="transparent"></embed>
        </object>';

        $this->router->expects($this->any())->method('generate')
            ->with('frontend_ad_redirect', [ 'id' => '20190328184032000000' ])
            ->willReturn('/ads/get/123');

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/flash.tpl', [
                'mediaUrl' => '/path/',
                'width'    => 300,
                'height'   => 300,
                'src'      => 'thud.opennemas.com/media/opennemas/images/path/foo.png',
                'url'      => 'thud.opennemas.com/ads/get/123'
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderInlineImage($ad, $photo)
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineImage
     */
    public function testRenderInlineImageWithAmp()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $photo                = new \Photo();
        $photo->category_name = 'foo';
        $photo->width         = 300;
        $photo->height        = 300;
        $photo->path_file     = '/path/';
        $photo->name          = 'foo.png';
        $photo->url           = '/ads/get/123';

        $output = '<a target="_blank" href="/ads/get/123" rel="nofollow">
            <amp-img
            src="thud.opennemas.com/media/opennemas/images/path/foo.png"
            width="300"
            height="300">
            </amp-img>
        </a>';

        $this->router->expects($this->any())->method('generate')
            ->with('frontend_ad_redirect', [ 'id' => '20190328184032000000' ])
            ->willReturn('/ads/get/123');

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/image.amp.tpl', [
                'mediaUrl' => '/path/',
                'width'    => 300,
                'height'   => 300,
                'src'      => 'thud.opennemas.com/media/opennemas/images/path/foo.png',
                'url'      => 'thud.opennemas.com/ads/get/123'
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderInlineImage($ad, $photo, 'amp')
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineReviveHeader
     */
    public function testRenderInlineReviveHeaderWithNoAd()
    {
        $ads = [];
        $this->assertEquals('', $this->renderer->renderInlineReviveHeader($ads));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineReviveHeader
     */
    public function testRenderInlineReviveHeaderWithNoAdAfterFilter()
    {
        $ad     = new \Advertisement();
        $ad->id = 123;

        $ads = [ $ad ];
        $this->assertEquals('', $this->renderer->renderInlineReviveHeader($ads));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineReviveHeader
     */
    public function testRenderInlineReviveHeader()
    {
        $ad              = new \Advertisement();
        $ad->id          = 123;
        $ad->positions   = [ 50 ];
        $ad->with_script = 2;
        $ad->params      = [ 'openx_zone_id' => 123456 ];

        $output = '<script>
            <!--// <![CDATA[
            var OA_zones = {
                \'zone_123\' : 123456,
            };
            // ]]> -->
        </script>
        <script src="https://revive.com/www/delivery/spcjs.php?cat_name=foo"></script>
        ';

        $this->ds->expects($this->any())->method('get')
            ->with('revive_ad_server')
            ->willReturn([ 'url' => 'https://revive.com' ]);

        $zones[] = [
            'id'      => 123,
            'openXId' => 123456
        ];
        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/revive.header.tpl', [
                'config' => [ 'url' => 'https://revive.com' ],
                'zones'  => $zones
            ])
            ->willReturn($output);

        $ads = [ $ad ];
        $this->assertEquals($output, $this->renderer->renderInlineReviveHeader($ads));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineReviveSlot
     */
    public function testRenderInlineReviveSlot()
    {
        $ad            = new \Advertisement();
        $ad->id        = 123;
        $ad->positions = [ 50 ];
        $ad->params    = [];

        $url         = '/ads/get/123';
        $returnValue = '<iframe src="' . $url . '"></iframe>
            <script data-id="{$id}">
                <!--// <![CDATA[
                OA_show(\'zone_' . $ad->id . '\');
                // ]]> -->
            </script>';

        $this->router->expects($this->any())->method('generate')
            ->with('frontend_ad_show', [ 'id' => $ad->id ])
            ->willReturn($url);

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/revive.slot.tpl', [
                'id'     => $ad->id,
                'iframe' => false,
                'url'    => $url,
            ])
            ->willReturn($returnValue);

        $this->assertEquals($returnValue, $this->renderer->renderInlineReviveSlot($ad));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineSmartHeader
     */
    public function testRenderInlineSmartHeader()
    {
        $ad              = new \Advertisement();
        $ad->id          = 1;
        $ad->created     = '2019-03-28 18:40:32';
        $ad->params      = [ 'smart_format_id' => 321 ];
        $ad->with_script = 4;

        $content     = new \stdClass();
        $content->id = 123;

        $params = [
            'advertisementGroup' => 'foo',
            'category'           => '',
            'extension'          => '',
            'content'            => $content

        ];

        $output = '<script type="application/javascript" src="//ced.sascdn.com/tag/0000/smart.js" async></script>
        <script type="application/javascript">
            var sas = sas || {};
            sas.cmd = sas.cmd || [];
            sas.cmd.push(function() {
                sas.setup({ networkid: 0000, domain: "https://example.com", async: true });
            });
        </script>';

        $config = [
            'domain'      => 'https://example.com',
            'network_id'  => 0000,
            'site_id'     => 1234,
            'page_id'     => [ 'foo' => 111 ],
            'tags_format' => 'ajax_async'
        ];

        $zones[] = [
            'id'        => 1,
            'format_id' => 321
        ];

        $this->ds->expects($this->at(0))->method('get')
            ->with('smart_ad_server')
            ->willReturn($config);

        $this->ds->expects($this->at(1))->method('get')
            ->with('smart_custom_code')
            ->willReturn('');

        $this->ds->expects($this->at(2))->method('get')
            ->with('smart_ad_server')
            ->willReturn($config);

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/smart.header.ajax_async.tpl', [
                'config'        => $config,
                'page_id'       => 111,
                'zones'         => $zones,
                'customCode'    => '',
                'targetingCode' => ''
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderInlineSmartHeader([ $ad ], $params)
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineSmartHeader
     */
    public function testRenderInlineSmartHeaderWithEmptyAds()
    {
        $this->assertEmpty($this->renderer->renderInlineSmartHeader([], []));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineSmartHeader
     */
    public function testRenderInlineSmartHeaderWithEmptyAdsFilter()
    {
        $ad              = new \Advertisement();
        $ad->id          = 1;
        $ad->created     = '2019-03-28 18:40:32';
        $ad->with_script = 999;
        $ad->params      = [ 'smart_format_id' => 321 ];

        $this->assertEmpty($this->renderer->renderInlineSmartHeader([ $ad ], []));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineSmartSlot
     */
    public function testRenderInlineSmartSlot()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';
        $ad->params  = [ 'smart_format_id' => 321 ];

        $content     = new \stdClass();
        $content->id = 123;

        $params = [
            'advertisementGroup' => 'foo',
            'category'           => '',
            'extension'          => '',
            'content'            => $content

        ];

        $output = '<div id="sas_{$id}"></div>
            <script type="application/javascript">
            sas.cmd.push(function() {
                sas.render("{$id}");
            });
            </script>';

        $config = [
            'domain'      => 'https://example.com',
            'network_id'  => 0000,
            'site_id'     => 1234,
            'page_id'     => [ 'foo' => 111 ],
            'tags_format' => 'onecall_async'
        ];

        $this->ds->expects($this->any())->method('get')
            ->with('smart_ad_server')
            ->willReturn($config);

        // Avoid template params due to untestable rand() function
        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/smart.slot.onecall_async.tpl')
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderInlineSmartSlot($ad, $params)
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineInterstitial
     */
    public function testRenderInlineInterstitial()
    {
        $ad                  = new \Advertisement();
        $ad->positions       = [ 1, 2, 50 ];
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

        $ad->params['device'] = [ 'phone' => 1, 'desktop' => 1 ];

        $ads = [ $ad ];

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
                        . ' data-timeout="5" data-type="1,2,50"></div>'
                . '</div>'
            . '</div>'
        . '</div>';

        $this->assertEquals($output, $this->renderer->renderInlineInterstitial($ads));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineInterstitial
     */
    public function testRenderInlineInterstitialWithEmptySizes()
    {
        $ad                   = new \Advertisement();
        $ad->positions        = [ 1, 2, 50 ];
        $ad->params['sizes']  = [];
        $ad->params['device'] = [ 'phone' => 1, 'desktop' => 1 ];

        $ads = [ $ad ];

        $output = '';

        $this->assertEquals($output, $this->renderer->renderInlineInterstitial($ads));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderInlineInterstitial
     */
    public function testRenderInlineInterstitialWithNoInterstitials()
    {
        $ads = [ ];

        $returnValue = '';
        $this->assertEquals(
            $returnValue,
            $this->renderer->renderInlineInterstitial($ads)
        );

        $ad1            = new \Advertisement();
        $ad1->positions = [ 1, 2 ];
        $ads            = [ $ad1 ];

        $returnValue = '';
        $this->assertEquals(
            $returnValue,
            $this->renderer->renderInlineInterstitial($ads)
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderSafeFrameSlot
     */
    public function testRenderSafeFrameSlot()
    {
        $ad            = new \Advertisement();
        $ad->id        = 1;
        $ad->positions = 1;
        $params        = [];

        $returnValue = '<div class="ad-slot oat" data-type="1"></div>';
        $this->assertEquals(
            $returnValue,
            $this->renderer->renderSafeFrameSlot($ad, $params)
        );

        $params = [ 'floating' => true ];

        $returnValue = '<div class="ad-slot oat" data-id="" data-type="37"></div>';
        $this->assertEquals(
            $returnValue,
            $this->renderer->renderSafeFrameSlot($ad, $params)
        );

        $params = [ 'width' => 620 ];

        $returnValue = '<div class="ad-slot oat" data-type="1" data-width="620"></div>';
        $this->assertEquals(
            $returnValue,
            $this->renderer->renderSafeFrameSlot($ad, $params)
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderSafeFrame
     */
    public function testRenderSafeFrameWithImage()
    {
        $ad      = new \Advertisement();
        $ad->img = '123';

        $params = [];

        $renderer = $this->getMockBuilder('Common\Core\Component\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderSafeFrameImage', 'getImage' ])
            ->getMock();

        $renderer->expects($this->once())->method('getImage')
            ->willReturn(new \Photo());

        $renderer->expects($this->once())->method('renderSafeFrameImage')
            ->willReturn('foo');

        $this->assertEquals(
            'foo',
            $renderer->renderSafeFrame($ad, $params)
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderSafeFrame
     */
    public function testRenderSafeFrameWithEmptyImage()
    {
        $ad     = new \Advertisement();
        $params = [];

        $this->assertEmpty($this->renderer->renderSafeFrame($ad, $params));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderSafeFrame
     */
    public function testRenderSafeFrameWithFlash()
    {
        $ad     = new \Advertisement();
        $params = [];

        $renderer = $this->getMockBuilder('Common\Core\Component\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderSafeFrameFlash', 'getImage' ])
            ->getMock();

        $img           = new \Photo();
        $img->type_img = 'swf';


        $renderer->expects($this->once())->method('getImage')
            ->willReturn($img);

        $renderer->expects($this->once())->method('renderSafeFrameFlash')
            ->willReturn('foo');

        $this->assertEquals('foo', $renderer->renderSafeFrame($ad, $params));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderSafeFrame
     */
    public function testRenderSafeFrameWithHtml()
    {
        $ad     = new \Advertisement();
        $params = [];

        $renderer = $this->getMockBuilder('Common\Core\Component\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderSafeFrameHtml' ])
            ->getMock();

        $renderer->expects($this->once())->method('renderSafeFrameHtml')
            ->willReturn('foo');

        $ad->with_script = 1;
        $this->assertEquals('foo', $renderer->renderSafeFrame($ad, $params));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderSafeFrame
     */
    public function testRenderSafeFrameWithRevive()
    {
        $ad     = new \Advertisement();
        $params = [];

        $renderer = $this->getMockBuilder('Common\Core\Component\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderSafeFrameRevive' ])
            ->getMock();

        $renderer->expects($this->once())->method('renderSafeFrameRevive')
            ->willReturn('foo');

        $ad->with_script = 2;
        $this->assertEquals('foo', $renderer->renderSafeFrame($ad, $params));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderSafeFrame
     */
    public function testRenderSafeFrameWithDFP()
    {
        $ad     = new \Advertisement();
        $params = [];

        $renderer = $this->getMockBuilder('Common\Core\Component\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderSafeFrameDFP' ])
            ->getMock();

        $renderer->expects($this->once())->method('renderSafeFrameDFP')
            ->willReturn('foo');

        $ad->with_script = 3;
        $this->assertEquals('foo', $renderer->renderSafeFrame($ad, $params));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::renderSafeFrame
     */
    public function testRenderSafeFrameWithSmart()
    {
        $ad     = new \Advertisement();
        $params = [];

        $renderer = $this->getMockBuilder('Common\Core\Component\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderSafeFrameSmart' ])
            ->getMock();

        $renderer->expects($this->once())->method('renderSafeFrameSmart')
            ->willReturn('foo');

        $ad->with_script = 4;
        $this->assertEquals('foo', $renderer->renderSafeFrame($ad, $params));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::RenderSafeFrameRevive
     */
    public function testRenderSafeFrameRevive()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';
        $ad->params  = [ 'openx_zone_id' => 321 ];

        $params = [
            'category'  => 'foo',
            'extension' => 'bar'
        ];

        $output = '<html>
        <head>
          <style>
            body {
              display: table;
              margin: 0;
              overflow: hidden;
              padding: 0;
              text-align: center;
            }

            img {
              height: auto;
              max-width: 100%;
            }
          </style>
          <script>
            var OA_zones = {
              \'zone_{{$id}}\': {{$openXId}}
            };
          </script>
        </head>
        <body>
          <div class="content">
            <script src="{{$url}}/www/delivery/spcjs.php?cat_name={{$category}}"></script>
            <script>
              OA_show(\'zone_{{$id}}\');
            </script>
          </div>
        </body>
      </html>';

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/safeframe/openx.tpl', [
                'id'            => 1,
                'category'      => 'foo',
                'extension'     => 'bar',
                'openXId'       => 321,
                'url'           => 'https://revive.com'
            ])
            ->willReturn($output);


        $this->ds->expects($this->any())->method('get')
            ->with('revive_ad_server')
            ->willReturn([ 'url' => 'https://revive.com' ]);

        $method = new \ReflectionMethod($this->renderer, 'renderSafeFrameRevive');
        $method->setAccessible(true);

        $this->assertEquals(
            $output,
            $method->invokeArgs($this->renderer, [ $ad, $params ])
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::RenderSafeFrameDFP
     */
    public function testRenderSafeFrameDFP()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';
        $ad->params  = [ 'googledfp_unit_id' => 321 ];

        $ad->params['device'] = [ 'phone' => 1, 'desktop' => 1 ];
        $ad->params['sizes']  = [
            '0' => [
                'width' => 300,
                'height' => 300,
                'device' => 'desktop'
            ],
            '1' => [
                'width' => 1,
                'height' => 1,
                'device' => 'tablet'
            ]
        ];

        $params = [
            'category'  => '',
            'extension' => '',
            'contentId' => ''
        ];

        $output = '<html>
        <head>
          <style>
            body {
              display: table;
              margin: 0;
              overflow: hidden;
              padding: 0;
              text-align: center;
            }

            img {
              height: auto;
              max-width: 100%;
            }
          </style>
        </head>
        <body>
          <div class="content">
            <script async="async" src="https://www.googletagservices.com/tag/js/gpt.js"></script>
            <script>
              var googletag = googletag || {};
              googletag.cmd = googletag.cmd || [];
            </script>
            <script>
              googletag.cmd.push(function() {
                googletag.defineSlot(\'321\', [ [ 300, 300 ], [ 1, 1 ] ], \'zone_1\').addService(googletag.pubads());
                googletag.pubads().enableSingleRequest();
                googletag.pubads().collapseEmptyDivs();
                googletag.enableServices();
              });
            </script>
            <div id="zone_1">
              <script>
                googletag.cmd.push(function() { googletag.display(\'zone_1\'); });
              </script>
            </div>
          </div>
        </body>
      </html>';

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/safeframe/dfp.tpl', [
                'id'            => 1,
                'dfpId'         => 321,
                'sizes'         => '[ [ 300, 300 ], [ 1, 1 ] ]',
                'customCode'    => '',
                'targetingCode' => ''
            ])
            ->willReturn($output);

        $method = new \ReflectionMethod($this->renderer, 'renderSafeFrameDFP');
        $method->setAccessible(true);

        $this->assertEquals(
            $output,
            $method->invokeArgs($this->renderer, [ $ad, $params ])
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::RenderSafeFrameSmart
     */
    public function testRenderSafeFrameSmart()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';
        $ad->params  = [ 'smart_format_id' => 321 ];

        $params = [ 'advertisementGroup' => 'foo' ];
        $output = '<html>
        <head>
          <style>
            body {
              display: table;
              margin: 0;
              overflow: hidden;
              padding: 0;
              text-align: center;
            }

            img {
              height: auto;
              max-width: 100%;
            }
          </style>
        </head>
        <body>
          <div class="content">
            <script type="application/javascript" src="//ced.sascdn.com/tag/0000/smart.js" async></script>
            <div id="sas_321"></div>
            <script type="application/javascript">
              var sas = sas || {};
              sas.cmd = sas.cmd || [];
              sas.cmd.push(
                function () {
                  sas.call(
                    { siteId: 1234, pageId: 111, formatId: 321, tagId: "sas_321" },
                    { networ7kId: 0000, domain: "https://example.com" /*, onNoad: function() {} */ }
                  );
                }
              );
            </script>
          </div>
        </body>
      </html>';

        $config = [
            'domain'     => 'https://example.com',
            'network_id' => 0000,
            'site_id'    => 1234,
            'page_id'    => [ 'foo' => 111 ]
        ];

        $this->ds->expects($this->any())->method('get')
            ->with('smart_ad_server')
            ->willReturn($config);

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/safeframe/smart.tpl', [
                'config'    => $config,
                'page_id'   => 111,
                'format_id' => 321
            ])
            ->willReturn($output);

        $method = new \ReflectionMethod($this->renderer, 'renderSafeFrameSmart');
        $method->setAccessible(true);

        $this->assertEquals(
            $output,
            $method->invokeArgs($this->renderer, [ $ad, $params ])
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::RenderSafeFrameFlash
     */
    public function testRenderSafeFrameFlash()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $photo                = new \Photo();
        $photo->category_name = 'foo';
        $photo->width         = 300;
        $photo->height        = 300;
        $photo->path_file     = '/path/';
        $photo->name          = 'foo.png';
        $photo->url           = '/ads/get/123';

        $output = '<html>
        <head>
          <style>
            body {
              display: table;
              margin: 0;
              overflow: hidden;
              padding: 0;
              text-align: center;
            }

            img {
              height: auto;
              max-width: 100%;
            }
          </style>
        </head>
        <body>
          <div class="content">
            <div style="width:300px; height:300px; margin: 0 auto;">
              <div style="position: relative; width: 300px; height: 300px;">
                <div style="left:0px;top:0px;cursor:pointer;background-color:#FFF;'
                . ' filter:alpha(opacity=0);opacity:0;position:absolute;z-index:100;'
                . 'width: 300px;height:300px;" onclick="javascript:window.open("/ads/get/123", "_blank");'
                . ' return false;"></div>
                <object width="300" height="300" >
                  <param name="wmode" value="transparent" />
                  <param name="movie" value="/ads/get/123" />
                  <param name="width" value="300" />
                  <param name="height" value="300" />
                  <embed src="http://console/media/opennemas/images/path/foo.png"'
                  . ' width="300" height="300" SCALE="exactfit" wmode="transparent"></embed>
                </object>
              </div>
            </div>
          </div>
        </body>
      </html>';

        $this->router->expects($this->any())->method('generate')
            ->with('frontend_ad_redirect', [ 'id' => '20190328184032000001' ])
            ->willReturn('/ads/get/123');

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/safeframe/flash.tpl', [
                'width'    => 300,
                'height'   => 300,
                'src'      => 'http://console/media/opennemas/images/path/foo.png',
                'url'      => '/ads/get/123'
            ])
            ->willReturn($output);

        $method = new \ReflectionMethod($this->renderer, 'renderSafeFrameFlash');
        $method->setAccessible(true);

        $this->assertEquals(
            $output,
            $method->invokeArgs($this->renderer, [ $ad, $photo ])
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::RenderSafeFrameHtml
     */
    public function testRenderSafeFrameHtml()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $output = '<html><style>body { margin: 0; overflow: hidden;'
            . ' padding: 0; text-align: center; } img { max-width: 100% }'
            . '</style><body><div class="content"></div></body>';

        $method = new \ReflectionMethod($this->renderer, 'renderSafeFrameHtml');
        $method->setAccessible(true);

        $this->assertEquals(
            $output,
            $method->invokeArgs($this->renderer, [ $ad ])
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::RenderSafeFrameImage
     */
    public function testRenderSafeFrameImage()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $photo                = new \Photo();
        $photo->category_name = 'foo';
        $photo->width         = 300;
        $photo->height        = 300;
        $photo->path_file     = '/path/';
        $photo->name          = 'foo.png';
        $photo->url           = '/ads/get/123';

        $output = '<html>
        <head>
          <style>
            body {
              display: table;
              margin: 0;
              overflow: hidden;
              padding: 0;
              text-align: center;
            }

            img {
              height: auto;
              max-width: 100%;
            }
          </style>
        </head>
        <body>
          <div class="content">
            <a target="_blank" href="/ads/get/123" rel="nofollow">
              <img alt="foo" src="thud.opennemas.com/media/opennemas/images/path/foo.png" width="300" height="300"/>
            </a>
          </div>
        </body>
      </html>';

        $this->router->expects($this->any())->method('generate')
            ->with('frontend_ad_redirect', [ 'id' => '20190328184032000001' ])
            ->willReturn('/ads/get/123');

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/safeframe/image.tpl', [
                'category' => 'foo',
                'width'    => 300,
                'height'   => 300,
                'src'      => 'thud.opennemas.com/media/opennemas/images/path/foo.png',
                'url'      => '/ads/get/123'
            ])
            ->willReturn($output);

        $method = new \ReflectionMethod($this->renderer, 'renderSafeFrameImage');
        $method->setAccessible(true);

        $this->assertEquals(
            $output,
            $method->invokeArgs($this->renderer, [ $ad, $photo ])
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::getHtml
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

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::getImage
     */
    public function testGetImage()
    {
        $photo   = new \Photo();
        $ad      = new \Advertisement();
        $ad->img = 0;

        $method = new \ReflectionMethod($this->renderer, 'getImage');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->renderer, [ $ad ]));

        // Not empty image
        $ad->img = 1;

        $this->em->expects($this->any())->method('find')
            ->with('Photo', $ad->img)
            ->willReturn($photo);

        $this->assertEquals(
            $photo,
            $method->invokeArgs($this->renderer, [ $ad ])
        );

        $this->em->expects($this->any())->method('find')
            ->with('Photo', $ad->img)
            ->will($this->throwException(new \Exception()));

        $this->assertEmpty($method->invokeArgs($this->renderer, [ $ad ]));
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::getDFPCustomCode
     */
    public function testGetDFPCustomCode()
    {
        $this->ds->expects($this->at(0))->method('get')
            ->with('dfp_custom_code')
            ->willReturn(base64_encode('dfp_custom_code'));

        $method = new \ReflectionMethod($this->renderer, 'getDFPCustomCode');
        $method->setAccessible(true);

        $this->assertEquals(
            'dfp_custom_code',
            $method->invokeArgs($this->renderer, [])
        );

        $this->ds->expects($this->any())->method('get')
            ->with('dfp_custom_code')
            ->willReturn(null);

        $this->assertEquals(
            '',
            $method->invokeArgs($this->renderer, [])
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::getDFPTargeting
     */
    public function testGetDFPTargeting()
    {
        $this->ds->expects($this->at(0))->method('get')
            ->with('dfp_options')
            ->willReturn([
                'target'     => 'cat',
                'module'     => 'mod',
                'content_id' => 'id'
            ]);

        $method = new \ReflectionMethod($this->renderer, 'getDFPTargeting');
        $method->setAccessible(true);

        $output = "googletag.pubads().setTargeting('cat', ['foo']);\n"
            . "googletag.pubads().setTargeting('mod', ['bar']);\n"
            . "googletag.pubads().setTargeting('id', ['baz']);\n";

        $this->assertEquals(
            $output,
            $method->invokeArgs($this->renderer, [ 'foo', 'bar', 'baz' ])
        );

        $this->ds->expects($this->any())->method('get')
            ->with('dfp_options')
            ->willReturn(null);

        $this->assertEquals(
            '',
            $method->invokeArgs($this->renderer, [ 'foo', 'bar', 'baz' ])
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::getSmartCustomCode
     */
    public function testGetSmartCustomCode()
    {
        $this->ds->expects($this->at(0))->method('get')
            ->with('smart_custom_code')
            ->willReturn(base64_encode('sas_custom_code'));

        $method = new \ReflectionMethod($this->renderer, 'getSmartCustomCode');
        $method->setAccessible(true);

        $this->assertEquals(
            'sas_custom_code',
            $method->invokeArgs($this->renderer, [])
        );

        $this->ds->expects($this->any())->method('get')
            ->with('smart_custom_code')
            ->willReturn(null);

        $this->assertEquals(
            '',
            $method->invokeArgs($this->renderer, [])
        );
    }

    /**
     * @covers \Common\Core\Component\Renderer\AdvertisementRenderer::getSmartTargeting
     */
    public function testGetSmartTargeting()
    {
        $this->ds->expects($this->any())->method('get')
            ->with('smart_ad_server')
            ->willReturn([
                'category_targeting' => 'cat',
                'module_targeting'   => 'mod',
                'url_targeting'      => 'url'
            ]);

        $targetingCode = 'cat=foo;mod=bar;url=baz;';

        $method = new \ReflectionMethod($this->renderer, 'getSmartTargeting');
        $method->setAccessible(true);

        $this->assertEquals(
            $targetingCode,
            $method->invokeArgs($this->renderer, [ 'foo', 'bar', 'baz' ])
        );
    }
}
