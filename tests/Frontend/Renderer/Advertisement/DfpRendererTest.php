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
use Frontend\Renderer\Advertisement\DfpRenderer;

/**
 * Defines test cases for DfpRenderer class.
 */
class DfpRendererTest extends TestCase
{
    /**
     * @var DfpRenderer
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

        $this->adRenderer = $this->getMockBuilder('Frontend\Renderer\AdvertisementRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getRendererClass' ])
            ->getMock();

        $this->renderer = new DfpRenderer($this->container);
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
     * @covers \Frontend\Renderer\Advertisement\DfpRenderer::renderAmp
     */
    public function testRenderAmp()
    {
        $ad         = new \Advertisement();
        $ad->params = [ 'googledfp_unit_id' => 321 ];

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
            'category'  => 'foo',
            'extension' => 'bar',
            'content' => $content
        ];

        $this->ds->expects($this->at(0))->method('get')
            ->with('dfp_options')
            ->willReturn([
                'target'     => 'cat',
                'module'     => 'mod',
                'content_id' => 'id'
            ]);

        $output = '<amp-ad
            data-block-on-consent
            data-npa-on-unknown-consent="true"
            width="300"
            height="300"
            type="doubleclick"
            data-slot="321"
        ></amp-ad>';

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/amp/dfp.tpl', [
                'dfpId'     => 321,
                'sizes'     => '',
                'width'     => 300,
                'height'    => 300,
                'targeting' => '{"targeting":{"cat":"foo","mod":"bar","id":123}}'
            ])
            ->willReturn($output);

        $this->assertEquals(
            '<div class="ad-slot oat oat-visible oat-top " data-mark="Advertisement">'
                . $output . '</div>',
            $this->renderer->renderAmp($ad, $params)
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\DfpRenderer::renderFia
     */
    public function testRenderFia()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';
        $ad->params  = [ 'googledfp_unit_id' => 321 ];

        $ad->params['sizes'] = [
            '0' => [
                'width' => 300,
                'height' => 300,
                'device' => 'phone'
            ],
        ];

        $params = [ 'op-ad-default' => true ];
        $output = '<figure class="op-ad op-ad-default">
            <iframe height="300" width="300" style="border:0;margin:0;padding:0;">
                <script async="async" src="https://www.googletagservices.com/tag/js/gpt.js"></script>
                <script>
                var googletag = googletag || {};
                googletag.cmd = googletag.cmd || [];
                </script>
                <script>
                googletag.cmd.push(function() {
                    googletag.defineSlot(\'321\', [ [ 300, 300 ] ],'
                    . ' \'zone_1\').addService(googletag.pubads());
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
            </iframe>
        </figure>';

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/fia/dfp.tpl', [
                'id'      => 1,
                'dfpId'   => 321,
                'sizes'   => '[ [ 300, 300 ] ]',
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
     * @covers \Frontend\Renderer\Advertisement\DfpRenderer::renderInline
     */
    public function testRenderInline()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

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

        $output = '<div class="ad-slot oat oat-visible oat-top " data-mark="Advertisement" '
            . 'style="height:600px;width:300px;">'
            . $output . '</div>';

        $this->assertEquals(
            $output,
            $this->renderer->renderInline($ad, [])
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\DfpRenderer::renderInline
     */
    public function testRenderInlineWithAmp()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\DfpRenderer')
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
     * @covers \Frontend\Renderer\Advertisement\DfpRenderer::renderInline
     */
    public function testRenderInlineWithFia()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\DfpRenderer')
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
     * @covers \Frontend\Renderer\Advertisement\DfpRenderer::renderSafeFrame
     */
    public function testRenderSafeFrame()
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

        $content     = new \stdClass();
        $content->id = 123;

        $params = [
            'category'  => '',
            'extension' => '',
            'contentId' => $content->id
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
                'id'         => 1,
                'dfpId'      => 321,
                'sizes'      => '[ [ 300, 300 ], [ 1, 1 ] ]',
                'customCode' => '',
                'targeting'  => ''
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderSafeFrame($ad, $params)
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\DfpRenderer::renderInlineHeader
     */
    public function testRenderInlineHeader()
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
                'category'   => $params['category'],
                'extension'  => $params['extension'],
                'customCode' => '',
                'options'    => null,
                'targeting'  => '',
                'zones'      => $zones
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $this->renderer->renderInlineHeader([ $ad ], $params)
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\DfpRenderer::getCustomCode
     */
    public function testGetCustomCode()
    {
        $this->ds->expects($this->at(0))->method('get')
            ->with('dfp_custom_code')
            ->willReturn(base64_encode('dfp_custom_code'));

        $method = new \ReflectionMethod($this->renderer, 'getCustomCode');
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
     * @covers \Frontend\Renderer\Advertisement\DfpRenderer::getTargeting
     */
    public function testGetTargeting()
    {
        $this->ds->expects($this->at(0))->method('get')
            ->with('dfp_options')
            ->willReturn([
                'target'     => 'cat',
                'module'     => 'mod',
                'content_id' => 'id'
            ]);

        $method = new \ReflectionMethod($this->renderer, 'getTargeting');
        $method->setAccessible(true);

        $output = [
            'cat' => 'foo',
            'mod' => 'bar',
            'id'  => 'baz',
        ];

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
     * @covers \Frontend\Renderer\Advertisement\DfpRenderer::getAmpMultiSizes
     */
    public function testGetAmpMultiSizes()
    {
        $method = new \ReflectionMethod($this->renderer, 'getAmpMultiSizes');
        $method->setAccessible(true);

        $ad                  = new \Advertisement();
        $ad->params['sizes'] = [
            '0' => [
                'width' => 300,
                'height' => 600,
                'device' => 'phone'
            ],
            '1' => [
                'width' => 300,
                'height' => 250,
            ]
        ];

        $this->assertEquals(
            "300x250",
            $method->invokeArgs($this->renderer, [ $ad, 'phone' ])
        );
    }
}
