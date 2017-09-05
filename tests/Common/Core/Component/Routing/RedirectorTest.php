<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Routing;

use Common\Core\Component\Routing\Redirector;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for Redirector class.
 */
class RedirectorTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Cache')
            ->setMethods([ 'exists', 'get', 'set' ])
            ->getMock();

        $this->connection = $this->getMockBuilder('Connection')
            ->setMethods([ 'fetchAssoc' ])
            ->getMock();

        $this->cm = $this->getMockBuilder('CacheManager')
            ->setMethods([ 'getConnection', 'hasConnection' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getConnection' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->cm->expects($this->any())->method('hasConnection')
            ->with('instance')->willReturn(true);
        $this->cm->expects($this->any())->method('getConnection')
            ->with('instance')->willReturn($this->cache);

        $this->em->expects($this->any())->method('getConnection')
            ->with('instance')->willReturn($this->connection);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->redirector = new Redirector($this->container);
    }

    /**
     * Returns mocks basing on arguments when calling get method of
     * ServiceContainer mock.
     */
    public function serviceContainerCallback()
    {
        $args = func_get_args();

        switch ($args[0]) {
            case 'cache.manager':
                return $this->cm;
            case 'orm.manager':
                return $this->em;
            default:
                throw new \Exception();
        }
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $container = new \ReflectionProperty($this->redirector, 'container');
        $container->setAccessible(true);

        $cache = new \ReflectionProperty($this->redirector, 'cache');
        $cache->setAccessible(true);

        $connection = new \ReflectionProperty($this->redirector, 'conn');
        $connection->setAccessible(true);

        $this->assertInstanceOf('Common\Core\Component\Routing\Redirector', $this->redirector);

        $this->assertEquals($this->container, $container->getValue($this->redirector));
        $this->assertEquals($this->cache, $cache->getValue($this->redirector));
        $this->assertEquals($this->connection, $connection->getValue($this->redirector));
    }

    /**
     * Tests getTranslation when entry not in cache with multiple values.
     */
    public function testGetTranslation()
    {
        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setMethods([ 'getTranslationById', 'getTranslationBySlug' ])
            ->setConstructorArgs([ $this->container ])
            ->getMock();

        $this->cache->expects($this->exactly(5))->method('exists')->willReturn(false);
        $this->cache->expects($this->exactly(5))->method('set');

        $redirector->expects($this->exactly(3))->method('getTranslationById');
        $redirector->expects($this->exactly(2))->method('getTranslationBySlug');

        $redirector->getTranslation('plugh-xyzzy-bar', null, null);
        $redirector->getTranslation('plugh-xyzzy-bar', 'article', null);
        $redirector->getTranslation('plugh-xyzzy-bar', null, 3145);
        $redirector->getTranslation(null, 'bar', 3145);
        $redirector->getTranslation(null, null, 3145);
    }

    /**
     * Tests getTranslationBySlug when entry in cache.
     */
    public function testGetTranslationWithCacheHit()
    {
        $translation = [
            'pk_content'     => 1467,
            'pk_content_old' => 4796,
            'slug'           => 'garply',
            'type'           => 'norf',
            'domain'         => 'frog.com'
        ];

        $this->cache->expects($this->once())->method('exists')
            ->with('redirector-garply-norf-')->willReturn(true);
        $this->cache->expects($this->once())->method('get')
            ->with('redirector-garply-norf-')->willReturn($translation);

        $this->assertEquals($translation, $this->redirector->getTranslation('garply', 'norf'));
    }

    /**
     * Tests getTranslation when no parameters provided.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetTranslationInvalid()
    {
        $this->redirector->getTranslation(null, null, null);
    }

    /**
     * Tests getTranslationById.
     */
    public function testGetTranslationById()
    {
        $translation = [
            'pk_content'     => 1467,
            'pk_content_old' => 4796,
            'slug'           => 'garply',
            'type'           => 'norf',
            'domain'         => 'frog.com'
        ];

        $method = new \ReflectionMethod($this->redirector, 'getTranslationById');
        $method->setAccessible(true);

        $this->connection->expects($this->once())->method('fetchAssoc')
            ->with('SELECT * FROM `translation_ids` WHERE `pk_content_old` = ? LIMIT 1', [ 4796 ])
            ->willReturn($translation);

        $this->assertEquals($translation, $method->invokeArgs($this->redirector, [ 4796 ]));
    }

    /**
     * Tests getTranslationBySlug.
     */
    public function testGetTranslationBySlug()
    {
        $translation = [
            'pk_content'     => 1467,
            'pk_content_old' => 4796,
            'slug'           => 'garply',
            'type'           => 'norf',
            'domain'         => 'frog.com'
        ];

        $method = new \ReflectionMethod($this->redirector, 'getTranslationBySlug');
        $method->setAccessible(true);

        $this->connection->expects($this->once())->method('fetchAssoc')
            ->with(
                'SELECT * FROM `translation_ids` WHERE `slug` = ? AND `type` = ? LIMIT 1',
                [ 'garply', 'norf' ]
            )->willReturn($translation);

        $this->assertEquals($translation, $method->invokeArgs($this->redirector, [ 'garply', 'norf']));
    }

    /**
     * Tests getCacheId with multiple values.
     */
    public function testGetCacheId()
    {
        $method = new \ReflectionMethod($this->redirector, 'getCacheId');
        $method->setAccessible(true);

        $this->assertEquals(
            'redirector---4325',
            $method->invokeArgs($this->redirector, [ null, null, 4325 ])
        );

        $this->assertEquals(
            'redirector-mumble--',
            $method->invokeArgs($this->redirector, [ 'mumble', null, null ])
        );

        $this->assertEquals(
            'redirector-mumble-flob-',
            $method->invokeArgs($this->redirector, [ 'mumble', 'flob', null ])
        );

        $this->assertEquals(
            'redirector-mumble--4325',
            $method->invokeArgs($this->redirector, [ 'mumble', null, 4325 ])
        );

        $this->assertEquals(
            'redirector-mumble-foobar-4325',
            $method->invokeArgs($this->redirector, [ 'mumble', 'foobar', 4325 ])
        );
    }

    /**
     * Tests hasCache when cache and no cache.
     */
    public function testHasCache()
    {
        $method = new \ReflectionMethod($this->redirector, 'hasCache');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->redirector, []));

        $cache = new \ReflectionProperty($this->redirector, 'cache');
        $cache->setAccessible(true);
        $cache->setValue($this->redirector, null);

        $this->assertFalse($method->invokeArgs($this->redirector, []));
    }
}
