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
            ->setMethods([ 'getRequest', 'getTheme', 'getInstance' ])
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
            ->setMethods([ 'getUrl' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'getBaseUrl', 'getMediaShortPath' ])
            ->getMock();

        $this->theme = new Theme([ 'path' => '/themes/fred' ]);

        $this->ch->expects($this->any())->method('isReadyForPublish')
            ->willReturn(true);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->globals->expects($this->any())->method('getTheme')
            ->willReturn($this->theme);

        $this->globals->expects($this->any())->method('getInstance')
            ->willReturn($this->instance);

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

            default:
                return null;
        }
    }

    /**
     * Tests get_url when the provided parameter is a content.
     */
    public function testGetUrlForContent()
    {
        $this->ugh->expects($this->once())->method('getUrl')
            ->with(new Content());

        get_url(new Content());
    }

    /**
     * Tests get_image_dir when the theme is not configured in globals.
     */
    public function testGetImageDirWhenNoTheme()
    {
        $this->globals->expects($this->any())->method('getTheme')
            ->willReturn($this->theme);

        $this->instance->expects($this->any())->method('getBaseUrl')
            ->willReturn('https://opennemas.com');

        $this->assertEquals('/themes/fred/images', get_image_dir());
        $this->assertEquals('https://opennemas.com/themes/fred/images', get_image_dir(true));
    }

    /**
     * Tests get_image_dir when the theme is configured in globals.
     */
    public function testGetImageWhenTheme()
    {
        $this->globals->expects($this->any())->method('getTheme')
            ->willReturn($this->theme);

        $this->instance->expects($this->any())->method('getBaseUrl')
            ->willReturn('https://opennemas.com');

        $this->assertEquals('/themes/fred/images', get_image_dir());
        $this->assertEquals('https://opennemas.com/themes/fred/images', get_image_dir(true));
    }

    /**
     * Tests get_instance_media
     */
    public function testGetInstanceMedia()
    {
        $this->instance->expects($this->any())->method('getMediaShortPath')
            ->willReturn('media/opennemas');

        $this->instance->internal_name = 'opennemas';
        $this->assertEquals('media/opennemas', get_instance_media());
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
            '<link rel="stylesheet" href="/themes/fred/dist/style.css">'
            . '<script src="/themes/fred/dist/main.js"></script>',
            webpack()
        );
    }
}
