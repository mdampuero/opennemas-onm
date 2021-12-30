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

            default:
                return null;
        }
    }

    /**
     * Tests the method prefixUrl when there is no inner decorator.
     */
    public function testPrefixUrlWhenNoInner()
    {
        $this->urlHelper->expects($this->once())->method('parse')
            ->willReturn([ 'path' => '/foo/baz/glorp.php' ]);

        $this->urlHelper->expects($this->once())->method('unparse')
            ->with([ 'path' => '/sub/foo/baz/glorp.php' ])
            ->willReturn('/sub/foo/baz/glorp.php');

        $this->locale->expects($this->once())->method('getContext')
            ->willReturn('frontend');

        $this->assertEquals('/sub/foo/baz/glorp.php', $this->decorator->prefixUrl('/foo/baz/glorp.php'));
    }

    /**
     * Tests the method prefixUrl when there is inner decorator.
     */
    public function testPrefixUrlWhenInner()
    {
        $url = '/foo/baz/glorp.php';

        $decorator = new UrlSubdirectoryDecorator($this->container, $this->urlHelper, $this->innerDecorator);

        $this->innerDecorator->expects($this->once())->method('prefixUrl')
            ->with($url)
            ->willReturn('/en/foo/baz/glorp.php');

        $this->urlHelper->expects($this->once())->method('parse')
            ->willReturn([ 'path' => '/en/foo/baz/glorp.php' ]);

        $this->urlHelper->expects($this->once())->method('unparse')
            ->with([ 'path' => '/sub/en/foo/baz/glorp.php' ])
            ->willReturn('/sub/en/foo/baz/glorp.php');

        $this->locale->expects($this->once())->method('getContext')
            ->willReturn('frontend');

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

        $this->locale->expects($this->once())->method('getContext')
            ->willReturn('backend');

        $this->assertEquals('/admin/foo/baz', $this->decorator->prefixUrl('/admin/foo/baz'));
    }
}
