<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\Common\ORM\Database\DataSet;

use Common\ORM\Core\Metadata;
use Common\ORM\Database\DataSet\BaseDataSet;

class BaseDataSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'executeQuery', 'fetchAll' ])
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
            ->setMethods([ 'remove', 'get', 'set' ])
            ->getMock();
    }

    /**
     * Tests delete method.
     */
    public function testDelete()
    {
        $this->cache->expects($this->once())->method('get')
            ->willReturn([ 'foo' => 'bar', 'wibble' => 'glorp' ]);

        $this->cache->expects($this->at(1))->method('set')
            ->with('foobar', [ 'foo' => 'bar' ]);
        $this->cache->expects($this->at(2))->method('set')
            ->with('foobar', []);

        $this->conn->expects($this->at(0))->method('executeQuery')->with(
            'delete from foobar where name in (?)',
            [ [ 'wibble' ] ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );
        $this->conn->expects($this->at(1))->method('executeQuery')->with(
            'delete from foobar where name in (?)',
            [ [ 'foo' ] ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );


        $dataset = new BaseDataSet($this->conn, $this->metadata, $this->cache);

        $dataset->delete([]);
        $dataset->delete(['plugh']);

        $dataset->delete(['wibble']);
        $this->assertEquals('bar', $dataset->get('foo'));
        $this->assertEmpty($dataset->get('wibble'));
        $dataset->delete('foo');
    }

    /**
     * Tests get method when all values are in cache.
     */
    public function testGet()
    {
        $this->cache->expects($this->once())->method('get')
            ->willReturn([ 'foo' => 'bar' ]);

        $dataset = new BaseDataSet($this->conn, $this->metadata, $this->cache);

        $this->assertEquals('bar', $dataset->get('foo'));
        $this->assertEmpty($dataset->get('mumble'));
        $this->assertEquals('flob', $dataset->get('mumble', 'flob'));

        $this->assertEquals(
            [ 'mumble' => 'flob' ],
            $dataset->get([ 'mumble' ], [ 'flob' ])
        );

        $this->assertEquals(
            [ 'mumble' => 'flob', 'gorp' => 'flob' ],
            $dataset->get([ 'mumble', 'gorp' ], 'flob')
        );

        $this->assertEquals(
            [ 'foo' => 'bar', 'gorp' => 'flob' ],
            $dataset->get([ 'foo', 'gorp' ], 'flob')
        );
    }

    /**
     * Tests get method when the values are not in cache nor database.
     */
    public function testGetWithDefaultValues()
    {
        $this->cache->expects($this->any())->method('get')->willReturn([]);
        $this->conn->expects($this->any())->method('fetchAll')->willReturn([]);

        $dataset = new BaseDataSet($this->conn, $this->metadata, $this->cache);

        $this->assertEquals('glork', $dataset->get('wibble', 'glork'));
        $this->assertEquals([ 'wibble' => 'glork' ], $dataset->get([ 'wibble' ], [ 'glork' ]));
        $this->assertEmpty($dataset->get('wibble'));
        $this->assertEquals([ 'wibble' => null ], $dataset->get(['wibble']));
    }

    /**
     * Tests set method.
     */
    public function testSet()
    {
        $this->cache->expects($this->once())->method('get')
            ->willReturn([ 'foo' => 'bar', 'wibble' => 'glorp' ]);

        $this->cache->expects($this->exactly(3))->method('set')->with('foobar');

        $this->conn->expects($this->at(0))->method('executeQuery')
            ->with(
                'insert into foobar (name, value) values (?,?) on duplicate key update value = values(value)',
                [ 'foo', serialize('bar') ],
                [ \PDO::PARAM_STR, \PDO::PARAM_STR ]
            );
        $this->conn->expects($this->at(1))->method('executeQuery')
            ->with(
                'insert into foobar (name, value) values (?,?),(?,?) on duplicate key update value = values(value)',
                [ 'foo', serialize('bar'), 'baz', serialize('thud') ],
                [ \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR ]
            );
        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with(
                'delete from foobar where name in (?)',
                [ [ 'wibble' ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );
        $this->conn->expects($this->at(3))->method('executeQuery')
            ->with(
                'delete from foobar where name in (?)',
                [ [ 'baz' ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );


        $dataset = new BaseDataSet($this->conn, $this->metadata, $this->cache);

        $dataset->set([]);
        $dataset->set('foo', 'bar');
        $dataset->set([ 'foo' => 'bar', 'wibble' => null, 'baz' => 'thud' ]);
        $dataset->set('baz', null);
    }

    /**
     * Tests autoload method when all data are in cache.
     */
    public function testAutoloadFromCache()
    {
        $this->cache->expects($this->any())->method('get')
            ->willReturn([ 'foo' => 'bar' ]);

        $dataset = new BaseDataSet($this->conn, $this->metadata, $this->cache, [ 'foo' ]);

        $property = new \ReflectionProperty($dataset, 'data');
        $property->setAccessible(true);

        $method = new \ReflectionMethod($dataset, 'autoload');
        $method->setAccessible(true);

        $this->assertEquals([ 'foo' => 'bar' ], $property->getValue($dataset));

        $method->invokeArgs($dataset, []);
    }

    /**
     * Tests autoload method when some data are not in cache.
     */
    public function testAutoloadFromDatabase()
    {
        $this->cache->expects($this->any())->method('get')->willReturn([]);
        $this->conn->expects($this->any())->method('fetchAll')
            ->willReturn([ [ 'name' => 'foo', 'value' => serialize('bar') ] ]);

        $dataset = new BaseDataSet($this->conn, $this->metadata, $this->cache, [ 'foo' ]);

        $property = new \ReflectionProperty($dataset, 'data');
        $property->setAccessible(true);

        $this->assertEquals([ 'foo' => 'bar' ], $property->getValue($dataset));
    }

    /**
     * Tests hasCache method for persisters with and without cache.
     */
    public function testHasCache()
    {
        $this->conn->expects($this->any())->method('fetchAll')
            ->willReturn([]);

        $dataset = new BaseDataSet($this->conn, $this->metadata, $this->cache);
        $method  = new \ReflectionMethod($dataset, 'hasCache');
        $method->setAccessible(true);
        $this->assertTrue($method->invokeArgs($dataset, []));

        $dataset = new BaseDataSet($this->conn, $this->metadata);
        $method  = new \ReflectionMethod($dataset, 'hasCache');
        $method->setAccessible(true);
        $this->assertFalse($method->invokeArgs($dataset, []));
    }
}
