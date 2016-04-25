<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Cache\Redis;

use Common\Cache\Redis\Redis;
use Common\ORM\Core\Entity;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for Redis class.
 */
class RedisTest extends KernelTestCase
{
    public function setUp()
    {
        self::bootKernel();

        $params = self::$kernel->getContainer()
            ->getParameter('cache_handler_params');

        $params['auth'] = 'gorp';

        $this->redis = new Redis($params);
    }

    /**
     * Tests constructor with invalid redis parameters.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidConfiguration()
    {
        new Redis([]);
    }

    /**
     * Tests getRedis.
     */
    public function testGetRedis()
    {
        $method = new \ReflectionMethod($this->redis, 'getRedis');
        $method->setAccessible(true);

        $this->assertInstanceOf('Redis', $method->invokeArgs($this->redis, []));
    }

    /**
     * Tests setNamespace and getNamespace.
     */
    public function testSetNamespace()
    {
        $this->redis->setNamespace('garply');
        $this->assertEquals('garply', $this->redis->getNamespace());
    }

    /**
     * Tests set and get with single values.
     */
    public function testWithSingleValues()
    {
        $this->redis->set('foo', 'bar', 60);
        $this->assertTrue($this->redis->exists('foo'));
        $this->assertEquals('bar', $this->redis->get('foo'));
        $this->redis->delete('foo');
        $this->assertEmpty($this->redis->get('foo'));

        $object = json_decode(json_encode(['foo' => 'bar']));

        $this->redis->set('flob', $object);
        $this->assertEquals($object, $this->redis->get('flob'));
        $this->redis->delete('flob');
        $this->assertEmpty($this->redis->get('flob'));
    }

    /**
     * Tests set and get with multiple keys and values.
     */
    public function testWithMultipleValues()
    {
        $this->redis->set([ 'foo' => 'bar', 'fred' => 'wibble' ]);

        $this->assertTrue($this->redis->exists('foo'));
        $this->assertEquals(['foo' => 'bar', 'fred' => 'wibble' ], $this->redis->get([ 'foo', 'fred' ]));
        $this->assertEquals(['foo' => 'bar' ], $this->redis->get([ 'foo', 'garply' ]));
        $this->assertTrue($this->redis->exists('foo'));

        $this->assertEquals('bar', $this->redis->get('foo'));
        $this->assertEquals('wibble', $this->redis->get('fred'));
        $this->redis->delete([ 'foo', 'fred' ]);
        $this->assertEmpty($this->redis->get([ 'foo', 'fred' ]));
    }
}
