<?php

namespace Common\Core\Component\Url;

use Common\Core\Component\Helper\UrlHelper;
use Common\Core\Component\Url\UrlLocalizerDecorator;

/**
 * Defines test cases for L10nRouteHelper class.
 */
class UrlLocalizerDecoratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->uroute = $this->getMockBuilder('Route')
            ->setMethods([ 'getOption' ])
            ->getMock();

        $this->lroute = $this->getMockBuilder('Route')
            ->setMethods([ 'getOption' ])
            ->getMock();

        $this->uroute->expects($this->any())->method('getOption')
            ->with('l10n')->willReturn(false);
        $this->lroute->expects($this->any())->method('getOption')
            ->with('l10n')->willReturn(true);

        $this->locale = $this->getMockBuilder('Locale')
            ->setMethods([ 'getLocale', 'getRequestLocale', 'getSlugs' ])
            ->getMock();

        $this->routeCollection = $this->getMockBuilder('RouteCollection')
            ->setMethods([ 'all' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'getRouteCollection' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->routeCollection->expects($this->any())->method('all')
            ->willReturn([
                'frog_plugh'  => $this->uroute,
                'corge_xyzzy' => $this->lroute
            ]);

        $this->router->expects($this->any())->method('getRouteCollection')
            ->willReturn($this->routeCollection);

        $this->urlHelper = $this->getMockBuilder('Common\Core\Component\Helper\UrlHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'parse', 'unparse' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->helper = new UrlLocalizerDecorator($this->container, $this->urlHelper);
    }

    /**
     * Returns a service mock basing on the parameter.
     *
     * @param string $name The service name.
     *
     * @return mixed The service mock.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.helper.url':
                return new UrlHelper();

            case 'core.locale':
                return $this->locale;

            case 'router':
                return $this->router;
        }
    }

    /**
     * Tests getLocalizableRoutes.
     */
    public function testGetLocalizableRoutes()
    {
        $this->assertEquals(
            [ 'corge_xyzzy' ],
            $this->helper->getLocalizableRoutes()
        );
    }

    /**
     * Tests prefixUrl when the current route supports locale and the current
     * requests does not match the default locale.
     */
    public function testPrefixUrlWithLocalizableRoute()
    {
        $property = new \ReflectionProperty($this->helper, 'routes');

        $property->setAccessible(true);
        $property->setValue($this->helper, [ 'foo_fubar_wibble' ]);

        $this->locale->expects($this->any())->method('getLocale')
            ->willReturn('es_ES');
        $this->locale->expects($this->any())
            ->method('getRequestLocale')->willReturn('en_US');
        $this->locale->expects($this->any())->method('getSlugs')
            ->willReturn([ 'es_ES' => 'es', 'en_US' => 'en' ]);

        $this->urlHelper->expects($this->at(0))->method('parse')
            ->willReturn([ 'path' => '/glork/fred' ]);

        $this->urlHelper->expects($this->at(1))->method('unparse')
            ->with([ 'path' => '/en/glork/fred' ])
            ->willReturn('/en/glork/fred');

        $this->assertEquals(
            '/en/glork/fred',
            $this->helper->prefixUrl('/glork/fred', 'foo_fubar_wibble')
        );

        $this->urlHelper->expects($this->at(0))->method('parse')
            ->willReturn([ 'domain' => 'http://quux.waldo', 'path' => '' ]);

        $this->urlHelper->expects($this->at(1))->method('unparse')
            ->with([ 'domain' => 'http://quux.waldo', 'path' => '/en' ])
            ->willReturn('http://quux.waldo/en');

        $this->assertEquals(
            'http://quux.waldo/en',
            $this->helper->prefixUrl(
                'http://quux.waldo',
                'foo_fubar_wibble',
                true
            )
        );

        $this->urlHelper->expects($this->at(0))->method('parse')
            ->willReturn([ 'domain' => 'http://quux.waldo', 'path' => '/glork/fred' ]);

        $this->urlHelper->expects($this->at(1))->method('unparse')
            ->with([ 'domain' => 'http://quux.waldo', 'path' => '/en/glork/fred' ])
            ->willReturn('http://quux.waldo/en/glork/fred');

        $this->assertEquals(
            'http://quux.waldo/en/glork/fred',
            $this->helper->prefixUrl(
                'http://quux.waldo/glork/fred',
                'foo_fubar_wibble'
            )
        );

        $this->urlHelper->expects($this->at(0))->method('parse')
            ->willReturn([
                'domain' => 'http://quux.waldo',
                'path'   => '/glork/fred?garply=quux&xyzzy=flob',
                'port'   => ':8080'
            ]);

        $this->urlHelper->expects($this->at(1))->method('unparse')
            ->with([
                'domain' => 'http://quux.waldo',
                'path' => '/en/glork/fred?garply=quux&xyzzy=flob',
                'port'   => ':8080'
            ])
            ->willReturn('http://quux.waldo:8080/en/glork/fred?garply=quux&xyzzy=flob');

        $this->assertEquals(
            'http://quux.waldo:8080/en/glork/fred?garply=quux&xyzzy=flob',
            $this->helper->prefixUrl(
                'http://quux.waldo:8080/glork/fred?garply=quux&xyzzy=flob',
                'foo_fubar_wibble'
            )
        );
    }

    /**
     * Tests localiezeUrl when the locale for the current request matches the
     * default locale.
     */
    public function testPrefixUrlWithDefaultLocale()
    {
        $this->locale->expects($this->any())
            ->method('getRequestLocale')->willReturn('en');
        $this->locale->expects($this->any())
            ->method('getLocale')->willReturn('en');

        $this->assertEquals(
            '/foo/bar',
            $this->helper->prefixUrl('/foo/bar', '')
        );

        $this->assertEquals(
            'http://example.com/foo/bar',
            $this->helper->prefixUrl('http://example.com/foo/bar', '')
        );
    }

    /**
     * Tests prefixUrl with an unknown locale.
     */
    public function testPrefixUrlWithUnknownLocale()
    {
        $this->locale->expects($this->any())
            ->method('getLocale')->willReturn('en_US');
        $this->locale->expects($this->any())
            ->method('getRequestLocale')->willReturn('mumble');
        $this->locale->expects($this->any())
            ->method('getSlugs')->willReturn([ 'en', 'es' ]);

        $this->assertEquals(
            '/foo/bar',
            $this->helper->prefixUrl('/foo/bar', '')
        );

        $this->assertEquals(
            'http://example.com/foo/bar',
            $this->helper->prefixUrl('http://example.com/foo/bar', '')
        );
    }

    /**
     * Tests prefixUrl when the current route does not support locale.
     */
    public function testPrefixUrlWithUnlocalizableRoute()
    {
        $property = new \ReflectionProperty($this->helper, 'routes');

        $property->setAccessible(true);
        $property->setValue($this->helper, [ 'foo_fubar_wibble' ]);

        $this->assertEquals(
            '/glork/fred',
            $this->helper->prefixUrl('/glork/fred', 'glork_fred')
        );
    }
}
