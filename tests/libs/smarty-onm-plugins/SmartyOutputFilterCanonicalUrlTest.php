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
use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for SmartyUrl class.
 */
class SmartyOutputFilterCanonicalUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/outputfilter.canonical_url.php';

        $this->instance = new Instance([
            'no_redirect_domain' => '',
            'domains'           => [ 'onm.com', 'opennemas.com', 'example.org' ],
            'main_domain'       => 2
        ]);

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('UrlGeneratorHelper')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getHost', 'getRequestUri', 'getUri' ])
            ->getMock();

        $this->rs = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([
                'getContainer', 'getValue', 'hasValue', '__set', '__get'
            ])->getMock();

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
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'request_stack':
                return $this->rs;

            case 'core.helper.url_generator':
                return $this->helper;

            case 'core.instance':
                return $this->instance;
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
            ->willReturn('/rss/facebook-instant-articles');

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->assertEquals($output, smarty_outputfilter_canonical_url(
            $output,
            $this->smarty
        ));
    }

    /**
     * Tests smarty_outputfilter_canonical_url when rendering a newsletter.
     */
    public function testCanonicalUrlForNewsletter()
    {
        $this->smarty->source->resource = 'newsletter/newsletter.tpl';

        $output = '<html><head></head><body></body></html>';

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->assertEquals($output, smarty_outputfilter_canonical_url(
            $output,
            $this->smarty
        ));
    }

    /**
     * Tests smarty_outputfilter_canonical_url when the canonical URL is already
     * generated and assigned to Smarty.
     */
    public function testCanonicalUrlWhenCanonical()
    {
        $output = '<html><head></head><body></body></html>';

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/thud/norf');

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->smarty->expects($this->at(2))->method('hasValue')
            ->with('o_canonical')->willReturn(true);
        $this->smarty->expects($this->at(3))->method('getValue')
            ->with('o_canonical')->willReturn('http://grault.corge');

        $this->smarty->expects($this->at(5))->method('getValue')
            ->with('o_content')->willReturn(null);

        $this->assertContains(
            'http://grault.corge',
            smarty_outputfilter_canonical_url(
                $output,
                $this->smarty
            )
        );
    }

    /**
     * Tests smarty_outputfilter_canonical_url whene there is no canonical URL
     * generated nor assigned to Smarty.
     */
    public function testCanonicalUrlWhenNoCanonical()
    {
        $output = '<html><head></head><body></body></html>';

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/thud/norf');

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('http://console/thud/norf?page=1');

        $this->smarty->expects($this->once())->method('hasValue')
            ->with('o_canonical')->willReturn(false);

        $this->smarty->expects($this->once())->method('getValue')
            ->with('o_content')->willReturn(null);

        $this->assertContains(
            'http://console/thud/norf',
            smarty_outputfilter_canonical_url(
                $output,
                $this->smarty
            )
        );
    }

    /**
     * Tests smarty_outputfilter_canonical_url when content has canonical
     */
    public function testCanonicalUrlWhenContentHasCanonical()
    {
        $output  = '<html><head></head><body></body></html>';
        $content = new Content(
            [
                'pk_content'   => 1,
                'canonicalurl' => 'https://opennemas.com'
            ]
        );

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/thud/norf');

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('http://console/thud/norf?page=1');

        $this->smarty->expects($this->at(2))->method('hasValue')
            ->with('o_canonical')->willReturn(false);

        $this->smarty->expects($this->at(4))->method('getValue')
            ->with('o_content')->willReturn($content);

        $this->assertContains(
            'https://opennemas.com',
            smarty_outputfilter_canonical_url(
                $output,
                $this->smarty
            )
        );
    }

    /**
     * Tests smarty_outputfilter_canonical_url when instance has no
     * redirect domain allowed
     */
    public function testCanonicalUrlWhenInstanceNoRedirectDomain()
    {
        $output = '<html><head></head><body></body></html>';

        $this->instance->no_redirect_domain = 1;

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/thud/norf');

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('https://console/thud/norf?page=1');

        $this->smarty->expects($this->at(2))->method('hasValue')
            ->with('o_canonical')->willReturn(false);

        $this->request->expects($this->once())->method('getHost')
            ->willReturn('console');

        $this->smarty->expects($this->at(4))->method('getValue')
            ->with('o_content')->willReturn(null);

        $this->assertContains(
            'https://opennemas.com',
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
