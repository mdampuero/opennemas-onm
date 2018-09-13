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
 * Defines test cases for SmartyUrl class.
 */
class SmartyOutputFilterMetaAmpHtmlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/outputfilter.meta_amphtml.php';

        $this->security = $this->getMockBuilder('Security')
            ->setMethods([ 'hasExtension' ])
            ->getMock();

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('L10nRouteHelper')
            ->setMethods([ 'localizeUrl' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
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
     *
     * @return mixed
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'request':
                return $this->request;
            case 'router':
                return $this->router;
            case 'core.helper.l10n_route':
                return $this->helper;
            case 'core.security':
                return $this->security;
            case 'router':
                return $this->router;
            case 'request_stack':
                return $this->requestStack;
        }

        return null;
    }

    /**
     * Tests smarty_outputfilter_meta_amphtml when AMP_MODULE extension is not
     * enabled.
     */
    public function testMetaAmpHtmlWithoutAmp()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->security->expects($this->once())->method('hasExtension')
            ->with('AMP_MODULE')->willReturn(false);

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_meta_amphtml($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_meta_amphtml when AMP_MODULE extension is not
     * enabled.
     */
    public function testMetaAmpHtmlWhenAlreadyInAmp()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->security->expects($this->once())->method('hasExtension')
            ->with('AMP_MODULE')->willReturn(true);

        $this->request->expects($this->once())->method('getUri')
            ->willReturn('wibble.amp.html');

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_meta_amphtml($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_meta_amphtml when there is no content in
     * smarty.
     */
    public function testMetaAmpHtmlWhenNoContent()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->security->expects($this->any())->method('hasExtension')
            ->with('AMP_MODULE')->willReturn(true);

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('http://t.co/wibble.html');

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_meta_amphtml($output, $this->smarty));

        $this->smarty->tpl_vars = [ 'content' => json_decode(json_encode([ 'value' => null ]), false) ];
        $this->assertEquals($output, smarty_outputfilter_meta_amphtml($output, $this->smarty));

        $this->smarty->tpl_vars = [ 'content' => json_decode(json_encode([
            'value' => json_decode(json_encode([ 'content_type_name' => 'opinion' ]), false)
        ]), false) ];
        $this->assertEquals($output, smarty_outputfilter_meta_amphtml($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_meta_amphtml when there is a valid content in
     * smarty.
     */
    public function testMetaAmpHtmlWhenContent()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->security->expects($this->any())->method('hasExtension')
            ->with('AMP_MODULE')->willReturn(true);

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('wibble.html');

        $this->router->expects($this->any())->method('generate')
            ->willReturn('/wibble/wubble');

        $this->helper->expects($this->at(0))->method('localizeUrl')
            ->willReturn('/wibble/wubble');

        $this->helper->expects($this->at(1))->method('localizeUrl')
            ->willReturn('/es/wibble/wubble');

        $this->smarty->tpl_vars = [ 'content' => json_decode(json_encode([
            'value' => json_decode(json_encode([
                    'category_name'     => 'gorp',
                    'pk_content'        => 145,
                    'created'           => '1999-12-31 23:59:59',
                    'content_type_name' => 'article',
                    'slug'              => 'foobar-thud'
            ]), false)
        ]), false) ];

        $this->assertEquals(
            '<html><head><link rel="amphtml" href="/wibble/wubble"/></head><body>Hello World!</body></html>',
            smarty_outputfilter_meta_amphtml(
                '<html><head></head><body>Hello World!</body></html>',
                $this->smarty
            )
        );

        $this->assertEquals(
            '<html><head><link rel="amphtml" href="/es/wibble/wubble"/></head><body>Hello World!</body></html>',
            smarty_outputfilter_meta_amphtml(
                '<html><head></head><body>Hello World!</body></html>',
                $this->smarty
            )
        );
    }

    /**
     * Test plugin with no currentRequest
     */
    public function testEmptyResturnIfNoRequest()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn(null);

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_meta_amphtml(
            $output,
            $this->smarty
        ));
    }
}
