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
            ->setMethods([ 'findOneBy' ])
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
        $this->cache->expects($this->once())->method('exists')
            ->with('fubar.foo')->willReturn(false);

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('domains regexp "^fubar.foo($|,)|,\s*fubar.foo\s*,|(^|,)\s*fubar.foo$"')
            ->willReturn(new Instance([
                'internal_name' => 'garply',
                'domains'       => [ 'fubar.foo' ]
            ]));

        $instance = $this->loader
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
        $this->cache->expects($this->once())->method('exists')
            ->with('fubar.foo')->willReturn(false);

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('domains regexp "^fubar.foo($|,)|,\s*fubar.foo\s*,|(^|,)\s*fubar.foo$"')
            ->willReturn(new Instance([
                'internal_name' => 'garply',
                'domains'       => [ 'thud.foo' ]
            ]));

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
        $this->repository->expects($this->once())->method('findOneBy')
            ->with('internal_name = "garply"')
            ->willReturn(new Instance([
                'internal_name' => 'garply',
                'domains'       => [ 'fubar.foo' ]
            ]));

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
        $this->repository->expects($this->once())->method('findOneBy')
            ->with('internal_name = "garply"')
            ->willReturn(new Instance([
                'internal_name' => 'thud',
                'domains'       => [ 'fubar.foo' ]
            ]));

        $this->expectException(\Exception::class);

        $this->loader
            ->loadInstanceByName('garply')
            ->getInstance();
    }

    /**
     * Tests setInstance.
     */
    public function testSetInstance()
    {
        $instance = new Instance([ 'internal_name' => 'glorp' ]);

        $this->loader->setInstance($instance);

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
