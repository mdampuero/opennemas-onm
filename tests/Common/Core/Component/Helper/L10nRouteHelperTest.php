<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\L10nRouteHelper;
use Common\Core\Component\Helper\UrlHelper;

/**
 * Defines test cases for L10nRouteHelper class.
 */
class L10nRouteHelperTest extends \PHPUnit_Framework_TestCase
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

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->routeCollection->expects($this->any())->method('all')
            ->willReturn([
                'frog_plugh'  => $this->uroute,
                'corge_xyzzy' => $this->lroute
            ]);

        $this->router->expects($this->any())->method('getRouteCollection')
            ->willReturn($this->routeCollection);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->helper = new L10nRouteHelper($this->container);
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
     * Tests localizeUrl when the current route supports locale and the current
     * requests does not match the default locale.
     */
    public function testLocalizeUrlWithLocalizableRoute()
    {
        $property = new \ReflectionProperty($this->helper, 'routes');

        $property->setAccessible(true);
        $property->setValue($this->helper, [ 'foo_fubar_wibble' ]);

        $this->locale->expects($this->any())
            ->method('getLocale')->willReturn('es');
        $this->locale->expects($this->any())
            ->method('getRequestLocale')->willReturn('en');
        $this->locale->expects($this->any())
            ->method('getSlugs')->willReturn([ 'en', 'es' ]);

        $this->assertEquals(
            '/en/glork/fred',
            $this->helper->localizeUrl('/glork/fred', 'foo_fubar_wibble')
        );

        $this->assertEquals(
            'http://quux.waldo/en',
            $this->helper->localizeUrl(
                'http://quux.waldo',
                'foo_fubar_wibble',
                true
            )
        );

        $this->assertEquals(
            'http://quux.waldo/en/glork/fred',
            $this->helper->localizeUrl(
                'http://quux.waldo/glork/fred',
                'foo_fubar_wibble'
            )
        );

        $this->assertEquals(
            'http://quux.waldo:8080/en/glork/fred?garply=quux&xyzzy=flob',
            $this->helper->localizeUrl(
                'http://quux.waldo:8080/glork/fred?garply=quux&xyzzy=flob',
                'foo_fubar_wibble'
            )
        );

        $this->assertEquals(
            'http://glorp:corge@quux.waldo:8080/en/glork/fred?garply=quux&xyzzy=flob',
            $this->helper->localizeUrl(
                'http://glorp:corge@quux.waldo:8080/glork/fred?garply=quux&xyzzy=flob',
                'foo_fubar_wibble'
            )
        );
    }

    /**
     * Tests localiezeUrl when the locale for the current request matches the
     * default locale.
     */
    public function testLocalizeUrlWithDefaultLocale()
    {
        $this->locale->expects($this->any())
            ->method('getRequestLocale')->willReturn('en');
        $this->locale->expects($this->any())
            ->method('getLocale')->willReturn('en');

        $this->assertEquals(
            '/foo/bar',
            $this->helper->localizeUrl('/foo/bar', '')
        );

        $this->assertEquals(
            'http://example.com/foo/bar',
            $this->helper->localizeUrl('http://example.com/foo/bar', '')
        );
    }

    /**
     * Tests localizeUrl with an unknown locale.
     */
    public function testLocalizeUrlWithUnknownLocale()
    {
        $this->locale->expects($this->any())
            ->method('getLocale')->willReturn('en');
        $this->locale->expects($this->any())
            ->method('getRequestLocale')->willReturn('mumble');
        $this->locale->expects($this->any())
            ->method('getSlugs')->willReturn([ 'en', 'es' ]);

        $this->assertEquals(
            '/foo/bar',
            $this->helper->localizeUrl('/foo/bar', '')
        );

        $this->assertEquals(
            'http://example.com/foo/bar',
            $this->helper->localizeUrl('http://example.com/foo/bar', '')
        );
    }

    /**
     * Tests localizeUrl when the current route does not support locale.
     */
    public function testLocalizeUrlWithUnlocalizableRoute()
    {
        $property = new \ReflectionProperty($this->helper, 'routes');

        $property->setAccessible(true);
        $property->setValue($this->helper, [ 'foo_fubar_wibble' ]);

        $this->assertEquals(
            '/glork/fred',
            $this->helper->localizeUrl('/glork/fred', 'glork_fred')
        );
    }
}
