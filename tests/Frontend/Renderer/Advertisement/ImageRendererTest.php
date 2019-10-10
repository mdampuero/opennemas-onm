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
use Frontend\Renderer\Advertisement\ImageRenderer;

/**
 * Defines test cases for ImageRenderer class.
 */
class ImageRendererTest extends TestCase
{
    /**
     * @var ImageRenderer
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

        $this->ugh = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->disableOriginalConstructor()
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

        $this->renderer = new ImageRenderer($this->container);
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

            case 'core.helper.url_generator':
                return $this->ugh;

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
     * @covers \Frontend\Renderer\Advertisement\ImageRenderer::renderInline
     */
    public function testRenderInlineWithNoImage()
    {
        $ad = new \Advertisement();

        $this->assertEmpty($this->renderer->renderInline($ad, []));
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\ImageRenderer::renderInline
     */
    public function testRenderInline()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $photo         = new \Photo();
        $photo->width  = 300;
        $photo->height = 300;

        $output = '<a target="_blank" href="/ads/get/123" rel="nofollow">
            <img src="thud.opennemas.com/media/opennemas/images/path/foo.png" width="300" height="300" />
        </a>';

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\ImageRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getImage' ])
            ->getMock();

        $renderer->expects($this->once())->method('getImage')
            ->willReturn($photo);

        $this->router->expects($this->any())->method('generate')
            ->with('frontend_ad_redirect', [ 'id' => '20190328184032000000' ])
            ->willReturn('/ads/get/123');

        $this->ugh->expects($this->once())->method('generate')
            ->with($photo)
            ->willReturn('/media/opennemas/images/path/foo.png');

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/image.tpl', [
                'width'    => 300,
                'height'   => 300,
                'src'      => '/media/opennemas/images/path/foo.png',
                'url'      => 'thud.opennemas.com/ads/get/123'
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $renderer->renderInline($ad, [])
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\ImageRenderer::renderInline
     */
    public function testRenderInlineWithFlash()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $photo           = new \Photo();
        $photo->width    = 300;
        $photo->height   = 300;
        $photo->type_img = 'swf';

        $output = '<object width="300" height="300">
            <param name="wmode" value="transparent" />
            <param name="movie" value="/ads/get/123" />
            <param name="width" value="300" />
            <param name="height" value="300" />
            <embed src="thud.opennemas.com/media/opennemas/images/path/foo.png"'
            . ' width="300" height="300" SCALE="exactfit" wmode="transparent"></embed>
        </object>';

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\ImageRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getImage' ])
            ->getMock();

        $renderer->expects($this->once())->method('getImage')
            ->willReturn($photo);

        $this->router->expects($this->any())->method('generate')
            ->with('frontend_ad_redirect', [ 'id' => '20190328184032000000' ])
            ->willReturn('/ads/get/123');

        $this->ugh->expects($this->once())->method('generate')
            ->with($photo)
            ->willReturn('/media/opennemas/images/path/foo.png');

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/flash.tpl', [
                'width'    => 300,
                'height'   => 300,
                'src'      => '/media/opennemas/images/path/foo.png',
                'url'      => 'thud.opennemas.com/ads/get/123'
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $renderer->renderInline($ad, [])
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\ImageRenderer::renderInline
     */
    public function testRenderInlineImageWithAmp()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $photo         = new \Photo();
        $photo->width  = 300;
        $photo->height = 300;

        $output = '<a target="_blank" href="/ads/get/123" rel="nofollow">
            <amp-img
            src="thud.opennemas.com/media/opennemas/images/path/foo.png"
            width="300"
            height="300">
            </amp-img>
        </a>';

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\ImageRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getImage' ])
            ->getMock();

        $renderer->expects($this->once())->method('getImage')
            ->willReturn($photo);

        $this->router->expects($this->any())->method('generate')
            ->with('frontend_ad_redirect', [ 'id' => '20190328184032000000' ])
            ->willReturn('/ads/get/123');

        $this->ugh->expects($this->once())->method('generate')
            ->with($photo)
            ->willReturn('/media/opennemas/images/path/foo.png');

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('advertisement/helpers/inline/image.amp.tpl', [
                'width'    => 300,
                'height'   => 300,
                'src'      => '/media/opennemas/images/path/foo.png',
                'url'      => 'thud.opennemas.com/ads/get/123'
            ])
            ->willReturn($output);

        $this->assertEquals(
            $output,
            $renderer->renderInline($ad, [ 'format' => 'amp' ])
        );
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\ImageRenderer::renderSafeFrame
     */
    public function testRenderSafeFrameWithImage()
    {
        $ad       = new \Advertisement();
        $ad->path = '123';

        $params = [];

        $renderer = $this->getMockBuilder('Frontend\Renderer\Advertisement\ImageRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'renderSafeFrameImage', 'getImage' ])
            ->getMock();

        $renderer->expects($this->once())->method('getImage')
            ->willReturn(new \Photo());

        $renderer->expects($this->once())->method('renderSafeFrameImage')
            ->willReturn('foo');

        $this->assertEquals('foo', $renderer->renderSafeFrame($ad, $params));
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\ImageRenderer::renderSafeFrame
     */
    public function testRenderSafeFrameWithEmptyImage()
    {
        $ad = new \Advertisement();

        $this->assertEmpty($this->renderer->renderSafeFrame($ad, []));
    }

    /**
     * @covers \Frontend\Renderer\Advertisement\ImageRenderer::RenderSafeFrameImage
     */
    public function testRenderSafeFrameImageWithFlash()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $photo           = new \Photo();
        $photo->width    = 300;
        $photo->height   = 300;
        $photo->type_img = 'swf';

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
                  <embed src="/media/opennemas/images/path/foo.swf"'
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

        $this->ugh->expects($this->once())->method('generate')
            ->with($photo)
            ->willReturn('/media/opennemas/images/path/foo.swf');

        $this->templateAdmin->expects($this->once())->method('fetch')
            ->with('advertisement/helpers/safeframe/flash.tpl', [
                'width'    => 300,
                'height'   => 300,
                'src'      => '/media/opennemas/images/path/foo.swf',
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
     * @covers \Frontend\Renderer\Advertisement\ImageRenderer::RenderSafeFrameImage
     */
    public function testRenderSafeFrameImage()
    {
        $ad          = new \Advertisement();
        $ad->id      = 1;
        $ad->created = '2019-03-28 18:40:32';

        $photo         = new \Photo();
        $photo->width  = 300;
        $photo->height = 300;

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
              <img alt="foo" src="/media/opennemas/images/path/foo.png" width="300" height="300"/>
            </a>
          </div>
        </body>
      </html>';

        $this->router->expects($this->any())->method('generate')
            ->with('frontend_ad_redirect', [ 'id' => '20190328184032000001' ])
            ->willReturn('/ads/get/123');

        $this->ugh->expects($this->once())->method('generate')
            ->with($photo)
            ->willReturn('/media/opennemas/images/path/foo.png');

        $this->templateAdmin->expects($this->once())->method('fetch')
            ->with('advertisement/helpers/safeframe/image.tpl', [
                'width'    => 300,
                'height'   => 300,
                'src'      => '/media/opennemas/images/path/foo.png',
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
     * @covers \Frontend\Renderer\Advertisement\ImageRenderer::getImage
     */
    public function testGetImage()
    {
        $photo    = new \Photo();
        $ad       = new \Advertisement();
        $ad->path = 0;

        $method = new \ReflectionMethod($this->renderer, 'getImage');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->renderer, [ $ad ]));

        // Not empty image
        $ad->path = 1;

        $this->em->expects($this->any())->method('find')
            ->with('Photo', $ad->path)
            ->willReturn($photo);

        $this->assertEquals(
            $photo,
            $method->invokeArgs($this->renderer, [ $ad ])
        );

        $this->em->expects($this->any())->method('find')
            ->with('Photo', $ad->path)
            ->will($this->throwException(new \Exception()));

        $this->assertEmpty($method->invokeArgs($this->renderer, [ $ad ]));
    }
}
