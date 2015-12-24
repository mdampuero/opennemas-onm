<?php

namespace Framework\Tests\ORM\Configuration;

use Framework\ORM\Core\Entity;
use Framework\ORM\Loader\Loader;
use Framework\Fixture\FixtureLoader;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidPaths()
    {
        new Loader(null, []);
    }

    public function testValidPaths()
    {
        $fakeLoader = $this->getMockBuilder('FakeLoader')
            ->disableOriginalConstructor()
            ->setMethods([ 'load' ])
            ->getMock();

        $fakeLoader->method('load')->willReturn(new Entity());
        $fakeLoader->expects($this->once())->method('load');

        $container = $this->getMockBuilder('ServiceContainer')
            ->disableOriginalConstructor()
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $container->method('get')->willReturn($fakeLoader);
        $container->method('getParameter')->willReturn(
            substr(__DIR__, 0, strpos(__DIR__, '/src')) . '/app'
        );

        $container->expects($this->once())->method('getParameter');
        $container->expects($this->once())->method('get');

        $loader = new Loader(
            $container,
            [ 'public/themes/basic' ]
        );

        $this->assertNotEmpty($loader->get());
    }
}
