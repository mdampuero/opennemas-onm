<?php

namespace Tests\Common\Core\Component\Url;

use Common\Core\Component\Url\UrlDecorator;
use Common\Core\Component\Url\UrlDecoratorFactory;
use Common\Core\Component\Url\UrlLocalizerDecorator;
use Common\Core\Component\Url\UrlSubdirectoryDecorator;

/**
 * Defines test cases for UrlDecoratorFactory class.
 */
class UrlDecoratorFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->urlHelper = $this->getMockBuilder('Common\Core\Component\Helper\UrlHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'parse', 'unparse' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Common\Model\Entity\Instance')
            ->disableOriginalConstructor()
            ->setMethods([ 'hasMultilanguage', 'isSubdirectory' ])
            ->getMock();

        $this->urlDecoratorFactory = new UrlDecoratorFactory($this->container, $this->urlHelper, $this->instance);
    }

    /**
     * Tests getUrlDecorator with normal instance.
     */
    public function testGetUrlDecoratorWithNormalInstance()
    {
        $urlDecorator = new UrlDecorator($this->container, $this->urlHelper);

        $this->instance->expects($this->once())->method('hasMultilanguage')
            ->willReturn(false);

        $this->instance->expects($this->once())->method('isSubdirectory')
            ->willReturn(false);

        $this->assertEquals($urlDecorator, $this->urlDecoratorFactory->getUrlDecorator($this->instance));
    }

    /**
     * Tests getUrlDecorator with multilanguage instance.
     */
    public function testGetUrlDecoratorWithMultilanguageInstance()
    {
        $urlLocalizerDecorator = new UrlLocalizerDecorator($this->container, $this->urlHelper);
        $urlDecorator          = new UrlDecorator($this->container, $this->urlHelper, $urlLocalizerDecorator);

        $this->instance->expects($this->once())->method('hasMultilanguage')
            ->willReturn(true);

        $this->instance->expects($this->once())->method('isSubdirectory')
            ->willReturn(false);

        $this->assertEquals($urlDecorator, $this->urlDecoratorFactory->getUrlDecorator());
    }

    /**
     * Tests getUrlDecorator with subdirectory instance.
     */
    public function testGetUrlDecoratorWithSubdirectoryInstance()
    {
        $urlSubdirectoryDecorator = new UrlSubdirectoryDecorator($this->container, $this->urlHelper);
        $urlDecorator             = new UrlDecorator($this->container, $this->urlHelper, $urlSubdirectoryDecorator);

        $this->instance->expects($this->once())->method('hasMultilanguage')
            ->willReturn(false);

        $this->instance->expects($this->once())->method('isSubdirectory')
            ->willReturn(true);

        $this->assertEquals($urlDecorator, $this->urlDecoratorFactory->getUrlDecorator());
    }
}
