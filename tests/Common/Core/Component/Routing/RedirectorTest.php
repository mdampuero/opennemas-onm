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
use Common\ORM\Entity\Url;

/**
 * Defines test cases for Redirector class.
 */
class RedirectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Common\Cache\Core\Cache')
            ->disableOriginalConstructor()
            ->setMethods([
                'contains', 'delete', 'deleteByPattern', 'deleteMulti',
                'exists', 'fetch', 'fetchMulti', 'get', 'save', 'saveMulti',
                'set'
            ])->getMock();

        $this->service = $this->getMockBuilder('Api\Service\V1\OrmService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getList' ])
            ->getMock();

        $this->redirector = new Redirector($this->service, $this->cache);
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $service = new \ReflectionProperty($this->redirector, 'service');
        $service->setAccessible(true);

        $cache = new \ReflectionProperty($this->redirector, 'cache');
        $cache->setAccessible(true);

        $this->assertInstanceOf('Common\Core\Component\Routing\Redirector', $this->redirector);

        $this->assertEquals($this->service, $service->getValue($this->redirector));
        $this->assertEquals($this->cache, $cache->getValue($this->redirector));
    }

    /**
     * Tests getTranslationBySlug when entry in cache.
     */
    public function testGetTranslationWithCacheHit()
    {
        $translation = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

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
        $translation = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

        $this->service->expects($this->at(0))->method('getList')
            ->with('content_type = "norf" and type in [0] and source = "4796" limit 1')
            ->willReturn([ 'items' => [ $translation ], 'total' => 1 ]);

        $this->service->expects($this->at(1))->method('getList')
            ->with('type in [0] and source = "4796" limit 1')
            ->willReturn([ 'items' => [ $translation ], 'total' => 1 ]);

        $this->assertEquals($translation, $this->redirector->getTranslation(null, 'norf', 4796));
        $this->assertEquals($translation, $this->redirector->getTranslation(null, null, 4796));
    }

    /**
     * Tests getTranslationBySlug.
     */
    public function testGetTranslationBySlug()
    {
        $translation = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => 'garply',
            'target'       => 4796,
            'type'         => 0
        ]);

        $this->service->expects($this->at(0))->method('getList')
            ->with('type in [1,2] and source = "garply" limit 1')
            ->willReturn([ 'items' => [ $translation ], 'total' => 1 ]);

        $this->service->expects($this->at(1))->method('getList')
            ->with('content_type = "norf" and type in [1,2] and source = "garply" limit 1')
            ->willReturn([ 'items' => [ $translation ], 'total' => 1 ]);

        $this->assertEquals($translation, $this->redirector->getTranslation('garply'));
        $this->assertEquals($translation, $this->redirector->getTranslation('garply', 'norf'));
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
