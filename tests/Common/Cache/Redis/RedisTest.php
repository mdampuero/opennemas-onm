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

/**
 * Defines test cases for Redis class.
 */
class RedisTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->baseRedis = $this->getMockBuilder('Redis')
            ->setMethods([ 'auth', 'delete', 'exists', 'eval', 'expire', 'get', 'mGet', 'mSet', 'pconnect', 'set' ])
            ->getMock();

        $this->redis = $this->getMockBuilder('Common\Cache\Redis\Redis')
            ->setMethods([ 'getRedis' ])
            ->setConstructorArgs([
                [
                    'name'   => 'bar',
                    'server' => '127.0.0.1',
                    'port'   => '6379',
                    'auth'   => 'gorp'
                ]
            ])->getMock();

        $this->redis->expects($this->any())->method('getRedis')
            ->willReturn($this->baseRedis);
    }

    /**
     * Tests deleteByPattern.
     *
     * TODO: Uncomment when updating PHP to version 7.0
     */
    //public function testDeleteByPattern()
    //{
        //$this->baseRedis->expects($this->once())->method('eval')->with(
            //'redis.call("del", unpack(redis.call("keys", ARGV[1])))',
            //['foo*']
        //);

        //$this->redis->deleteByPattern('foo*');
    //}

    /**
     * Tests execute.
     *
     * TODO: Uncomment when updating PHP to version 7.0
     */
    //public function testExecute()
    //{
        //$this->baseRedis->expects($this->once())->method('eval')
            //->with('foo' , [ 'bar' ]);

        //$this->redis->execute('foo', [ 'bar' ]);
    //}

    /**
     * Tests getPrefix with default and custom values.
     */
    public function testGetPrefix()
    {
        $method = new \ReflectionMethod($this->redis, 'getPrefix');
        $method->setAccessible(true);

        $this->assertEquals('', $method->invokeArgs($this->redis, []));

        $this->redis->prefix = 'quux';

        $this->assertEquals('quux_', $method->invokeArgs($this->redis, []));
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
        $object = json_decode(json_encode(['foo' => 'bar']));

        $this->baseRedis->expects($this->at(2))->method('exists')
            ->willReturn(true);
        $this->baseRedis->expects($this->at(3))->method('get')
            ->with('_foo')->willReturn('s:3:"bar";');
        $this->baseRedis->expects($this->at(7))->method('get')
            ->with('_flob')->willReturn(serialize($object));

        $this->redis->set('foo', 'bar', 60);
        $this->assertTrue($this->redis->exists('foo'));
        $this->assertEquals('bar', $this->redis->get('foo'));
        $this->redis->remove('foo');
        $this->assertEmpty($this->redis->get('foo'));

        $this->redis->set('flob', $object);
        $this->assertEquals($object, $this->redis->get('flob'));
        $this->redis->remove('flob');
        $this->assertEmpty($this->redis->get('flob'));
    }

    /**
     * Tests set and get with multiple keys and values.
     */
    public function testWithMultipleValues()
    {
        $this->baseRedis->expects($this->at(1))->method('exists')
            ->willReturn(true);
        $this->baseRedis->expects($this->at(2))->method('mGet')
            ->with([ '_foo', '_fred' ])->willReturn([ serialize('bar'), serialize('wibble') ]);
        $this->baseRedis->expects($this->at(3))->method('mGet')
            ->with([ '_garply' ])->willReturn([ null ]);
        $this->baseRedis->expects($this->at(5))->method('mGet')
            ->with(['_foo', '_fred'])->willReturn([ null, null ]);

        $this->redis->set([ 'foo' => 'bar', 'fred' => 'wibble' ]);

        $this->assertTrue($this->redis->exists('foo'));
        $this->assertEquals(['foo' => 'bar', 'fred' => 'wibble' ], $this->redis->get([ 'foo', 'fred' ]));
        $this->assertEquals(['foo' => 'bar' ], $this->redis->get([ 'foo', 'garply' ]));
        $this->assertTrue($this->redis->exists('foo'));

        $this->assertEquals('bar', $this->redis->get('foo'));
        $this->assertEquals('wibble', $this->redis->get('fred'));
        $this->redis->remove([ 'foo', 'fred' ]);
        $this->assertEmpty($this->redis->get([ 'foo', 'fred' ]));
    }
}
