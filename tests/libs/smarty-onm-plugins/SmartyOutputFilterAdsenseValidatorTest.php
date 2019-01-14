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
            ->setMethods([ 'getContainer', '__get' ])
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

            case 'orm.manager':
                return $this->em;

            case 'request_stack':
                return $this->requestStack;
        }

        return null;
    }

    /**
     * Test all cases for the conditional with regex pattern matching
     */
    public function testInvalidRegex()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('http://manager/test.com');

        $this->assertEquals($this->output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('http://managerws/test.com');

        $this->assertEquals($this->output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('http://share-by-email/test.com');

        $this->assertEquals($this->output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('http://sharrre/test.com');

        $this->assertEquals($this->output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('http://ads/test.com');

        $this->assertEquals($this->output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('http://comments/test.com');

        $this->assertEquals($this->output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('http://fb/instant-articles/test.com');

        $this->assertEquals($this->output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('http://test.amp.html');

        $this->assertEquals($this->output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test plugin when Ads module is activated
     */
    public function testAdsModuleActivated()
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

        $output = '<html><head>Hello World' . "\n"
            . '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>' . "\n"
            . '<script>' . "\n"
            . '(adsbygoogle = window.adsbygoogle || []).push({' . "\n"
            . 'google_ad_client: "ca-pub-999999999999999",' . "\n"
            . 'enable_page_level_ads: true' . "\n"
            . '});' . "\n"
            . '</script></head><body></body></html>';

        $this->assertEquals($output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));
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
            . '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>' . "\n"
            . '<script>' . "\n"
            . '(adsbygoogle = window.adsbygoogle || []).push({' . "\n"
            . 'google_ad_client: "ca-pub-7694073983816204",' . "\n"
            . 'enable_page_level_ads: true' . "\n"
            . '});' . "\n"
            . '</script></head><body></body></html>';

        $this->assertEquals($output, smarty_outputfilter_adsense_validator(
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

        $this->ds->expects($this->once())
            ->method('get')
            ->with('adsense_id')
            ->willReturn('');

        $this->security->expects($this->once())
            ->method('hasExtension')
            ->with('ADS_MANAGER')
            ->willReturn(true);

        $this->assertEquals($this->output, smarty_outputfilter_adsense_validator(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test plugin with valid adSense
     */
    public function testValidAdsenseCode()
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

        $output = '<html><head>Hello World' . "\n"
            . '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>' . "\n"
            . '<script>' . "\n"
            . '(adsbygoogle = window.adsbygoogle || []).push({' . "\n"
            . 'google_ad_client: "ca-pub-999999999999999",' . "\n"
            . 'enable_page_level_ads: true' . "\n"
            . '});' . "\n"
            . '</script></head><body></body></html>';

        $this->assertEquals($output, smarty_outputfilter_adsense_validator(
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
