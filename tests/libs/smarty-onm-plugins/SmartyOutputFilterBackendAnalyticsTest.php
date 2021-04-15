<?php

namespace Tests\Libs\Smarty;

/**
 * Defines test cases for SmartyBackendAnalytics class.
 */
class SmartyOutputFilterBackendAnalyticsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/outputfilter.backend_analytics.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', 'getValue', '__get' ])
            ->getMock();

        $this->fr = $this->getMockBuilder('Frontend\Renderer\Renderer')
            ->disableOriginalConstructor()
            ->setMethods([ 'render' ])
            ->getMock();

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->server = $this->getMockBuilder('ServerBag')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->request->server = $this->server;

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

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
            case 'request_stack':
                return $this->requestStack;
            case 'frontend.renderer':
                return $this->fr;
        }

        return null;
    }

    /**
     * Tests smarty_outputfilter_backend_analytics when no request.
     */
    public function testBackendAnalyticsWhenNoRequest()
    {
        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')->willReturn(null);

        $output = '<!doctype html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_backend_analytics($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_backend_analytics when Admin Uri
     */
    public function testBackendAnalyticsWhenAdminUri()
    {
        $code = '<!-- Google Analytics -->'
        . '<script>'
        . '(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){'
        . '(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),'
        . 'm=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)'
        . '})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');'
        . 'ga(\'create\', \'UA-40838799-4\', { cookieDomain: \''
        . 'TESTSERVER\' });'
        . 'ga(\'send\', \'pageview\');'
        . '</script>'
        . '<!-- End Google Analytics -->';

        $output = '<!doctype html><head></head><body>Hello World!</body></html>';

        $result = str_replace('</head>', $code . '</head>', $output);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->request->expects($this->once())
            ->method('getUri')->willReturn('/admin');

        $this->container->expects($this->once())
            ->method('getParameter')
            ->with('backend_analytics.enabled')
            ->willReturn(true);

        $this->request->server->expects($this->once())
            ->method('get')
            ->with('SERVER_NAME')
            ->willReturn('TESTSERVER');

        $this->assertEquals($result, smarty_outputfilter_backend_analytics($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_backend_analytics when newsletter.
     */
    public function testBackendAnalyticsWhenNewsletter()
    {
        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->smarty->source->resource = 'newsletter';

        $output = '<!doctype html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_backend_analytics($output, $this->smarty));
    }
}
