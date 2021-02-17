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
use Frontend\Renderer\Advertisement\SmartRenderer;

/**
 * Defines test cases for SmartRenderer class.
 */
class SmartRendererTest extends TestCase
{
    /**
     * @var SmartRenderer
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

        $this->renderer = new SmartRenderer($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
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
     * @covers \Frontend\Renderer\Advertisement\SmartRenderer::renderAmp
     */
    public function testRenderAmp()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';
        $ad->params  = [ 'smart_format_id' => 321 ];

        $ad->params['sizes'] = [
            '0' => [
                'width' => 300,
                'height' => 300,
                'device' => 'phone'
            ],
        ];

        $content     = new \stdClass();
        $content->id = 123;

        $params = [
            'current_position' => 1051,
            'category'         => 'foo',
            'extension'        => 'bar',
            'content'          => $content,
        ];

        $config = [
            'domain'     => 'https://example.com',
            'network_id' => 0000,
            'site_id'    => 1234,
            'page_id'    => [ 'other' => 111 ]
        ];

        $this->ds->expects($this->at(0))->method('get')
            ->with('smart_ad_server')
            ->willReturn($config);
        $this->ds->expects($this->at(1))->method('get')
            ->with('smart_ad_server')
            ->willReturn([
                'category_targeting' => 'cat',
                'module_targeting'   => 'mod',
                'url_targeting'      => 'url'
            ]);

        $output = '<amp-ad width="300" height="300"
                data-block-on-consent="_auto_reject"
                type="smartadserver"
                data-call="std"
                data-site="1234"
                data-page="111"
                data-format="321"
                data-target="cat=foo;mod=bar;url=123;"
                data-domain="https://example.com">
            </amp-ad>';

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/amp/smart.tpl', [
                'config'    => $config,
                'format_id' => $ad->params['smart_format_id'],
                'page_id'   => $config['page_id']['other'],
                'width'     => 300,
                'height'    => 300,
                'targeting' => 'cat=foo;mod=bar;url=123;'
            ])
            ->willReturn($output);

        $this->assertEquals(
            '<div class="ad-slot oat oat-visible oat-top " data-mark="Advertisement">'
                . $output . '</div>',
            $this->renderer->renderAmp($ad, $params)
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\SmartRenderer::renderFia
     */
    public function testRenderFia()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';
        $ad->params  = [ 'smart_format_id' => 321 ];

        $ad->params['sizes'] = [
            '0' => [
                'width' => 300,
                'height' => 300,
                'device' => 'phone'
            ],
        ];

        $params = [ 'current_position' => 1076 ];
        $config = [
            'domain'     => 'https://example.com',
            'network_id' => 0000,
            'site_id'    => 1234,
            'page_id'    => [ 'article_inner' => 111 ]
        ];

        $this->ds->expects($this->once())->method('get')
            ->with('smart_ad_server')
            ->willReturn($config);

        $output = '<figure class="op-ad">
            <iframe height="300" width="300" style="border:0;margin:0;padding:0;">
            <script type="application/javascript" src="//ced.sascdn.com/tag/0000/smart.js" async></script>
            <div id="sas_321"></div>
            <script type="application/javascript">
                var sas = sas || {};
                sas.cmd = sas.cmd || [];
                sas.cmd.push(
                function () {
                    sas.call(
                    { siteId: 1234, pageId: 111, formatId: 321, tagId: "sas_321" },
                    { networkId: 0000, domain: "https://example.com" }
                    );
                }
                );
            </script>
            </iframe>
        </figure>';

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/fia/smart.tpl', [
                'config'    => $config,
                'page_id'   => $config['page_id']['article_inner'],
                'format_id' => (int) $ad->params['smart_format_id'],
                'width'     => 300,
                'height'    => 300,
                'default'   => false,
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderFia($ad, $params)
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\SmartRenderer::renderInline
     */
    public function testRenderInline()
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
            'content'            => $content,
            'placeholder'        => 'bar'
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
            'page_id'     => [ 'foo' => 111, 'frontpage' => 111 ],
            'tags_format' => 'onecall_async'
        ];

        $this->ds->expects($this->at(0))->method('get')
            ->with('smart_ad_server')
            ->willReturn($config);
        $this->ds->expects($this->at(1))->method('get')
            ->with('smart_ad_server')
            ->willReturn([]);

        // Avoid template params due to untestable rand() function
        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/smart.slot.onecall_async.tpl')
            ->willReturn($output);

        $output = '<div class="ad-slot oat oat-visible oat-top " data-mark="Advertisement">'
            . $output . '</div>';
        $this->assertEquals(
            $output,
            $this->renderer->renderInline($ad, $params)
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\SmartRenderer::renderInline
     */
    public function testRenderInlineWithFia()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\SmartRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderFia' ])
            ->getMock();

        $renderer->expects($this->any())->method('renderFia')
            ->willReturn('foo');

        $this->assertEquals('foo', $renderer->renderInline($ad, [
            'ads_format' => 'fia'
        ]));
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\SmartRenderer::renderInline
     */
    public function testRenderInlineWithAmp()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\SmartRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderAmp' ])
            ->getMock();

        $renderer->expects($this->any())->method('renderAmp')
            ->willReturn('foo');

        $this->assertEquals('foo', $renderer->renderInline($ad, [
            'ads_format' => 'amp'
        ]));
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\SmartRenderer::renderSafeFrame
     */
    public function testRenderSafeFrame()
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
            'contentId'          => $content->id,
            'placeholder'        => 'bar'
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
            <script type="application/javascript" src="//ced.sascdn.com/tag/0000/smart.js" async></script>
            <div id="sas_321"></div>
            <script type="application/javascript">
              var sas = sas || {};
              sas.cmd = sas.cmd || [];
              sas.cmd.push(
                function () {
                  sas.call(
                    { siteId: 1234, pageId: 111, formatId: 321, tagId: "sas_321" },
                    { networkId: 0000, domain: "https://example.com" /*, onNoad: function() {} */ }
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
            'page_id'    => [ 'foo' => 111, 'frontpage' => 111 ]
        ];

        $this->ds->expects($this->any())->method('get')
            ->with('smart_ad_server')
            ->willReturn($config);

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/safeframe/smart.tpl', [
                'config'    => $config,
                'page_id'   => 111,
                'format_id' => 321,
                'targeting' => null,
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderSafeFrame($ad, $params)
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\SmartRenderer::renderInlineHeader
     */
    public function testRenderInlineHeader()
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
                sas.setup({ networkId: 0000, domain: "https://example.com", async: true });
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
                'config'     => $config,
                'page_id'    => 111,
                'zones'      => $zones,
                'customCode' => '',
                'targeting'  => null
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderInlineHeader([ $ad ], $params)
        );
    }
    /**
     * @covers \Frontend\Renderer\Advertisement\SmartRenderer::getCustomCode
     */
    public function testGetCustomCode()
    {
        $this->ds->expects($this->at(0))->method('get')
            ->with('smart_custom_code')
            ->willReturn(base64_encode('sas_custom_code'));

        $method = new \ReflectionMethod($this->renderer, 'getCustomCode');
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
     * @covers \Frontend\Renderer\Advertisement\SmartRenderer::getTargeting
     */
    public function testGetTargeting()
    {
        $this->ds->expects($this->any())->method('get')
            ->with('smart_ad_server')
            ->willReturn([
                'category_targeting' => 'cat',
                'module_targeting'   => 'mod',
                'url_targeting'      => 'url'
            ]);

        $targetingCode = 'cat=foo;mod=bar;url=baz;';

        $method = new \ReflectionMethod($this->renderer, 'getTargeting');
        $method->setAccessible(true);

        $this->assertEquals(
            $targetingCode,
            $method->invokeArgs($this->renderer, [ 'foo', 'bar', 'baz' ])
        );
    }
}
