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
use PHPUnit\Framework\TestCase;
use Frontend\Renderer\StatisticsRenderer;

/**
 * Defines test cases for StatisticsRenderer class.
 */
class StatisticsRendererTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->global = $this->getMockBuilder('Common\Core\Component\Core\GlobalVariables')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRequest', 'getContainer' ])
            ->getMock();

        $this->ds = $this->getMockForAbstractClass('Opennemas\Orm\Core\DataSet');

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->tpl = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\BrowserKit\Request')
            ->disableOriginalConstructor()
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->childRenderer = $this->getMockBuilder('Frontend\Renderer\Statistics\GAnalyticsRenderer')
            ->disableOriginalConstructor()
            ->setMethods([ 'validate', 'getParameters' ])
            ->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->ds);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->renderer = $this->getMockBuilder('Frontend\Renderer\StatisticsRenderer')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'getCodeType', 'getRendererClass' ])
            ->getMock();

        $this->renderer->expects($this->any())->method('getRendererClass')
            ->willReturn($this->childRenderer);

        $this->childRenderer->expects($this->any())->method('validate')
            ->willReturn(true);

        $this->global->expects($this->any())->method('getContainer')
            ->willReturn($this->container);
    }

    /**
     * @covers \Frontend\Renderer\StatisticsRenderer::__construct
     */
    public function testConstruct()
    {
        $this->assertEquals($this->global, $this->renderer->global);
        $this->assertEquals($this->tpl, $this->renderer->backend);
        $this->assertEquals($this->smarty, $this->renderer->frontend);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'orm.manager':
                return $this->em;

            case 'core.globals':
                return $this->global;

            case 'core.template.admin':
                return $this->tpl;

            case 'core.template.frontend':
                return $this->smarty;
        }

        return null;
    }

    /**
     * Tests render when output is empty.
     */
    public function testRenderWhenEmptyOutput()
    {
        $this->renderer->expects($this->at(0))->method('getCodeType')
            ->willReturn('image');

        $params = [ 'params' => 'params' ];

        $this->childRenderer->expects($this->at(1))->method('getParameters')
            ->willReturn($params);

        $path = 'statistics/helpers/ganalytics/image.tpl';

        $this->tpl->expects($this->once())->method('fetch')
            ->with($path, $params)->willReturn('Image code');

        $this->assertEquals('Image code', $this->renderer->render(null, ['types' => [ 'GAnalytics' ], 'output' => '']));
    }

    /**
     * Tests render when code is amp.
     */
    public function testRenderWhenAmp()
    {
        $content = new Content();
        $result  = '<body>' . "\n" . 'Amp code' . "\n" . 'Some different code</body>';
        $output  = '<body>' . "\n" . 'Some different code</body>';
        $types   = [ 'GAnalytics' ];

        $this->renderer->expects($this->at(0))->method('getCodeType')
            ->willReturn('amp');

        $this->tpl->expects($this->once())->method('fetch')
            ->willReturn('Amp code');

        $this->assertEquals($result, $this->renderer->render($content, [ 'types' => $types, 'output' => $output ]));
    }

    /**
     * Tests render when code is script.
     */
    public function testRenderWhenScript()
    {
        $content = new Content();
        $result  = '<head>Script code</head>';
        $output  = '<head></head>';
        $types   = [ 'GAnalytics' ];

        $this->renderer->expects($this->at(0))->method('getCodeType')
            ->willReturn('script');

        $this->tpl->expects($this->once())->method('fetch')
            ->willReturn('Script code');

        $this->assertEquals($result, $this->renderer->render($content, [ 'types' => $types, 'output' => $output ]));
    }

    /**
     * Tests render when code is fia.
     */
    public function testRenderWhenFia()
    {
        $content = new Content();
        $result  = '<figure class="op-tracker"><iframe>Fia code</iframe></figure>';
        $output  = '';
        $types   = [ 'GAnalytics' ];

        $this->renderer->expects($this->at(0))->method('getCodeType')
            ->willReturn('fia');

        $this->tpl->expects($this->once())->method('fetch')
            ->willReturn('Fia code');

        $this->assertEquals($result, $this->renderer->render($content, [ 'types' => $types, 'output' => $output ]));
    }


    /**
     * Tests render when no template is available.
     */
    public function testRenderWhenNoTemplate()
    {
        $output = '<head></head>';
        $types  = [ 'GAnalytics' ];

        $this->renderer->expects($this->at(0))->method('getCodeType')
            ->willReturn('script');

        $this->renderer->expects($this->at(1))->method('getRendererClass')
            ->with('GAnalytics');

        $this->tpl->expects($this->at(0))->method('fetch')
            ->will($this->throwException(new \Exception()));

        $this->renderer->render(null, [ 'types' => $types, 'output' => $output ]);
    }

    /**
     * Tests getCodeType when image.
     */
    public function testGetCodeTypeWhenCommandImage()
    {
        $renderer = new StatisticsRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getCodeType');
        $method->setAccessible(true);

        $this->global->expects($this->at(0))->method('getRequest')
            ->willReturn(null);

        $this->assertEquals(
            'image',
            $method->invokeArgs($renderer, [])
        );
    }

    /**
     * Tests getCodeType when image.
     */
    public function testGetCodeTypeWhenBackendImage()
    {
        $renderer = new StatisticsRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getCodeType');
        $method->setAccessible(true);

        $this->global->expects($this->at(0))->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects($this->at(0))->method('getUri')
            ->willReturn('/newsletters/save-contents');

        $this->assertEquals(
            'image',
            $method->invokeArgs($renderer, [])
        );
    }

    /**
     * Test getCodeType when amp page.
     */
    public function testGetCodeTypeWhenAmp()
    {
        $renderer = new StatisticsRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getCodeType');
        $method->setAccessible(true);

        $this->global->expects($this->at(0))->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects($this->once())->method('getUri')
            ->willReturn(
                'domain.com/article/category/slug/20180924122634000777.amp.html'
            );

        $this->assertEquals(
            'amp',
            $method->invokeArgs($renderer, [])
        );
    }

    /**
     * Tests getCodeType when script.
     */
    public function testGetCodeTypeWhenScript()
    {
        $renderer = new StatisticsRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getCodeType');
        $method->setAccessible(true);

        $this->global->expects($this->at(0))->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects($this->once())->method('getUri')
            ->willReturn(
                'domain.com/article/category/slug/20180924122634000777.html'
            );

        $this->assertEquals(
            'script',
            $method->invokeArgs($renderer, [])
        );
    }

    /**
     * Tests getCodeType when fia.
     */
    public function testGetCodeTypeWhenFia()
    {
        $renderer = new StatisticsRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getCodeType');
        $method->setAccessible(true);

        $this->global->expects($this->at(0))->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects($this->once())->method('getUri')
            ->willReturn(
                'domain.com/rss/facebook-instant-articles'
            );

        $this->assertEquals(
            'fia',
            $method->invokeArgs($renderer, [])
        );
    }

    /**
     * Tests getRendererClass.
     */
    public function testGetRendererClass()
    {
        $renderer = new StatisticsRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getRendererClass');
        $method->setAccessible(true);

        $this->assertEquals(
            'Frontend\Renderer\Statistics\GAnalyticsRenderer',
            get_class($method->invokeArgs($renderer, [ 'GAnalytics' ]))
        );
    }

    /**
     * Tests validate
     */
    public function testValidate()
    {
        $renderer = new StatisticsRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'validate');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($renderer, []));
    }

    /**
     * Tests getParameters when not empty content
     */
    public function testGetParametersWhenContent()
    {
        $content  = new Content();
        $renderer = new StatisticsRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getParameters');
        $method->setAccessible(true);

        $this->assertIsArray($method->invokeArgs($renderer, [ $content ]));
    }

    /**
     * Tests getParameter when empty content
     */
    public function testGetParameterWhenEmptyContent()
    {
        $content = null;

        $renderer = new StatisticsRenderer($this->container);
        $method   = new \ReflectionMethod($renderer, 'getParameters');
        $method->setAccessible(true);

        $this->assertIsArray($method->invokeArgs($renderer, [ $content ]));
    }
}
