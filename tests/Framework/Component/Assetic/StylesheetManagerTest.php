<?php

namespace Tests\Framework\Component\Assetic;

use Framework\Component\Assetic\StylesheetManager;

class StylesheetManagerTest extends AssetManagerTest
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->manager = new StylesheetManager(
            $this->container,
            [
                'asset_compilation_in_dev' => false,
                'filters' => [
                    'uglifycss' => [
                        'bin'     => 'foo',
                        'node'    => 'bar',
                        'options' => []
                    ],
                    'less' => [
                        'node'       => 'foo',
                        'node_paths' => [ 'bar' ],
                        'options'    => [ 'foo' => 'bar' ]
                    ]
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
        $this->assertEquals('baz/quux/*.css', $property->getValue($factory));

        $property = new \ReflectionProperty($factory, 'debug');
        $property->setAccessible(true);

        $this->assertEquals($this->manager->debug(), $property->getValue($factory));
    }

    public function testGetTargetPath()
    {
        $method = new \ReflectionMethod($this->manager, 'getTargetPath');
        $method->setAccessible(true);

        $src = $method->invokeArgs($this->manager, [ 'foo.css' ]);
        $this->assertEquals(1, preg_match('/baz\/quux\/foo\.[a-z0-9]{8}\.[0-9]{14}\.xzy\.css/', $src));

        $src = $method->invokeArgs($this->manager, [ [ 'foo.css', 'bar.less' ] ]);
        $this->assertEquals(1, preg_match('/baz\/quux\/default\.[a-z0-9]{8}\.[0-9]{14}\.xzy\.css/', $src));
    }

    public function testGetFilterManager()
    {
        $method = new \ReflectionMethod($this->manager, 'getFilterManager');
        $method->setAccessible(true);

        $fm = $method->invokeArgs($this->manager, [ [ 'cssrewrite', 'less', 'uglifycss' ] ]);

        $this->assertTrue($fm->has('cssrewrite'));
        $this->assertTrue($fm->has('less'));
        $this->assertTrue($fm->has('uglifycss'));
    }

    public function testGetFiltersInDevelopment()
    {
        $method = new \ReflectionMethod($this->manager, 'getFilters');
        $method->setAccessible(true);

        $this->assertEmpty(
            $method->invokeArgs($this->manager, [ 'foo.bar', [ 'cssrewrite', 'less', 'uglifycss' ] ])
        );

        $this->assertEquals(
            [ 'cssrewrite' ],
            $method->invokeArgs($this->manager, [ 'foo.css', [ 'cssrewrite', 'less', 'uglifycss' ] ])
        );

        $this->assertEquals(
            [ 'cssrewrite', 'less' ],
            $method->invokeArgs($this->manager, [ 'foo.less', [ 'cssrewrite', 'less', 'uglifycss' ] ])
        );
    }

    public function testGetFiltersInProduction()
    {
        $manager = new StylesheetManager(
            $this->container,
            [ 'asset_compilation_in_dev' => true ]
        );

        $method = new \ReflectionMethod($manager, 'getFilters');
        $method->setAccessible(true);

        $this->assertEmpty(
            $method->invokeArgs($manager, [ 'foo.bar', [ 'cssrewrite', 'less', 'uglifycss' ] ])
        );

        $this->assertEquals(
            [ 'cssrewrite', 'uglifycss' ],
            $method->invokeArgs($manager, [ 'foo.css', [ 'cssrewrite', 'less', 'uglifycss' ] ])
        );

        $this->assertEquals(
            [ 'cssrewrite', 'less', 'uglifycss' ],
            $method->invokeArgs($manager, [ 'foo.less', [ 'cssrewrite', 'less', 'uglifycss' ] ])
        );
    }
}
