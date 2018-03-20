<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\Common\ORM\Database\Repository;

use Common\ORM\Core\Metadata;
use Common\ORM\Database\Repository\BaseRepository;

class BaseRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetchAll', 'fetchArray' ])
            ->getMock();

        $this->metadata = new Metadata([
            'name' => 'Extension',
            'properties' => [
                'foo'    => 'integer',
                'bar'    => 'string',
                'wibble' => 'string',
                'norf'   => 'array::norf_id=>extension_id:integer;norf_id:integer'
            ],
            'mapping' => [
                'database' => [
                    'table' => 'foobar',
                    'id'    => 'foo',
                    'metas' => [
                        'foo' => 'flob'
                    ],
                    'relations' => [
                        'norf' => [
                            'table'   => 'extension_norf',
                            'ids'     => [ 'foo' => 'foo_id' ],
                            'columns' => [
                                'extension_id' => [
                                    'type'    => 'integer',
                                    'options' => [ 'default' => null, 'unsigned' => true ]
                                ],
                                'norf_id' => [
                                    'type'    => 'integer',
                                    'options' => [ 'default' => null, 'unsigned' => true ]
                                ]
                            ]
                        ]
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

        $this->cache = $this->getMockBuilder('Common\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'exists', 'get', 'set' ])
            ->getMock();

        $this->repository = new BaseRepository('foo', $this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests countBy with valid and invalid results.
     */
    public function testCountBy()
    {
        $this->conn->expects($this->at(0))->method('fetchArray')->willReturn([ 10 ]);
        $this->conn->expects($this->at(1))->method('fetchArray')->willReturn(false);

        $this->assertEquals(10, $this->repository->countBy('foo ~ "bar"'));
        $this->assertFalse($this->repository->countBy('foo ~ "bar"'));
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
    public function testFindForEmptyEntityInCache()
    {
        $this->cache->expects($this->once())->method('exists')->willReturn(true);
        $this->cache->expects($this->once())->method('get')->willReturn('-miss-');
        $this->repository->find(1);
    }

    /**
     * Tests find for an empty entity in cache.
     *
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testFindForEmptyEntityInDatabase()
    {
        $this->cache->expects($this->once())->method('exists')->willReturn(false);
        $this->cache->expects($this->once())->method('set');
        $this->conn->expects($this->once())->method('fetchAll')->willReturn([]);

        $this->repository->find(1);
    }

    /**
     * Tests find with valid entity.
     */
    public function testFind()
    {
        $this->cache->expects($this->once())->method('exists')->willReturn(false);
        $this->cache->expects($this->once())->method('set');
        $this->conn->expects($this->at(0))->method('fetchAll')->willReturn([
            [ 'foo' => 1, 'bar' => 'glork' ]
        ]);
        $this->conn->expects($this->at(1))->method('fetchAll')->willReturn([
            [ 'foobar_foo' => 1, 'meta_key' => 'wibble', 'meta_value' => 'qux' ]
        ]);
        $this->conn->expects($this->at(2))->method('fetchAll')
            ->with('select * from extension_norf where foo_id in (1)')
            ->willReturn([
                [ 'foo_id' => 1, 'norf_id' => 3 ],
            ]);

        $entity = $this->repository->find(1);

        $this->assertNotEmpty($entity);
        $this->assertEquals([
            'foo'    => 1,
            'bar'    => 'glork',
            'wibble' => 'qux',
            'norf'   => [ 3 => [ 'norf_id' => 3 ] ]
        ], $entity->getData());
    }

    /**
     * Tests findBy.
     */
    public function testFindBy()
    {
        $this->cache->expects($this->once())->method('get')->willReturn([]);
        $this->cache->expects($this->any())->method('set');
        $this->conn->expects($this->at(0))->method('fetchAll')->willReturn([
            [ 'foo' => 1 ],  ['foo' => 2 ]
        ]);
        $this->conn->expects($this->at(1))->method('fetchAll')
            ->with('select * from foobar where foo in ( ? , ? )', [ 1, 2 ], [ 1, 1 ])
            ->willReturn([
                [ 'foo' => 1, 'bar' => 'glork' ],
                [ 'foo' => 2, 'bar' => 'thud' ]
            ]);
        $this->conn->expects($this->at(2))->method('fetchAll')
            ->with('select * from foobar_meta where foobar_foo in (1,2)')
            ->willReturn([
                [ 'foobar_foo' => 1, 'meta_key' => 'wibble', 'meta_value' => 'qux' ],
                [ 'foobar_foo' => 2, 'meta_key' => 'wibble', 'meta_value' => 'glork' ]
            ]);
        $this->conn->expects($this->at(3))->method('fetchAll')
            ->with('select * from extension_norf where foo_id in (1,2)')
            ->willReturn([
                [ 'foo_id' => 1, 'norf_id' => 3 ],
                [ 'foo_id' => 2, 'norf_id' => 5 ]
            ]);

        $entities = $this->repository->findBy('foo in [1,2] limit 10');

        $this->assertEquals([
            'foo'    => 1,
            'bar'    => 'glork',
            'wibble' => 'qux',
            'norf'   => [
                3 => [ 'norf_id' => 3 ],
            ]
        ], $entities[0]->getData());

        $this->assertEquals([
            'foo'    => 2,
            'bar'    => 'thud',
            'wibble' => 'glork',
            'norf'   => [
                5 => [ 'norf_id' => 5 ],
            ]
        ], $entities[1]->getData());
    }

    /**
     * Tests findBySql.
     */
    public function testFindBySql()
    {
        $this->cache->expects($this->once())->method('get')->willReturn([
            'foo-1' => '-miss-'
        ]);
        $this->cache->expects($this->any())->method('set');
        $this->conn->expects($this->at(0))->method('fetchAll')
            ->with('select foo from corge limit 10')
            ->willReturn([
                [ 'foo' => 1 ],  ['foo' => 2 ]
            ]);
        $this->conn->expects($this->at(1))->method('fetchAll')->willReturn([
            [ 'foo' => 1, 'bar' => 'glork' ],
            [ 'foo' => 2, 'bar' => 'thud' ]
        ]);
        $this->conn->expects($this->at(2))->method('fetchAll')->willReturn([
            [ 'foobar_foo' => 1, 'meta_key' => 'wibble', 'meta_value' => 'qux' ],
            [ 'foobar_foo' => 2, 'meta_key' => 'wibble', 'meta_value' => 'glork' ]
        ]);
        $this->conn->expects($this->at(3))->method('fetchAll')
            ->with('select * from extension_norf where foo_id in (1,2)')
            ->willReturn([
                [ 'foo_id' => 1, 'norf_id' => 3 ],
                [ 'foo_id' => 2, 'norf_id' => 5 ]
            ]);

        $entities = $this->repository->findBySql('select foo from corge limit 10');

        $this->assertEquals([
            'foo'    => 1,
            'bar'    => 'glork',
            'wibble' => 'qux',
            'norf'   => [
                3 => [ 'norf_id' => 3 ],
            ]
        ], $entities[0]->getData());

        $this->assertEquals([
            'foo'    => 2,
            'bar'    => 'thud',
            'wibble' => 'glork',
            'norf'   => [
                5 => [ 'norf_id' => 5 ],
            ]
        ], $entities[1]->getData());
    }

    /**
     * Tests findOneBy when no entities found in database.
     *
     * @expectedException Common\ORM\Core\Exception\EntityNotFoundException
     */
    public function testFindOneByForEmptyEntity()
    {
        $this->cache->expects($this->once())->method('get')->willReturn([]);
        $this->cache->expects($this->any())->method('set');
        $this->conn->expects($this->at(0))->method('fetchAll')->willReturn([]);

        $this->repository->findOneBy();
    }

    /**
     * Tests findOneBy.
     */
    public function testFindOne()
    {
        $this->cache->expects($this->once())->method('get')->willReturn([]);
        $this->cache->expects($this->any())->method('set');
        $this->conn->expects($this->at(0))->method('fetchAll')->willReturn([
            [ 'foo' => 1 ]
        ]);
        $this->conn->expects($this->at(1))->method('fetchAll')->willReturn([
            [ 'foo' => 1, 'bar' => 'glork' ],
        ]);
        $this->conn->expects($this->at(2))->method('fetchAll')->willReturn([
            [ 'foobar_foo' => 1, 'meta_key' => 'wibble', 'meta_value' => 'qux' ],
        ]);
        $this->conn->expects($this->at(3))->method('fetchAll')
            ->with('select * from extension_norf where foo_id in (1)')
            ->willReturn([ [ 'foo_id' => 1, 'norf_id' => 3 ] ]);

        $this->cache->expects($this->once())->method('get')->willReturn([]);
        $this->cache->expects($this->any())->method('set');
        $this->conn->expects($this->at(0))->method('fetchAll')->willReturn([]);

        $entity = $this->repository->findOneBy('limit 1');

        $this->assertNotEmpty($entity);
    }

    /**
     * Tests hasCache method for repositories with and without cache.
     */
    public function testHasCache()
    {
        $method = new \ReflectionMethod($this->repository, 'hasCache');
        $method->setAccessible(true);
        $this->assertTrue($method->invokeArgs($this->repository, []));

        $repository = new Baserepository('foo', $this->conn, $this->metadata);
        $method     = new \ReflectionMethod($repository, 'hasCache');
        $method->setAccessible(true);
        $this->assertFalse($method->invokeArgs($repository, []));
    }
}
