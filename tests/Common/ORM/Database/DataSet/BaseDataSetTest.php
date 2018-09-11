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

class BaseDataSetTest extends \PHPUnit\Framework\TestCase
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
        $this->cache->expects($this->exactly(2))->method('remove')->with([ 'foo' ]);
        $this->conn->expects($this->exactly(2))->method('executeQuery')->with(
            'delete from foobar where name in (?)',
            [ [ 'foo' ] ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        $dataset = new BaseDataSet($this->conn, $this->metadata, $this->cache);

        $dataset->delete([]);
        $dataset->delete(['foo']);
        $dataset->delete('foo');

        $this->addToAssertionCount(1);
    }

    /**
     * Tests get method when all values are in cache.
     */
    public function testGetFromCache()
    {
        $this->cache->expects($this->any())->method('get')
            ->willReturn([ 'foo' => 'bar' ]);
        $this->conn->expects($this->any())->method('fetchAll')
            ->willReturn([ [ 'name' => 'fubar', 'value' => serialize('thud') ] ]);

        $dataset = new BaseDataSet($this->conn, $this->metadata, $this->cache);

        $this->assertEquals('bar', $dataset->get('foo'));
        $this->assertEquals('bar', $dataset->get('foo', 'wubble'));
        $this->assertEquals([ 'foo' => 'bar' ], $dataset->get([ 'foo' ]));
        $this->assertEquals([ 'foo' => 'bar' ], $dataset->get([ 'foo' ], [ 'wubble' ]));
    }

    /**
     * Tests get method when all values are in database.
     */
    public function testGetFromDatabase()
    {
        $this->cache->expects($this->any())->method('get')->willReturn([]);
        $this->conn->expects($this->any())->method('fetchAll')
            ->with('select * from foobar where name in (?)', [ [ 'qux' ] ], [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ])
            ->willReturn([ [ 'name' => 'qux', 'value' => serialize('norf') ] ]);

        $dataset = new BaseDataSet($this->conn, $this->metadata, $this->cache);

        $this->assertEquals('norf', $dataset->get('qux'));
        $this->assertEquals('norf', $dataset->get('qux', 'grault'));
        $this->assertEquals([ 'qux' => 'norf' ], $dataset->get(['qux']));
        $this->assertEquals([ 'qux' => 'norf' ], $dataset->get(['qux'], ['grault']));
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
        $this->cache->expects($this->any())->method('remove')->with('foo');
        $this->conn->expects($this->any())->method('executeQuery')
            ->with(
                'insert into foobar (name, value) values (?,?) on duplicate key update value = ?',
                [ 'foo', serialize('bar'), serialize('bar') ],
                [ \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR ]
            );

        $dataset = new BaseDataSet($this->conn, $this->metadata, $this->cache);

        $dataset->set([]);
        $dataset->set('foo', 'bar');
        $dataset->set([ 'foo' => 'bar' ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests autoload method when all data are in cache.
     */
    public function testAutoloadFromCache()
    {
        $this->cache->expects($this->any())->method('get')
            ->willReturn([ 'foo' => 'bar' ]);

        $dataset = new BaseDataSet($this->conn, $this->metadata, $this->cache, [ 'foo' ]);

        $property = new \ReflectionProperty($dataset, 'autoloaded');
        $property->setAccessible(true);

        $this->assertEquals([ 'foo' => 'bar' ], $property->getValue($dataset));
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

        $property = new \ReflectionProperty($dataset, 'autoloaded');
        $property->setAccessible(true);

        $this->assertEquals([ 'foo' => 'bar' ], $property->getValue($dataset));
    }

    /**
     * Tests hasCache method for persisters with and without cache.
     */
    public function testHasCache()
    {
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
