<?php

namespace Common\Core\Component\Url;

/**
 * Defines test cases for UrlDecoratorFactory class.
 */
class UrlDecoratorTest extends \PHPUnit\Framework\TestCase
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

        $this->decorator = new UrlDecorator($this->container, $this->urlHelper);
    }

    /**
     * Tests the method prefixUrl when there is no inner decorator.
     */
    public function testPrefixUrlWhenNoInner()
    {
        $this->assertEquals('/foo/baz/glorp.php', $this->decorator->prefixUrl('/foo/baz/glorp.php'));
    }

    /**
     * Tests the method prefixUrl when there is inner decorator.
     */
    public function testPrefixUrlWhenInner()
    {
        $url = '/foo/baz/glorp.php';

        $decorator = new UrlDecorator($this->container, $this->urlHelper, $this->innerDecorator);

        $this->innerDecorator->expects($this->once())->method('prefixUrl')
            ->with($url)
            ->willReturn('/sub/foo/baz/glorp.php');

        $this->assertEquals('/sub/foo/baz/glorp.php', $decorator->prefixUrl($url));
    }
}
