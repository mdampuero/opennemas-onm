<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\Common\ORM\Database\Persister;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;
use Common\ORM\Database\Persister\BasePersister;

class BasePersisterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'delete', 'executeQuery', 'insert', 'lastInsertId', 'update' ])
            ->getMock();

        $this->metadata = new Metadata([
            'name' => 'Foobar',
            'properties' => [
                'foo'    => 'integer',
                'bar'    => 'string',
                'wibble' => 'string',
            ],
            'mapping' => [
                'database' => [
                    'id' => 'foo',
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

        $this->cache = $this->getMockBuilder('Common\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $this->persister = new BasePersister($this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests create for an entity with one meta.
     */
    public function testCreate()
    {
        $entity = new Entity([ 'bar' => 'fubar', 'wibble' => 'xyzzy', 'mumble' => null ]);

        $this->conn->expects($this->once())->method('lastInsertId')->willReturn(1);
        $this->conn->expects($this->once())->method('insert')->with(
            'foobar',
            [ 'foo' => null, 'bar' => 'fubar' ],
            [ 'foo' => \PDO::PARAM_STR, 'bar' => \PDO::PARAM_STR ]
        );
        $this->conn->expects($this->at(2))->method('executeQuery')->with(
            'replace into foobar_meta values (?,?,?)',
            [ 1, 'wibble', 'xyzzy' ],
            [ \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR ]
        );
        $this->conn->expects($this->at(3))->method('executeQuery')->with(
            'delete from foobar_meta where foobar_foo = ? and meta_key in (?)',
            [ 1, [ 'mumble' ] ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        $this->persister->create($entity);
        $this->assertEquals(1, $entity->foo);
    }

    /**
     * Tests remove.
     */
    public function testRemove()
    {
        $entity = new Entity([ 'foo' => 1, 'bar' => 'fubar' ]);

        $this->conn->expects($this->once())->method('delete')
            ->with('foobar', [ 'foo' => 1 ]);
        $this->cache->expects($this->once())->method('remove');

        $this->persister->remove($entity);
    }

    /**
     * Tests update for an entity with one meta.
     */
    public function testUpdate()
    {
        $entity = new Entity([
            'foo'    => 1,
            'bar'    => 'garply',
            'wibble' => 'xyzzy',
        ]);

        $this->conn->expects($this->once())->method('update')->with(
            'foobar',
            [ 'bar' => 'garply' ],
            [ 'foo' => 1 ],
            [ 'bar' => \PDO::PARAM_STR ]
        );
        $this->conn->expects($this->at(1))->method('executeQuery')->with(
            'replace into foobar_meta values (?,?,?)',
            [ 1, 'wibble', 'xyzzy' ],
            [ \PDO::PARAM_INT, \PDO::PARAM_STR, \PDO::PARAM_STR ]
        );

        $this->cache->expects($this->once())->method('remove');
        $this->persister->update($entity);
    }

    /**
     * Tests update for an entity with one meta.
     */
    public function testUpdateWithNoChanges()
    {
        $entity = new Entity([
            'foo'    => 1,
            'bar'    => 'garply',
            'wibble' => 'xyzzy',
        ]);

        $entity->refresh();

        $this->conn->expects($this->never())->method($this->anything());
        $this->cache->expects($this->never())->method($this->anything());
        $this->persister->update($entity);
    }

    /**
     * Tests saveMetas with empty metas.
     */
    public function testSaveMetas()
    {
        $method = new \ReflectionMethod($this->persister, 'saveMetas');
        $method->setAccessible(true);

        $method->invokeArgs($this->persister, [ [ 'foo' => 1 ], [] ]);
    }

    /**
     * Tests hasCache method for persisters with and without cache.
     */
    public function testHasCache()
    {
        $method = new \ReflectionMethod($this->persister, 'hasCache');
        $method->setAccessible(true);
        $this->assertTrue($method->invokeArgs($this->persister, []));

        $persister = new BasePersister($this->conn, $this->metadata);
        $method    = new \ReflectionMethod($persister, 'hasCache');
        $method->setAccessible(true);
        $this->assertFalse($method->invokeArgs($persister, []));
    }
}
