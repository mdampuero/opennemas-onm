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

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('L10nRouteHelper')
            ->setMethods([ 'localizeUrl' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getRequestUri' ])
            ->getMock();

        $this->rs = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->security = $this->getMockBuilder('Security')
            ->setMethods([ 'hasExtension' ])
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
            case 'request_stack':
                return $this->rs;
        }

        return null;
    }

    /**
     * Tests smarty_outputfilter_meta_amphtml when AMP_MODULE extension is not
     * enabled.
     */
    public function testMetaAmpHtmlWithoutAmp()
    {
        $this->rs->expects($this->any())->method('getCurrentRequest')
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
        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->security->expects($this->once())->method('hasExtension')
            ->with('AMP_MODULE')->willReturn(true);

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('wibble.amp.html');

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_meta_amphtml($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_meta_amphtml when there is a valid content in
     * smarty.
     */
    public function testMetaAmpHtmlWhenContent()
    {
        $this->helper->expects($this->once())->method('localizeUrl')
            ->willReturn('/wibble/wubble');

        $this->request->expects($this->any())->method('getRequestUri')
            ->willReturn('wibble.html');

        $this->router->expects($this->any())->method('generate')
            ->willReturn('/wibble/wubble');

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->security->expects($this->once())->method('hasExtension')
            ->with('AMP_MODULE')->willReturn(true);

        $this->smarty->expects($this->exactly(3))->method('getTemplateVars')
            ->willReturn([
                'o_content' => json_decode(json_encode([
                    'category_name'     => 'gorp',
                    'pk_content'        => 145,
                    'created'           => '1999-12-31 23:59:59',
                    'content_type_name' => 'article',
                    'slug'              => 'foobar-thud'
                ]), false)
            ]);

        $this->assertEquals(
            '<html><head><link rel="amphtml" href="/wibble/wubble"/></head><body>Hello World!</body></html>',
            smarty_outputfilter_meta_amphtml(
                '<html><head></head><body>Hello World!</body></html>',
                $this->smarty
            )
        );
    }

    /**
     * Tests smarty_outputfilter_meta_amphtml when there is a content but it is
     * not an article.
     */
    public function testMetaAmpHtmlWhenNoArticle()
    {
        $this->rs->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->security->expects($this->once())->method('hasExtension')
            ->with('AMP_MODULE')->willReturn(true);

        $this->request->expects($this->any())->method('getRequestUri')
            ->willReturn('http://t.co/wibble.html');

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->smarty->expects($this->exactly(2))->method('getTemplateVars')
            ->willReturn([
                'o_content' => json_decode(json_encode([
                    'content_type_name' => 'opinion'
                ]), false)
            ]);

        $this->assertEquals($output, smarty_outputfilter_meta_amphtml($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_meta_amphtml when there is no content in
     * smarty.
     */
    public function testMetaAmpHtmlWhenNoContent()
    {
        $this->rs->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->security->expects($this->once())->method('hasExtension')
            ->with('AMP_MODULE')->willReturn(true);

        $this->request->expects($this->any())->method('getRequestUri')
            ->willReturn('http://t.co/wibble.html');

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->smarty->expects($this->once())->method('getTemplateVars')
            ->willReturn([]);

        $this->assertEquals(
            $output,
            smarty_outputfilter_meta_amphtml($output, $this->smarty)
        );
    }

    /**
     * Test smarty_outputfilter_meta_amphtml when there is no request in
     * progress.
     */
    public function testMetaAmpHtmlWhenNoRequest()
    {
        $this->rs->expects($this->any())
            ->method('getCurrentRequest')->willReturn(null);

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_meta_amphtml(
            $output,
            $this->smarty
        ));
    }
}
