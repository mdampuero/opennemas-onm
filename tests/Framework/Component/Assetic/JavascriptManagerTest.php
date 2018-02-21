<?php

namespace Tests\Framework\Component\Assetic;

use Framework\Component\Assetic\JavascriptManager;

class JavascriptManagerTest extends AssetManagerTest
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->manager = new JavascriptManager(
            $this->container,
            [
                'asset_compilation_in_dev' => false,
                'filters' => [
                    'uglifyjs' => [
                        'bin'     => 'foo',
                        'node'    => 'bar',
                        'options' => [ 'mangle' => true ]
                    ],
                ],
                'build_path'  => 'baz/quux',
                'output_path' => 'baz/quux/dist',
                'root'        => 'foo'
            ]
        );
    }

    public function testGetAssetFactory()
    {
        $method = new \ReflectionMethod($this->manager, 'getAssetFactory');
        $method->setAccessible(true);

        $factory = $method->invokeArgs($this->manager, []);
        $property = new \ReflectionProperty($factory, 'output');
        $property->setAccessible(true);

        $this->assertInstanceOf('Assetic\Factory\AssetFactory', $factory);
        $this->assertEquals('baz/quux/*.js', $property->getValue($factory));

        $property = new \ReflectionProperty($factory, 'debug');
        $property->setAccessible(true);

        $this->assertEquals($this->manager->debug(), $property->getValue($factory));
    }

    public function testGetTargetPath()
    {
        $method = new \ReflectionMethod($this->manager, 'getTargetPath');
        $method->setAccessible(true);

        $src = $method->invokeArgs($this->manager, [ 'foo.js' ]);
        $this->assertEquals(1, preg_match('/baz\/quux\/foo\.[a-z0-9]{8}\.[0-9]{14}\.xzy\.js/', $src));

        $src = $method->invokeArgs($this->manager, [ 'foo.js', 'default', true ]);
        $this->assertEquals(1, preg_match('/baz\/quux\/dist\/foo\.[a-z0-9]{8}\.[0-9]{14}\.xzy\.js/', $src));

        $src = $method->invokeArgs($this->manager, [ [ 'foo.js', 'bar.js' ], 'norf' ]);
        $this->assertEquals(1, preg_match('/baz\/quux\/norf\.[a-z0-9]{8}\.[0-9]{14}\.xzy\.js/', $src));
    }

    public function testGetFilterManager()
    {
        $method = new \ReflectionMethod($this->manager, 'getFilterManager');
        $method->setAccessible(true);

        $fm = $method->invokeArgs($this->manager, [ [ 'uglifyjs' ] ]);

        $this->assertTrue($fm->has('uglifyjs'));
    }

    public function testGetFiltersInDevelopment()
    {
        $method = new \ReflectionMethod($this->manager, 'getFilters');
        $method->setAccessible(true);

        $this->assertEmpty(
            $method->invokeArgs($this->manager, [ 'foo.bar', [ 'uglifyjs' ] ])
        );

        $this->assertEmpty(
            $method->invokeArgs($this->manager, [ 'foo.js', [ 'uglifyjs' ] ])
        );
    }

    public function testGetFiltersInProduction()
    {
        $manager = new JavascriptManager(
            $this->container,
            [ 'asset_compilation_in_dev' => true ]
        );

        $method = new \ReflectionMethod($manager, 'getFilters');
        $method->setAccessible(true);

        $this->assertEmpty(
            $method->invokeArgs($manager, [ 'foo.bar', [ 'uglifyjs' ] ])
        );

        $this->assertEquals(
            [ 'uglifyjs' ],
            $method->invokeArgs($manager, [ 'foo.js', [ 'uglifyjs' ] ])
        );
    }
}
