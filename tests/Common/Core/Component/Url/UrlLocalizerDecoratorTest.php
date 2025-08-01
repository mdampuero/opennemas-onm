<?php

namespace Common\Core\Component\Url;

use Common\Core\Component\Helper\UrlHelper;
use Common\Core\Component\Url\UrlLocalizerDecorator;

/**
 * Defines test cases for UrlLocalizerDecorator class.
 */
class UrlLocalizerDecoratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->routeHelper = $this->getMockBuilder('Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'isRouteLocalizable' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale')
            ->setMethods([ 'getLocale', 'getRequestLocale', 'getSlugs' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->disableOriginalConstructor()
            ->setMethods([ 'match' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->urlHelper = $this->getMockBuilder('Common\Core\Component\Helper\UrlHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'parse', 'unparse' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->localizer = new UrlLocalizerDecorator($this->container, $this->urlHelper);
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

            case 'core.helper.l10n_route':
                return $this->routeHelper;

            case 'router':
                return $this->router;
        }
    }

    /**
     * Tests prefixUrl when there is an inner decorator.
     */
    public function testPrefixUrlWhenInner()
    {
        $decorator = $this->getMockBuilder('Common\Core\Component\Url\UrlDecorator')
            ->disableOriginalConstructor()
            ->setMethods([ 'prefixUrl' ])
            ->getMock();

        $localizer = new UrlLocalizerDecorator($this->container, $this->urlHelper, $decorator);

        $decorator->expects($this->once())->method('prefixUrl')
            ->with('/foo/baz')
            ->willReturn('/foo/baz');

        $this->urlHelper->expects($this->once())->method('parse')
            ->with('/foo/baz')
            ->willReturn([ 'path' => '/foo/baz' ]);

        $this->router->expects($this->once())->method('match')
            ->will($this->throwException(new \Exception));

        $this->assertEquals('/foo/baz', $localizer->prefixUrl('/foo/baz'));
    }

    /**
     * Tests prefixUrl when the router throws an exception.
     */
    public function testPrefixUrlWhenNoMatching()
    {
        $this->urlHelper->expects($this->once())->method('parse')
            ->with('/foo/baz')
            ->willReturn([ 'path' => '/foo/baz' ]);

        $this->router->expects($this->once())->method('match')
            ->with('/foo/baz')
            ->will($this->throwException(new \Exception()));

        $this->assertEquals('/foo/baz', $this->localizer->prefixUrl('/foo/baz'));
    }

    /**
     * Tests prefixUrl when the locale is the default or not exists.
     */
    public function testPrefixUrlWhenDefaultLocale()
    {
        $this->urlHelper->expects($this->once())->method('parse')
            ->with('/foo/baz')
            ->willReturn([ 'path' => '/foo/baz' ]);

        $this->locale->expects($this->at(0))->method('getLocale')
            ->with('frontend')
            ->willReturn('esp');

        $this->locale->expects($this->at(1))->method('getRequestLocale')
            ->willReturn('esp');

        $this->assertEquals('/foo/baz', $this->localizer->prefixUrl('/foo/baz'));
    }

    /**
     * Tests prefixUrl when the url is successfully translated.
     */
    public function testPrefixUrlWhenSuccess()
    {
        $this->urlHelper->expects($this->once())->method('parse')
            ->with('/foo/baz')
            ->willReturn([ 'path' => '/foo/baz' ]);

        $this->router->expects($this->once())->method('match')
            ->willReturn([ '_route' => 'foo_baz' ]);

        $this->locale->expects($this->at(0))->method('getLocale')
            ->with('frontend')
            ->willReturn('esp');

        $this->locale->expects($this->at(1))->method('getRequestLocale')
            ->willReturn('eng');

        $this->locale->expects($this->at(2))->method('getSlugs')
            ->willReturn([ 'esp' => 'esp', 'eng' => 'eng' ]);

        $this->routeHelper->expects($this->once())->method('isRouteLocalizable')
            ->willReturn([ 'foo_baz' ]);

        $this->urlHelper->expects($this->once())->method('unparse')
            ->with([ 'path' => '/eng/foo/baz' ])
            ->willReturn('/eng/foo/baz');

        $this->assertEquals('/eng/foo/baz', $this->localizer->prefixUrl('/foo/baz'));
    }
}
