<?php

namespace Tests\Common\Core\Functions;

use Common\Model\Entity\Instance;
use Common\Model\Entity\Content;
use Common\Model\Entity\Theme;

/**
 * Defines test cases for helper functions.
 */
class HelperFunctionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->getMock();

        $this->ch = $this->getMockBuilder('Common\Core\Helper\ContentHelper')
            ->setMethods([ 'isReadyForPublish' ])
            ->getMock();

        $this->globals = $this->getMockBuilder('Common\Core\Component\Core\GlobalVariables')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRequest', 'getTheme' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer', 'getEnvironment' ])
            ->getMock();

        $this->lrh = $this->getMockBuilder('Common\Core\Component\Helper\L10nRouteHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'localizeUrl' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPort', 'getSchemeAndHttpHost' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->ugh = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'getBaseUrl' ])
            ->getMock();

        $this->theme = $this->getMockBuilder('Theme')->getMock();

        $this->theme->path = '/theme/fred';

        $this->ch->expects($this->any())->method('isReadyForPublish')
            ->willReturn(true);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->globals->expects($this->any())->method('getTheme')
            ->willReturn(new Theme([ 'path' => 'wibble/bar' ]));

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Returns a mocked service based on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.globals':
                return $this->globals;

            case 'core.helper.content':
                return $this->ch;

            case 'core.helper.l10n_route':
                return $this->lrh;

            case 'core.helper.url_generator':
                return $this->ugh;

            case 'kernel':
                return $this->kernel;

            case 'router':
                return $this->router;

            case 'core.instance':
                return $this->instance;

            case 'core.theme':
                return $this->theme;

            default:
                return null;
        }
    }

    /**
     * Tests get_url when the provided parameter is a content.
     */
    public function testGetUrlForContent()
    {
        $item = new Content();

        $this->ugh->expects($this->once())->method('generate')
            ->with($item, [ 'absolute' => false, '_format' => null ])
            ->willReturn('/grault/fred');

        $this->lrh->expects($this->once())->method('localizeUrl')
            ->with('/grault/fred')->willReturn('/en/grault/fred');

        $this->assertEquals('/en/grault/fred', get_url($item, [ 'flob' => 'grault' ]));
    }

    /**
     * Tests get_url when the provided content has a related content (an array
     * with item, type, caption and position).
     */
    public function testGetUrlForRelatedContent()
    {
        $item = new Content();

        $this->ugh->expects($this->once())->method('generate')
            ->with($item, [ 'absolute' => false, '_format' => null ])
            ->willReturn('/grault/fred');

        $this->lrh->expects($this->once())->method('localizeUrl')
            ->with('/grault/fred')->willReturn('/en/grault/fred');

        $this->assertEquals(
            '/en/grault/fred',
            get_url([ 'item' => $item ], [ 'flob' => 'grault' ])
        );
    }

    /**
     * Tests get_url for contents from external data source.
     */
    public function testGetUrlForExternalContent()
    {
        $item = new Content();

        $item->externalUri = '/xyzzy/plugh';

        $this->assertEquals('/xyzzy/plugh', get_url($item));
    }

    /**
     * Tests get_url when the provided parameter is a route name.
     */
    public function testGetUrlForRoute()
    {
        $this->router->expects($this->exactly(2))->method('generate')
            ->with('wibble', [ 'flob' => 'grault' ])
            ->willReturn('/grault/fred');

        $this->lrh->expects($this->exactly(2))->method('localizeUrl')
            ->with('/grault/fred')->willReturn('/en/grault/fred');

        $this->assertEquals('/en/grault/fred', get_url('wibble', [
            'flob' => 'grault'
        ]));

        $this->assertEquals('/en/grault/fred', get_url('wibble', [
            '_absolute' => true,
            'flob' => 'grault'
        ]));
    }

    /**
     * Tests get_url when the provided parameter is empty.
     */
    public function testGetUrlWhenEmpty()
    {
        $this->assertEmpty(get_url(null));
    }

    /**
     * Tests get_url when an error is thrown.
     */
    public function testGetUrlWhenError()
    {
        $this->router->expects($this->once())->method('generate')
            ->will($this->throwException(new \Exception()));

        $this->assertEmpty(get_url('garply'));
    }

    /**
     * Tests getImageDir
     */
    public function testGetImageDir()
    {
        $this->instance->expects($this->any())->method('getBaseUrl')
            ->willReturn('https://opennemas.com');

        $this->assertEquals('/theme/fred/images', getImageDir());
        $this->assertEquals('https://opennemas.com/theme/fred/images', getImageDir(true));

        $this->theme = null;
        $this->assertEquals(null, getImageDir());
        $this->assertEquals(null, getImageDir(true));
    }

    /**
     * Tests has_flag.
     */
    public function testHasFlag()
    {
        $this->assertFalse(has_flag('wobble', null));
        $this->assertFalse(has_flag('wobble', []));
        $this->assertFalse(has_flag('wobble', 'gorp'));
        $this->assertTrue(has_flag('wobble', 'wobble'));
        $this->assertFalse(has_flag('wobble', [ 'fubar' ]));
        $this->assertTrue(has_flag('wobble', [ 'wobble' ]));
    }

    /**
     * Tests parse_flags.
     */
    public function testParseFlags()
    {
        $this->assertEquals([], parse_flags(null, 'garply'));
        $this->assertEquals([], parse_flags([], 'garply'));
        $this->assertEquals([ 'wobble' ], parse_flags('wobble', 'fubar'));
        $this->assertEquals([ 'wobble' ], parse_flags([ 'wobble' ], 'fubar'));
        $this->assertEquals([ 'wobble' ], parse_flags('fubar-wobble', 'fubar'));
        $this->assertEquals([ 'wobble' ], parse_flags([ 'fubar-wobble' ], 'fubar'));
        $this->assertEquals([ 'flob', 'wobble' ], parse_flags([ 'flob', 'fubar-wobble' ], 'fubar'));
    }

    /**
     * Tests webpack for dev environment when there is no request.
     */
    public function testWebpackForDevWhenNoRequest()
    {
        $this->kernel->expects($this->once())->method('getEnvironment')
            ->willReturn('dev');

        $this->globals->expects($this->once())->method('getRequest')
            ->willReturn(null);

        $this->assertEquals(
            '<script src="/main.js"></script>',
            webpack()
        );
    }

    /**
     * Tests webpack for dev environment there is a request in progress.
     */
    public function testWebpackForDevWhenRequest()
    {
        $this->kernel->expects($this->once())->method('getEnvironment')
            ->willReturn('dev');

        $this->globals->expects($this->once())->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects($this->once())->method('getPort')
            ->willReturn('8080');
        $this->request->expects($this->once())->method('getSchemeAndHttpHost')
            ->willReturn('http://grault:8080');

        $this->assertEquals(
            '<script src="http://grault:9000/main.js"></script>',
            webpack()
        );
    }

    /**
     * Tests webpack for prod environment.
     */
    public function testWebpackForProd()
    {
        $this->kernel->expects($this->once())->method('getEnvironment')
            ->willReturn('prod');

        $this->assertEquals(
            '<link rel="stylesheet" href="/wibble/bar/dist/style.css">'
            . '<script src="/wibble/bar/dist/main.js"></script>',
            webpack()
        );
    }
}
