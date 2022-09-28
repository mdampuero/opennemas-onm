<?php

namespace Tests\Common\Core\Component\Loader;

use Common\Core\Component\Loader\InstanceLoader;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for InstanceLoader class.
 */
class InstanceLoaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Opennemas\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'exists', 'get', 'set' ])
            ->getMock();

        $this->cm = $this->getMockBuilder('Opennemas\Cache\Core\CacheManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getConnection' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Openneams\Orm\Database\Repository\BaseRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'findOneBy', 'findBy' ])
            ->getMock();

        $this->cm->expects($this->any())->method('getConnection')
            ->with('manager')->willReturn($this->cache);

        $this->em->expects($this->any())->method('getRepository')
            ->with('Instance')->willReturn($this->repository);

        $this->loader = new InstanceLoader($this->cm, $this->em);
    }

    /**
     * Tests getInstance.
     */
    public function testGetInstance()
    {
        $this->assertEmpty($this->loader->getInstance());

        $instance = new Instance([ 'internal_name' => 'mumble' ]);

        $property = new \ReflectionProperty($this->loader, 'instance');
        $property->setAccessible(true);
        $property->setValue($this->loader, $instance);

        $this->assertEquals($instance, $this->loader->getInstance());
    }

    /**
     * Tests loadInstanceByDomain when the provided URI is for manager.
     */
    public function testLoadInstanceByDomainForManager()
    {
        $instance = $this->loader
            ->loadInstanceByDomain('fubar.foo', '/manager')
            ->getInstance();

        $this->assertEquals('manager', $instance->internal_name);
    }

    /**
     * Tests loadInstanceByDomain when the provided domain and URI refers to a
     * valid instance that was previously saved to cache.
     */
    public function testLoadInstanceByDomainWhenInstanceInCache()
    {
        $this->cache->expects($this->once())->method('exists')
            ->with('fubar.foo')->willReturn(true);
        $this->cache->expects($this->once())->method('get')
            ->with('fubar.foo')
            ->willReturn(new Instance([
                'internal_name' => 'garply',
                'domains'       => [ 'fubar.foo' ]
            ]));

        $this->repository->expects($this->any())->method('findBy')
            ->willReturn([
                new Instance([
                    'internal_name' => 'garply',
                    'domains'       => [ 'fubar.foo' ]
                ])
            ]);

        $instance = $this->loader
            ->loadInstanceByDomain('fubar.foo', '/')
            ->getInstance();

        $this->assertEquals('garply', $instance->internal_name);
    }

    /**
     * Tests loadInstanceByDomain when the provided domain and URI refers to a
     * valid instance that was previously not saved to cache.
     */
    public function testLoadInstanceByDomainWhenInstanceInDatabase()
    {
        $this->repository->expects($this->any())->method('findBy')
            ->willReturn([
                new Instance([
                    'internal_name' => 'garply',
                    'domains'       => [ 'fubar.foo' ]
                ])
            ]);

        $loader = $this->getMockBuilder('Common\Core\Component\Loader')
            ->disableOriginalConstructor()
            ->setMethods([ 'isValid', 'loadInstanceByDomain', 'getInstance' ])
            ->getMock();

        $loader->expects($this->any())->method('isValid')
            ->willReturn(true);

        $loader->expects($this->any())->method('loadInstanceByDomain')
            ->willReturn($loader);

        $loader->expects($this->any())->method('getInstance')
            ->willReturn(
                new Instance([
                'internal_name' => 'garply',
                'domains'       => [ 'fubar.foo' ]
                ])
            );

        $instance = $loader
            ->loadInstanceByDomain('fubar.foo', '/')
            ->getInstance();

        $this->assertEquals('garply', $instance->internal_name);
    }

    /**
     * Tests loadInstanceByDomain when the provided domain and URI refers to a
     * valid instance that was previously not saved to cache.
     */
    public function testLoadInstanceByDomainWhenWrongInstanceInDatabase()
    {
        $this->repository->expects($this->any())->method('findBy')
            ->willReturn([]);

        $this->expectException(\Exception::class);

        $this->loader
            ->loadInstanceByDomain('fubar.foo', '/')
            ->getInstance();
    }

    /**
     * Tests loadInstanceByName when the provided name is manager.
     */
    public function testLoadInstanceByNameForManager()
    {
        $instance = $this->loader
            ->loadInstanceByName('manager')
            ->getInstance();

        $this->assertEquals('manager', $instance->internal_name);
    }

    /**
     * Tests loadInstanceByName when the provided instance name is found in
     * database.
     */
    public function testLoadInstanceByNameWhenInstanceInDatabase()
    {
        $instance = new Instance([
            'internal_name' => 'garply',
            'domains'       => [ 'fubar.foo' ]
        ]);

        $this->repository->expects($this->any())->method('findOneBy')
            ->willReturn($instance);

        $instance = $this->loader
            ->loadInstanceByName('garply')
            ->getInstance();

        $this->assertEquals('garply', $instance->internal_name);
    }

    /**
     * Tests loadInstanceByName when the provided instance name is found in
     * database but wrong.
     */
    public function testLoadInstanceByNameWhenWrongInstanceInDatabase()
    {
        $this->repository->expects($this->any())->method('findOneBy')
            ->willReturn(
                new Instance([
                    'internal_name' => 'thud',
                    'domains'       => [ 'fubar.foo' ]
                ])
            );
        $this->expectException(\Exception::class);

        $this->loader
            ->loadInstanceByName('garply')
            ->getInstance();
    }

    /**
     * Tests loadInstanceByDomainWhenSubdirectoryAndNotValidInstance.
     *
     * @expectedException \Exception
     */
    public function testLoadInstanceByDomainWhenSubdirectoryAndNotValidInstance()
    {
        $this->repository->expects($this->any())->method('findBy')
            ->willReturn([]);

        $this->loader->loadInstanceByDomain('https://glorp.baz', '/subdirectory');
    }

    /**
     * Tests loadInstanceByDomainWhenSubdirectoryAndValidInstance.
     */
    public function testLoadInstanceByDomainWhenSubdirectoryAndValidInstance()
    {
        $instance = new Instance([
            'internal_name' => 'garply',
            'subdirectory'  => 'subdirectory',
            'domains'       => [ 'glorp.baz' ]
        ]);

        $this->repository->expects($this->any())->method('findBy')
            ->willReturn([$instance]);

            $this->assertEquals('garply', $instance->internal_name);
    }

    /**
     * Tests loadInstanceByDomainWhenSubdirectoryAndNotValidCachedInstance.
     *
     * @expectedException \Exception
     */
    public function testLoadInstanceByDomainWhenSubdirectoryAndNotValidCachedInstance()
    {
        $this->cache->expects($this->once())->method('exists')
            ->willReturn(true);

        $this->repository->expects($this->any())->method('findBy')
            ->willReturn([
                new Instance([])
            ]);

        $this->loader->loadInstanceByDomain('glorp.baz', '/subdirectory');
    }

    /**
     * Tests loadInstanceByDomainWhenSubdirectoryAndValidCachedInstance.
     */
    public function testLoadInstanceByDomainWhenSubdirectoryAndValidCachedInstance()
    {
        $instance = new Instance(
            [
                'subdirectory' => '/subdirectory',
                'domains'      => [ 'glorp.baz' ]
            ]
        );
        $this->cache->expects($this->once())->method('exists')
            ->willReturn(true);

        $this->repository->expects($this->any())->method('findBy')
            ->willReturn([
                $instance
            ]);

        $this->cache->expects($this->once())->method('get')
            ->willReturn($instance);

        $this->assertEquals($this->loader, $this->loader->loadInstanceByDomain('glorp.baz', '/subdirectory'));
    }

    /**
     * Tests setInstance.
     */
    public function testSetInstance()
    {
        $instance = new Instance([ 'internal_name' => 'glorp' ]);

        $this->loader->setInstance($instance);

        $this->repository->expects($this->any())->method('findBy')
            ->willReturn([
                $instance
            ]);

        $this->assertEquals($instance, $this->loader->getInstance());
    }

    /**
     * Tests getManagerInstance.
     */
    public function testGetManagerInstance()
    {
        $method = new \ReflectionMethod($this->loader, 'getManagerInstance');
        $method->setAccessible(true);

        $this->assertEquals(new Instance([
            'activated'     => true,
            'internal_name' => 'manager',
            'settings'      => [
                'BD_DATABASE'   => 'onm-instances',
                'TEMPLATE_USER' => 'manager'
            ],
            'activated_modules' => [],
        ]), $method->invokeArgs($this->loader, []));
    }

    /**
     * Tests isManagerUri for manager and non-manager URIs.
     */
    public function testIsManagerUri()
    {
        $method = new \ReflectionMethod($this->loader, 'isManagerUri');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->loader, [ '/manager' ]));
        $this->assertFalse($method->invokeArgs($this->loader, [ '/corge' ]));
    }

    /**
     * Tests isValid for valid and invalid instance values.
     */
    public function testIsValid()
    {
        $method = new \ReflectionMethod($this->loader, 'isValid');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->loader, [ new Instance([
            'domains' => []
        ]), 'glork' ]));

        $this->assertTrue($method->invokeArgs($this->loader, [ new Instance([
            'domains' => [ 'glork' ]
        ]), 'glork' ]));
    }
}
