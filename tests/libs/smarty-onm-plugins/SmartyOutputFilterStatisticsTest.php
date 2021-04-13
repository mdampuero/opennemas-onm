<?php

namespace Tests\Libs\Smarty;

/**
 * Defines test cases for SmartyStatistics class.
 */
class SmartyOutputFilterStatisticsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/outputfilter.statistics.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
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

        $this->headers = $this->getMockBuilder('HeaderBag')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->request->headers = $this->headers;

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
     * Tests smarty_outputfilter_statistics when no request.
     */
    public function testStatisticsWhenNoRequest()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn(null);

        $output = '<!doctype html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_statistics($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_statistics when no HTML page.
     */
    public function testStatisticsWhenNoHTMLPage()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $output = '<div class="foobar"></div>';

        $this->assertEquals($output, smarty_outputfilter_statistics($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_statistics.
     */
    public function testStatistics()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $output      = '<!DocType html><head></head><body>Hello World!</body></html>';
        $returnvalue = '<!DocType html><head>foo-bar-baz</head><body>Hello World!</body></html>';

        $this->fr->expects($this->any())->method('render')
            ->with(null, [
                'types'  => [ 'Default', 'Chartbeat', 'Comscore', 'Ojd', 'GAnalytics' ],
                'output' => $output,
            ])
            ->willReturn($returnvalue);

        $this->assertEquals(
            $returnvalue,
            smarty_outputfilter_statistics($output, $this->smarty)
        );
    }
}
