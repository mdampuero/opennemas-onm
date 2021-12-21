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

        $this->urlHelper = $this->getMockBuilder('Common\Core\Component\Helper\UrlHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'parse', 'unparse' ])
            ->getMock();

        $this->innerDecorator = $this->getMockBuilder('Common\Core\Component\Url\UrlDecorator')
            ->disableOriginalConstructor()
            ->setMethods([ 'prefixUrl' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->with('core.instance')
            ->willReturn(new Instance([ 'subdirectory' => '/sub' ]));

        $this->decorator = new UrlSubdirectoryDecorator($this->container, $this->urlHelper);
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

        $this->assertEquals('/sub/en/foo/baz/glorp.php', $decorator->prefixUrl($url));
    }
}
