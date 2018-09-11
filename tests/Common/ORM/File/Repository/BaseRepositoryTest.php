<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\Common\ORM\File\Repository;

use Common\ORM\Core\Metadata;
use Common\ORM\Core\Entity;
use Common\ORM\File\Repository\BaseRepository;

class BaseRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Common\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'exists', 'get', 'set' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'getParameter' ])
            ->getMock();

        $this->container->expects($this->any())->method('getParameter')
            ->willReturn(__DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . '..');

        $this->paths = [];

        $this->metadata = new Metadata([
            'name' => 'Extension',
            'properties' => [
                'foo'    => 'integer',
                'bar'    => 'string',
                'wibble' => 'array',
            ],
            'mapping' => [
                'database' => [
                    'table' => 'foobar',
                    'id'    => 'foo',
                    'metas' => [
                        'foo' => 'flob'
                    ],
                    'columns' => [
                        'foo' => [
                            'type'    => 'integer',
                            'options' => [ 'default' => null ]
                        ],
                        'bar' => [
                            'type'    => 'string',
                            'options' => [ 'default' => null, 'length' => 60 ]
                        ]
                    ],
                    'index' => [
                        [
                            'primary' => true,
                            'columns' => [ 'foo' ]
                        ]
                    ]
                ]
            ]
        ]);

        $this->repository =
            new BaseRepository('foo', $this->container, $this->paths, $this->metadata, $this->cache);

        $this->repository->entities = [
            new Entity([ 'foo' => 'thud', 'bar' => 'flob' ]),
            new Entity([ 'foo' => 'mumble', 'bar' => 'flob' ])
        ];
    }

    /**
     * Tests constructor when paths are not empty.
     */
    public function testConstructor()
    {
        $repository = $this->getMockBuilder('Common\ORM\File\Repository\BaseRepository')
            ->setMethods([ 'load' ])
            ->setConstructorArgs([ 'foo', $this->container, [ 'foo' ], $this->metadata, $this->cache ])
            ->getMock();

        $method = new \ReflectionMethod($repository, 'load');
        $method->setAccessible(true);

        $method->invokeArgs($repository, []);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests countBy with valid and invalid results.
     */
    public function testCountBy()
    {
        $this->assertEquals(2, $this->repository->countBy());
        $this->assertEquals(1, $this->repository->countBy('foo ~ "ble"'));
    }

    /**
     * Tests find with empty id.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testFindWithEmptyId()
    {
        $this->repository->find(null);
    }

    /**
     * Tests find for an empty entity in cache.
     *
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testFindForUnexistingEntity()
    {
        $this->repository->find('quux');
    }

    /**
     * Tests find with valid entity.
     */
    public function testFind()
    {
        $entity = $this->repository->find(['foo' => 'mumble']);

        $this->assertEquals([ 'foo' => 'mumble', 'bar' => 'flob' ], $entity->getData());
    }

    /**
     * Tests findBy.
     */
    public function testFindBy()
    {
        $entities = $this->repository->findBy('bar ~ "flob" order by foo asc');

        $this->assertEquals([ 'foo' => 'mumble', 'bar' => 'flob' ], $entities[0]->getData());
        $this->assertEquals([ 'foo' => 'thud', 'bar' => 'flob' ], $entities[1]->getData());
    }

    /**
     * Tests findOneBy when no entities found in database.
     *
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testFindOneByForEmptyEntity()
    {
        $this->repository->findOneBy('foo = "wobble"');
    }

    /**
     * Tests findOneBy.
     */
    public function testFindOneBy()
    {
        $this->assertNotEmpty($this->repository->findOneBy('bar = "flob"'));
    }

    /**
     * Tests evaluate for property with an array as value.
     */
    public function testEvaluate()
    {
        $entity = new Entity([ 'wibble' => [ 'foo', 'glork' ] ]);

        $method = new \ReflectionMethod($this->repository, 'evaluate');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->repository, [ $entity, 'isEquals', 'wibble', 'foo' ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ $entity, 'isEquals', 'wibble', 'fubar' ]));
    }

    /**
     * Tests isEquals for equal and non-equal values.
     */
    public function testIsEquals()
    {
        $method = new \ReflectionMethod($this->repository, 'isEquals');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->repository, [ 'foo', 'foo' ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 'foo', 'bar' ]));
    }

    /**
     * Tests isGreat for greater and non-greater values.
     */
    public function testIsGreat()
    {
        $method = new \ReflectionMethod($this->repository, 'isGreat');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->repository, [ 1, 4 ]));
        $this->assertTrue($method->invokeArgs($this->repository, [ 'foo', 'thud' ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 3, 2 ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 'wubble', 'wubble' ]));
    }

    /**
     * Tests isGreatEquals for equal values.
     */
    public function testIsGreatEquals()
    {
        $method = new \ReflectionMethod($this->repository, 'isGreatEquals');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->repository, [ 1, 1 ]));
        $this->assertTrue($method->invokeArgs($this->repository, [ 'foo', 'foo' ]));
    }

    /**
     * Tests isInArray for values in and not in the array.
     */
    public function testIsInArray()
    {
        $method = new \ReflectionMethod($this->repository, 'isInArray');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->repository, [ 1, [ 1, 2 ] ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 1, null ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 1, '' ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 2, [ 1 ] ]));
    }

    /**
     * Tests isLess for lesser and non-lesser values.
     */
    public function testIsLess()
    {
        $method = new \ReflectionMethod($this->repository, 'isLess');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->repository, [ 4, 2 ]));
        $this->assertTrue($method->invokeArgs($this->repository, [ 'thud', 'foo' ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 1, 3 ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 'wubble', 'wubble' ]));
    }

    /**
     * Tests isLessEquals for equal values.
     */
    public function testIsLessEquals()
    {
        $method = new \ReflectionMethod($this->repository, 'isLessEquals');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->repository, [ 1, 1 ]));
        $this->assertTrue($method->invokeArgs($this->repository, [ 'foo', 'foo' ]));
    }

    /**
     * Tests isLike for inside and non-inside values.
     */
    public function testIsLike()
    {
        $method = new \ReflectionMethod($this->repository, 'isLike');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->repository, [ 'foo', 'fo' ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 'foo', 'bar' ]));
    }

    /**
     * Tests isNotEquals for equal and non-equal values.
     */
    public function testIsNotEquals()
    {
        $method = new \ReflectionMethod($this->repository, 'isNotEquals');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->repository, [ 'foo', 'bar' ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 'foo', 'foo' ]));
    }

    /**
     * Tests isNotInArray for values in and not in the array.
     */
    public function testIsNotInArray()
    {
        $method = new \ReflectionMethod($this->repository, 'isNotInArray');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->repository, [ 1, [ 1, 2 ] ]));
        $this->assertTrue($method->invokeArgs($this->repository, [ 1, null ]));
        $this->assertTrue($method->invokeArgs($this->repository, [ 1, '' ]));
        $this->assertTrue($method->invokeArgs($this->repository, [ 2, [ 1 ] ]));
    }

    /**
     * Tests isNotLike for inside and non-inside values.
     */
    public function testIsNotLike()
    {
        $method = new \ReflectionMethod($this->repository, 'isNotLike');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->repository, [ 'foo', 'bar' ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 'foo', 'fo' ]));
    }

    /**
     * Tests match for values that match and don't match the pattern.
     */
    public function testMatch()
    {
        $method = new \ReflectionMethod($this->repository, 'match');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->repository, [ 'foo', 'oo$' ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 'foo', '^b' ]));
    }

    /**
     * Tests notMatch for values that match and don't match the pattern.
     */
    public function testNotMatch()
    {
        $method = new \ReflectionMethod($this->repository, 'notMatch');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->repository, [ 'foo', '^b' ]));
        $this->assertFalse($method->invokeArgs($this->repository, [ 'foo', 'oo$' ]));
    }
}
