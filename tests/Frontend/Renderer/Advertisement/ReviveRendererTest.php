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
use Frontend\Renderer\Advertisement\ReviveRenderer;

/**
 * Defines test cases for ReviveRenderer class.
 */
class ReviveRendererTest extends TestCase
{
    /**
     * @var ReviveRenderer
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

        $this->renderer = new ReviveRenderer($this->container);
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
     * @covers \Frontend\Renderer\Advertisement\ReviveRenderer::renderInline
     */
    public function testRenderInline()
    {
        $ad            = new \Advertisement();
        $ad->id        = 123;
        $ad->positions = [ 50 ];
        $ad->params    = [];

        $url         = '/ads/get/123';
        $returnValue = '<iframe src="' . $url . '"></iframe>
            <script data-id="{$id}">
                OA_show(\'zone_' . $ad->id . '\');
            </script>';

        $this->router->expects($this->any())->method('generate')
            ->with('api_v1_advertisement_show', [ 'id' => $ad->id ])
            ->willReturn($url);

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/revive.slot.tpl', [
                'id'     => $ad->id,
                'iframe' => false,
                'url'    => $url,
            ])
            ->willReturn($returnValue);

        $this->assertEquals(
            $returnValue,
            $this->renderer->renderInline($ad, [])
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\ReviveRenderer::renderSafeFrame
     */
    public function testRenderSafeFrame()
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

        $this->assertEquals(
            $output,
            $this->renderer->renderSafeFrame($ad, $params)
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\ReviveRenderer::renderInlineHeader
     */
    public function testRenderInlineHeader()
    {
        $ad              = new \Advertisement();
        $ad->id          = 123;
        $ad->positions   = [ 50 ];
        $ad->with_script = 2;
        $ad->params      = [ 'openx_zone_id' => 123456 ];

        $output = '<script>
            var OA_zones = {
                \'zone_123\' : 123456,
            };
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

        $this->assertEquals(
            $output,
            $this->renderer->renderInlineHeader([ $ad ], [])
        );
    }
}
