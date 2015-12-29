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
        $container = $this->getMockBuilder('ServiceContainer')
            ->disableOriginalConstructor()
            ->setMethods([ 'getParameter' ])
            ->getMock();

        $container->method('getParameter')->willReturn(__DIR__ . '/../../../../app');
        $container->expects($this->once())->method('getParameter');

        $loader = new Loader(
            $container,
            [ 'config/orm' ]
        );

        $this->assertNotEmpty($loader->load());
    }
}
