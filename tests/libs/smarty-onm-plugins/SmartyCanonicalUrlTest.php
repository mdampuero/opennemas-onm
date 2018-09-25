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

use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Defines test cases for SmartyUrl class.
 */
class SmartyCanonicalUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/outputfilter.canonical_url.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('UrlGeneratorHelper')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getRequestUri' ])
            ->getMock();

        $this->rs = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', 'getTemplateVars' ])
            ->getMock();

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'request_stack':
                return $this->rs;

            case 'core.helper.url_generator':
                return $this->helper;
        }

        return null;
    }

    /**
     * Tests smarty_outputfilter_canonical_url when requesting facebook instant
     * articles list.
     */
    public function testCanonicalUrlForFacebookInstantArticles()
    {
        $output = '<html><head></head><body></body></html>';

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/fb/instant-articles');

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->assertEquals($output, smarty_outputfilter_canonical_url(
            $output,
            $this->smarty
        ));
    }

    /**
     * Tests smarty_outputfilter_canonical_url where there is a content
     * assigned to the template.
     */
    public function testCanonicalUrlWhenContentInTemplate()
    {
        $output = '<html><head></head><body></body></html>';

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/thud/norf');

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->smarty->expects($this->exactly(2))->method('getTemplateVars')
            ->willReturn([ 'content' => 'grault' ]);

        $this->helper->expects($this->once())->method('generate')
            ->with('grault')->willReturn('http://console/grault');

        $this->assertContains(
            'http://console/grault',
            smarty_outputfilter_canonical_url(
                $output,
                $this->smarty
            )
        );
    }

    /**
     * Tests smarty_outputfilter_canonical_url where there is no content
     * assigned to the template.
     */
    public function testCanonicalUrlWhenNoContentInTemplate()
    {
        $output = '<html><head></head><body></body></html>';

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/thud/norf');

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->smarty->expects($this->once())->method('getTemplateVars')
            ->willReturn([]);

        $this->assertContains(
            'http://console/thud/norf',
            smarty_outputfilter_canonical_url(
                $output,
                $this->smarty
            )
        );
    }

    /**
     * Tests smarty_outputfilter_canonical_url where there is no request in
     * progress.
     */
    public function testCanonicalUrlWhenNoRequest()
    {
        $output = '<html><head></head><body></body></html>';

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn(null);

        $this->assertEquals($output, smarty_outputfilter_canonical_url(
            $output,
            $this->smarty
        ));
    }
}
