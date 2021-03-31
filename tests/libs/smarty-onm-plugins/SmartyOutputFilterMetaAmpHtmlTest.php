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

        $this->cache = $this->getMockBuilder('Cache')
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->conn = $this->getMockBuilder('DatabaseConnection')
            ->setMethods([ 'fetchAll' ])
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

        $this->urlGenerator = $this->getMockBuilder('UrlGenerator')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->security = $this->getMockBuilder('Security')
            ->setMethods([ 'hasExtension' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', 'getValue' ])
            ->getMock();

        $this->conn->expects($this->any())->method('fetchAll')
            ->willReturn([]);
        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->smartySource = $this->getMockBuilder('Smarty_Template_Source')
            ->disableOriginalConstructor()
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
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
            case 'cache':
                return $this->cache;
            case 'request':
                return $this->request;
            case 'core.helper.url_generator':
                return $this->urlGenerator;
            case 'core.helper.l10n_route':
                return $this->helper;
            case 'core.security':
                return $this->security;
            case 'dbal_connection':
                return $this->conn;
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
        $content = new \Content();
        $content->load([
            'pk_content'        => 27616,
            'category_slug'     => 'gorp',
            'created'           => '1999-12-31 23:59:59',
            'content_type_name' => 'opinion',
            'slug'              => 'foobar-thud'
        ]);

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->smarty->expects($this->once())->method('getValue')
            ->willReturn($content);

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
        $content = new \Content();
        $content->load([
            'pk_content'        => 27616,
            'category_slug'     => 'gorp',
            'created'           => '1999-12-31 23:59:59',
            'content_type_name' => 'opinion',
            'slug'              => 'foobar-thud'
        ]);

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->smarty->expects($this->once())->method('getValue')
            ->willReturn($content);

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

        $this->urlGenerator->expects($this->any())->method('generate')
            ->willReturn('/wibble/wubble');

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->security->expects($this->once())->method('hasExtension')
            ->with('AMP_MODULE')->willReturn(true);

        $content = new \Content();
        $content->load([
            'pk_content'        => 27616,
            'category_slug'     => 'gorp',
            'created'           => '1999-12-31 23:59:59',
            'content_type_name' => 'opinion',
            'slug'              => 'foobar-thud'
        ]);

        $this->smarty->expects($this->once())->method('getValue')
            ->willReturn($content);

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
    public function testMetaAmpHtmlWhenNoValidContentType()
    {
        $this->rs->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request->expects($this->any())->method('getRequestUri')
            ->willReturn('http://t.co/wibble.html');

        $output = '<html><head></head><body>Hello World!</body></html>';

        $content = new \Content();
        $content->load([
            'content_type_name' => 'invalidtype'
        ]);

        $this->smarty->expects($this->once())->method('getValue')
            ->willReturn($content);

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

        $output = '<html><head></head><body>Hello World!</body></html>';

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

    /**
     * Tests smarty_outputfilter_meta_amphtml when there is an error.
     */
    public function testMetaAmpHtmlWhenError()
    {
        $this->request->expects($this->any())->method('getRequestUri')
            ->willReturn('wibble.html');

        $this->urlGenerator->expects($this->any())->method('generate')
            ->willReturn('/wibble/wubble');

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->security->expects($this->once())->method('hasExtension')
            ->with('AMP_MODULE')->willReturn(true);

        $content = new \Content();
        $content->load([
            'pk_content'        => 27616,
            'category_slug'     => 'gorp',
            'created'           => '1999-12-31 23:59:59',
            'content_type_name' => 'opinion',
            'slug'              => 'foobar-thud'
            ]);

        $this->smarty->expects($this->once())->method('getValue')
            ->willReturn($content);

        $this->helper->expects($this->once())->method('localizeUrl')
            ->will($this->throwException(new \Exception()));

        $this->assertEquals(
            '<html><head></head><body>Hello World!</body></html>',
            smarty_outputfilter_meta_amphtml(
                '<html><head></head><body>Hello World!</body></html>',
                $this->smarty
            )
        );
    }
}
