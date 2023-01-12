<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Libs\Smarty;

/**
 * Defines test cases for SmartyAdsGeneratorTest class.
 */
class SmartyOutputFilterAdsGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/outputfilter.ads_generator.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->contentHelper = $this->getMockBuilder('Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods(['isInTime'])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->decorator = $this->getMockBuilder('Common\Core\Component\Url\UrlDecorator')
            ->disableOriginalConstructor()
            ->setMethods([ 'prefixUrl' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->templateAdmin = $this->getMockBuilder('TemplateAdmin')
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([
                'getContainer', 'getValue', 'hasValue', '__get', '__set'
            ])
            ->getMock();

        $this->helper = $this->getMockBuilder('AdvertisementHelper')
            ->setMethods([ 'isSafeFrameEnabled' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale' . uniqid())
            ->setMethods([ 'getTimeZone' ])->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->request_stack = $this->getMockBuilder('request_stack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->renderer = $this->getMockBuilder('AdvertisementRenderer')
            ->setMethods([
                'renderInlineHeaders', 'renderInlineInterstitial', 'getAdvertisements',
                'getInlineFormats', 'getRequested', 'getPositions', 'getExpiringAdvertisements', 'getXCacheFor'
            ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->request->headers = $this->getMockBuilder('Headers')
            ->setMethods([ 'all' ])
            ->getMock();

        $this->request_stack->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->decorator->expects($this->any())->method('prefixUrl')
            ->will($this->returnArgument(0));

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->contentHelper->expects($this->any())->method('isInTime')
            ->willReturn(true);

        $this->locale->expects($this->any())->method('getTimeZone')
            ->willReturn(new \DateTimeZone('UTC'));

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->smartySource = $this->getMockBuilder('Smarty_Template_Source')
            ->disableOriginalConstructor()
            ->getMock();

        $this->smarty->expects($this->any())
            ->method('__get')
            ->with($this->equalTo('source'))
            ->will($this->returnValue($this->smartySource));

        $this->smarty->source->resource = 'foo.tpl';
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.decorator.url':
                return $this->decorator;

            case 'core.helper.content':
                return $this->contentHelper;

            case 'core.security':
                return $this->security;

            case 'orm.manager':
                return $this->em;

            case 'frontend.renderer.advertisement':
                return $this->renderer;

            case 'core.helper.advertisement':
                return $this->helper;

            case 'core.locale':
                return $this->locale;

            case 'core.template.admin':
                return $this->templateAdmin;

            case 'router':
                return $this->router;

            case 'request_stack':
                return $this->request_stack;
        }

        return null;
    }

    /**
     * Test ads_generator with empty ads
     */
    public function testAdsGeneratorWithNoAds()
    {
        $output = '<html><head></head><body></body></html>';

        $this->renderer->expects($this->once())->method('getRequested')
            ->willReturn([]);

        $this->assertEquals($output, smarty_outputfilter_ads_generator(
            $output,
            $this->smarty
        ));
    }

    /**
     * Test ads_generator with amp
     */
    public function testAdsGeneratorWithAmp()
    {
        $ad     = new \Advertisement();
        $output = '<html><head></head><body></body></html>';

        $this->helper->expects($this->once())->method('isSafeFrameEnabled')
            ->willReturn(false);
        $this->renderer->expects($this->once())->method('getRequested')
            ->willReturn([$ad]);

        $this->renderer->expects($this->any())->method('getExpiringAdvertisements')
            ->willReturn([]);

        $this->smarty->expects($this->at(5))->method('getValue')
            ->with('ads_format')
            ->willReturn('amp');

        $this->renderer->expects($this->any())->method('getInlineFormats')
            ->willReturn([ 'amp', 'fia', 'newsletter' ]);

        $this->assertEquals($output, smarty_outputfilter_ads_generator(
            $output,
            $this->smarty
        ));
    }

    /**
     * Test ads_generator without safeframe (inline)
     */
    public function testAdsGeneratorWithoutSafeframe()
    {
        $ad = new \Advertisement();

        $this->helper->expects($this->once())->method('isSafeFrameEnabled')
            ->willReturn(false);
        $this->renderer->expects($this->at(0))->method('getRequested')
            ->willReturn([$ad]);

        $params = [
            'section'            => 'foo',
            'extension'          => 'bar',
            'advertisementGroup' => 'baz',
            'environment'        => 'dev'
        ];

        $this->smarty->expects($this->at(3))->method('getValue')
            ->with('app')
            ->willReturn($params);

        $content     = new \stdClass();
        $content->id = 123;

        $this->smarty->expects($this->at(5))->method('getValue')
            ->with('ads_format')
            ->willReturn(null);

        $this->renderer->expects($this->at(2))->method('getInlineFormats')
            ->willReturn([ 'amp', 'fia', 'newsletter' ]);

        $this->smarty->expects($this->at(6))->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->ds->expects($this->any())->method('get')
            ->with('ads_settings')
            ->willReturn([ 'lifetime_cookie' => 100 ]);

        $this->renderer->expects($this->at(3))->method('renderInlineHeaders')
            ->willReturn('<script>AdsHeaders</script>');

        $this->renderer->expects($this->at(4))->method('renderInlineInterstitial')
            ->willReturn('<script>Intersticial</script>');

        $this->renderer->expects($this->at(5))->method('getPositions')
            ->willReturn([]);

        $this->router->expects($this->once())->method('generate')
            ->with('api_v1_advertisements_list')
            ->willReturn('/ads/foo/bar/');

        $this->templateAdmin->expects($this->at(0))->method('fetch')
            ->with('advertisement/helpers/inline/js.tpl')
            ->willReturn('devices_template');

        $this->templateAdmin->expects($this->at(1))->method('fetch')
            ->with('advertisement/helpers/safeframe/js.tpl', [
                'debug' => 'true',
                'category' => 'foo',
                'extension' => 'bar',
                'advertisementGroup' => 'baz',
                'contentId' => 123,
                'lifetime' => 100,
                'positions' => '',
                'url' => '/ads/foo/bar/',
            ])
            ->willReturn('_onmaq_template');


        $output      = '<html><head></head><body></body></html>';
        $returnValue = "<html><head><script>AdsHeaders</script>_onmaq_template</head>"
        . "<body><script>Intersticial</script>\ndevices_template</body></html>";

        $this->assertEquals($returnValue, smarty_outputfilter_ads_generator(
            $output,
            $this->smarty
        ));
    }

    /**
     * Test ads_generator with safeframe
     */
    public function testAdsGeneratorWithSafeframe()
    {
        $ad = new \Advertisement();

        $this->helper->expects($this->once())->method('isSafeFrameEnabled')
            ->willReturn(true);
        $this->renderer->expects($this->once())->method('getAdvertisements')
            ->willReturn([$ad]);

        $params = [
            'section'            => 'foo',
            'extension'          => 'bar',
            'advertisementGroup' => 'baz',
            'environment'        => 'dev'
        ];

        $this->smarty->expects($this->at(3))->method('getValue')
            ->with('app')
            ->willReturn($params);

        $content     = new \stdClass();
        $content->id = 123;

        $this->renderer->expects($this->once())->method('getInlineFormats')
            ->willReturn([ 'amp', 'fia', 'newsletter' ]);

        $this->renderer->expects($this->once())->method('getPositions')
            ->willReturn([]);

        $this->smarty->expects($this->at(5))->method('getValue')
            ->with('ads_format')
            ->willReturn(null);

        $this->smarty->expects($this->at(6))->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->ds->expects($this->any())->method('get')
            ->with('ads_settings')
            ->willReturn([ 'lifetime_cookie' => 100 ]);

        $this->router->expects($this->once())->method('generate')
            ->with('api_v1_advertisements_list')
            ->willReturn('/ads/foo/bar/');

        $this->templateAdmin->expects($this->at(0))->method('fetch')
            ->with('advertisement/helpers/safeframe/js.tpl', [
                'debug' => 'true',
                'category' => 'foo',
                'extension' => 'bar',
                'advertisementGroup' => 'baz',
                'contentId' => 123,
                'lifetime' => 100,
                'positions' => '',
                'url' => '/ads/foo/bar/',
            ])
            ->willReturn('_onmaq_template');


        $output      = '<html><head></head><body></body></html>';
        $returnValue = "<html><head>_onmaq_template</head><body></body></html>";

        $this->assertEquals($returnValue, smarty_outputfilter_ads_generator(
            $output,
            $this->smarty
        ));
    }

    /**
     * Test ads_generator with safeframe without ads
     */
    public function testAdsGeneratorWithSafeframeWithoutAds()
    {
        $ad = new \Advertisement();

        $this->helper->expects($this->once())->method('isSafeFrameEnabled')
            ->willReturn(true);
        $this->renderer->expects($this->once())->method('getAdvertisements')
            ->willReturn([$ad]);

        $params = [
            'section'            => 'foo',
            'extension'          => 'bar',
            'advertisementGroup' => 'baz',
            'environment'        => 'dev'
        ];

        $this->smarty->expects($this->at(3))->method('getValue')
            ->with('app')
            ->willReturn($params);

        $content     = new \stdClass();
        $content->id = 123;

        $this->smarty->expects($this->at(5))->method('getValue')
            ->with('ads_format')
            ->willReturn(null);

        $this->renderer->expects($this->any())->method('getInlineFormats')
            ->willReturn([ 'amp', 'fia', 'newsletter' ]);

        $this->renderer->expects($this->once())->method('getPositions')
            ->willReturn([]);

        $this->smarty->expects($this->at(6))->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->ds->expects($this->any())->method('get')
            ->with('ads_settings')
            ->willReturn([ 'lifetime_cookie' => 100 ]);

        $output = '<html><head></head><body></body></html>';
        $this->assertEquals($output, smarty_outputfilter_ads_generator(
            $output,
            $this->smarty
        ));
    }
}
