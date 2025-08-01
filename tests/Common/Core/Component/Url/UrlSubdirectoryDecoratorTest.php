<?php

namespace Common\Core\Component\Url;

use Common\Model\Entity\Instance;

/**
 * Defines test cases for UrlSubdirectoryDecorator class.
 */
class UrlSubdirectoryDecoratorTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContext' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Symfony\Component\Routing\Router')
            ->disableOriginalConstructor()
            ->setMethods([ 'match', 'getRouteCollection', 'all' ])
            ->getMock();

        $this->route = $this->getMockBuilder('Symfony\Component\Routing\Route')
            ->disableOriginalConstructor()
            ->setMethods([ 'getOption' ])
            ->getMock();

        $this->urlHelper = $this->getMockBuilder('Common\Core\Component\Helper\UrlHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'parse', 'unparse' ])
            ->getMock();

        $this->innerDecorator = $this->getMockBuilder('Common\Core\Component\Url\UrlDecorator')
            ->disableOriginalConstructor()
            ->setMethods([ 'prefixUrl' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->decorator = new UrlSubdirectoryDecorator($this->container, $this->urlHelper);
    }

    /**
     * Returns a mock service basing on name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.locale':
                return $this->locale;

            case 'core.instance':
                return new Instance([ 'subdirectory' => '/sub' ]);

            case 'router':
                return $this->router;

            default:
                return null;
        }
    }

    /**
     * Tests the method prefixUrl when there is no inner decorator.
     */
    public function testPrefixUrlWhenNoInner()
    {
        $this->router->expects($this->at(0))->method('match')
            ->willReturn([ '_route' => 'glorp_baz' ]);

        $this->router->expects($this->at(1))->method('getRouteCollection')
            ->willReturn($this->router);

        $this->router->expects($this->at(2))->method('all')
            ->willReturn([ 'glorp_baz' => $this->route ]);

        $this->route->expects($this->once())->method('getOption')
            ->with('subdirectory')
            ->willReturn(true);

        $this->urlHelper->expects($this->once())->method('parse')
            ->willReturn([ 'path' => '/foo/baz/glorp.php' ]);

        $this->urlHelper->expects($this->once())->method('unparse')
            ->with([ 'path' => '/sub/foo/baz/glorp.php' ])
            ->willReturn('/sub/foo/baz/glorp.php');

        $this->assertEquals('/sub/foo/baz/glorp.php', $this->decorator->prefixUrl('/foo/baz/glorp.php'));
    }

    /**
     * Tests the method prefixUrl when there is inner decorator.
     */
    public function testPrefixUrlWhenInner()
    {
        $url = '/foo/baz/glorp.php';

        $decorator = new UrlSubdirectoryDecorator($this->container, $this->urlHelper, $this->innerDecorator);

        $this->router->expects($this->at(0))->method('match')
            ->willReturn([ '_route' => 'glorp_baz' ]);

        $this->router->expects($this->at(1))->method('getRouteCollection')
            ->willReturn($this->router);

        $this->router->expects($this->at(2))->method('all')
            ->willReturn([ 'glorp_baz' => $this->route ]);

        $this->route->expects($this->once())->method('getOption')
            ->with('subdirectory')
            ->willReturn(true);

        $this->innerDecorator->expects($this->once())->method('prefixUrl')
            ->with($url)
            ->willReturn('/en/foo/baz/glorp.php');

        $this->urlHelper->expects($this->once())->method('parse')
            ->willReturn([ 'path' => '/en/foo/baz/glorp.php' ]);

        $this->urlHelper->expects($this->once())->method('unparse')
            ->with([ 'path' => '/sub/en/foo/baz/glorp.php' ])
            ->willReturn('/sub/en/foo/baz/glorp.php');

        $this->assertEquals('/sub/en/foo/baz/glorp.php', $decorator->prefixUrl($url));
    }

    /**
     * Tests the method prefixUrl when the url is not decorable.
     */
    public function testPrefixUrlWhenNoDecorable()
    {
        $this->urlHelper->expects($this->once())->method('parse')
            ->with('/admin/foo/baz')
            ->willReturn([ 'path' => '/admin/foo/baz' ]);

        $this->router->expects($this->at(0))->method('match')
            ->willReturn([ '_route' => 'foo_baz' ]);

        $this->router->expects($this->at(1))->method('getRouteCollection')
            ->willReturn($this->router);

        $this->router->expects($this->at(2))->method('all')
            ->willReturn([ 'glorp_baz' => $this->route ]);

        $this->assertEquals('/admin/foo/baz', $this->decorator->prefixUrl('/admin/foo/baz'));
    }
}
