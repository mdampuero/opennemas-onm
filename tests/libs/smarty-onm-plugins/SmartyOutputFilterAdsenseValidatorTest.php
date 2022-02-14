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
 * Defines test cases for SmartyAdsenseValidatorTest class.
 */
class SmartyOutputFilterAdsenseValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/outputfilter.adsense_validator.php';

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', '__get', 'getValue' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->sh = $this->getMockBuilder('Common\Core\Component\Helper\SubscriptionHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getToken', 'hasAdvertisements' ])
            ->getMock();

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->security = $this->getMockBuilder('Security')
            ->setMethods([ 'hasExtension' ])
            ->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->output = '<html><head>Hello World</head><body></body></html>';

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
            case 'core.security':
                return $this->security;

            case 'core.helper.subscription':
                return $this->sh;

            case 'orm.manager':
                return $this->em;

            case 'request_stack':
                return $this->requestStack;
        }

        return null;
    }

    /**
     * Test plugin when Ads module is not activated
     */
    public function testAdsModuleNotActivated()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->ds->expects($this->once())
            ->method('get')
            ->with('adsense_id')
            ->willReturn('ca-pub-999999999999999');

        $this->security->expects($this->once())
            ->method('hasExtension')
            ->with('ADS_MANAGER')
            ->willReturn(false);

        $output = '<html><head>Hello World' . "\n"
            . '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='
            . 'ca-pub-7694073983816204" crossorigin="anonymous"></script></head><body></body></html>';

        $this->assertEquals($output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test plugin when Ads module is activated and not a content
     */
    public function testAdsModuleActivatedAndNotContent()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->ds->expects($this->once())
            ->method('get')
            ->with('adsense_id')
            ->willReturn('ca-pub-999999999999999');

        $this->security->expects($this->once())
            ->method('hasExtension')
            ->with('ADS_MANAGER')
            ->willReturn(true);

        $this->smarty->expects($this->once())->method('getValue')
            ->with('content')
            ->willReturn(null);

        $output = '<html><head>Hello World' . "\n"
            . '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='
            . 'ca-pub-999999999999999" crossorigin="anonymous"></script></head><body></body></html>';

        $this->assertEquals($output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test plugin when Ads module is activated and is a content with ads
     */
    public function testAdsModuleActivatedAndContentWithAds()
    {
        $content = new \Content();

        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->ds->expects($this->once())
            ->method('get')
            ->with('adsense_id')
            ->willReturn('ca-pub-999999999999999');

        $this->security->expects($this->once())
            ->method('hasExtension')
            ->with('ADS_MANAGER')
            ->willReturn(true);

        $this->smarty->expects($this->once())->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->sh->expects($this->once())->method('getToken')
            ->with($content)
            ->willReturn('token');

        $this->sh->expects($this->once())->method('hasAdvertisements')
            ->with('token')
            ->willReturn(true);

        $output = '<html><head>Hello World' . "\n"
            . '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client='
            . 'ca-pub-999999999999999" crossorigin="anonymous"></script></head><body></body></html>';

        $this->assertEquals($output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test plugin when Ads module is activated and is a content without ads
     */
    public function testAdsModuleActivatedAndContentWithoutAds()
    {
        $content = new \Content();

        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->ds->expects($this->once())
            ->method('get')
            ->with('adsense_id')
            ->willReturn('ca-pub-999999999999999');

        $this->security->expects($this->once())
            ->method('hasExtension')
            ->with('ADS_MANAGER')
            ->willReturn(true);

        $this->smarty->expects($this->once())->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->sh->expects($this->once())->method('getToken')
            ->with($content)
            ->willReturn('token');

        $this->sh->expects($this->once())->method('hasAdvertisements')
            ->with('token')
            ->willReturn(false);

        $this->assertEquals($this->output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test plugin when adSense code is invalid
     */
    public function testInvalidAdsenseCode()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->security->expects($this->once())
            ->method('hasExtension')
            ->with('ADS_MANAGER')
            ->willReturn(true);

        $this->ds->expects($this->once())
            ->method('get')
            ->with('adsense_id')
            ->willReturn(null);

        $this->assertEquals($this->output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test plugin with no currentRequest
     */
    public function testEmptyResturnIfNoRequest()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn(null);

        $this->assertEquals($this->output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));
    }
}
