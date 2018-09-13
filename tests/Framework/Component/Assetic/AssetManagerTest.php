<?php

namespace Tests\Framework\Component\Assetic;

abstract class AssetManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $port = 80;

    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
    }

    public function serviceContainerCallback()
    {
        $args = func_get_args();

        switch ($args[0]) {
            case 'kernel':
                $kernel = $this->getMockBuilder('Kernel')
                    ->setMethods([ 'getEnvironment' ])
                    ->getMock();

                $kernel->method('getEnvironment')->willReturn('dev');

                return $kernel;
            case 'core.instance':
                $instance           = new \StdClass();
                $instance->settings = [ 'TEMPLATE_USER' => 'foo' ];

                return $instance;
            case 'request_stack':
                $headers = $this->getMockBuilder('HeadersBag')
                    ->setMethods([ 'get' ])
                    ->getMock();

                $headers->expects($this->any())->method('get')->willReturn($this->port);

                $request          = new \StdClass();
                $request->headers = $headers;

                $requestStack = $this->getMockBuilder('RequestStack')
                    ->setMethods([ 'getCurrentRequest' ])
                    ->getMock();

                $requestStack->expects($this->any())->method('getCurrentRequest')->willReturn($request);

                return $requestStack;
        }
    }

    public function testCreateAssetSrcWithAssetServersInvalidPattern()
    {
        $manager = $this->getMockForAbstractClass(
            'Framework\Component\Assetic\AssetManager',
            [
                $this->container,
                [
                    'asset_compilation_in_dev' => true,
                    'use_asset_servers'        => true,
                    'asset_domain'             => '//media.opennemas.net',
                ]
            ]
        );

        $method = new \ReflectionMethod($manager, 'createAssetSrc');
        $method->setAccessible(true);

        $src = $method->invokeArgs($manager, [ 'foo.css' ]);
        $this->assertEquals('//media.opennemas.net/foo.css', $src);
    }

    public function testCreateAssetSrcWithAssetServersValidPattern()
    {
        $manager = $this->getMockForAbstractClass(
            'Framework\Component\Assetic\AssetManager',
            [
                $this->container,
                [
                    'asset_compilation_in_dev' => true,
                    'use_asset_servers'        => true,
                    'asset_domain'             => '//media%d.opennemas.net',
                    'asset_servers'            => 3
                ]
            ]
        );

        $method = new \ReflectionMethod($manager, 'createAssetSrc');
        $method->setAccessible(true);

        $src = $method->invokeArgs($manager, [ 'foo.css' ]);
        $this->assertEquals(1, preg_match('/\/\/media\d+\.opennemas\.net\/foo\.css/', $src));

        $this->port = 8080;
        $src        = $method->invokeArgs($manager, [ 'foo.css' ]);
        $this->assertEquals(1, preg_match('/\/\/media\d+\.opennemas\.net:8080\/foo\.css/', $src));
    }

    public function testCreateAssetSrcWithoutAssetServers()
    {
        $manager = $this->getMockForAbstractClass(
            'Framework\Component\Assetic\AssetManager',
            [
                $this->container,
                [
                    'asset_compilation_in_dev' => true,
                    'use_asset_servers'        => false,
                ]
            ]
        );

        $method = new \ReflectionMethod($manager, 'createAssetSrc');
        $method->setAccessible(true);

        $src = $method->invokeArgs($manager, [ 'foo.css' ]);
        $this->assertEquals('/foo.css', $src);
    }
}
